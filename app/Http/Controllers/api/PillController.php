<?php

namespace App\Http\Controllers\api;

use App\Helpers\MyTokenManager;
use App\Http\Controllers\Controller;
use App\Http\Requests\ImageDetectionRequest;
use App\Http\Requests\InteractionRequest;
use App\Http\Resources\PillInteractionResource;
use App\Http\Resources\PillResource;
use App\Models\api\Pill;
use App\Models\PillInteraction;
use App\Models\UserInteractions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class PillController extends Controller
{
    public function detection(ImageDetectionRequest $request)
    {
        $img = $request->file('img');
        $response = Http::attach(
            'img',
            file_get_contents($img->path()),
            $img->getClientOriginalName()
        )->post('http://127.0.0.1:5000/detect');
        if ($response->successful()) {
            $data = $response->json();
            $pill = Pill::where('name', $data['Class Name'])->first();

            if ($pill) {
                return new  PillResource($pill);
            } else {
                return response()->json(['errorMessage' => 'Pill not found'], 404);
            }
        } else {
            return response()->json(['errorMessage' => 'Error processing the image detection request, try again later.'], $response->status());
        }
    }

    public function interactionIndex()
    {
        $pills = Pill::select('name')->get();
        if ($pills->isEmpty()) {
            return response()->json(['message' => 'No pills found.'], 404);
        }
        return response()->json([
            'message' => 'get Pills list successfully',
            'data' => $pills,
        ], 200);
    }

    public function interaction(InteractionRequest $request)
    {
        $pill_1_id = Pill::where('name', $request->input('pillName_1'))->value('id');
        $pill_2_id = Pill::where('name', $request->input('pillName_2'))->value('id');

        $pillInteractionData = PillInteraction::whereIn('pill_1_id', [$pill_1_id, $pill_2_id])
            ->whereIn('pill_2_id', [$pill_1_id, $pill_2_id])
            ->get();


        if ($pillInteractionData) {
            $user = MyTokenManager::currentUser($request);
            UserInteractions::create([
                'interaction_id' => $pillInteractionData[0]->id,
                'user_id' => $user->id,
            ]);
            return PillInteractionResource::collection($pillInteractionData);
        } else {
            return response()->json(['errorMessage' => 'Pill Interaction Data not found'], 404);
        }
    }

    public function imageInteraction(Request $request)
    {
        $images = [$request->file('img1'), $request->file('img2')];
        foreach ($images as $img) {
            $responses[] = Http::attach(
                'img',
                file_get_contents($img->path()),
                $img->getClientOriginalName()
            )->post('http://127.0.0.1:5000/detect');
        }
        $allSuccessful = collect($responses)->every(function ($response) {
            return $response->successful();
        });

        if ($allSuccessful) {
            $detect_pill_1 = $responses[0]->json();
            $detect_pill_2 = $responses[1]->json();
            $pills_id = Pill::select('id')->whereIn('name', [$detect_pill_1['Class Name'], $detect_pill_2['Class Name']])->get();

            $interactions = PillInteraction::where('pill_1_id', $pills_id[0]->id)
                ->where('pill_2_id', $pills_id[1]->id)
                ->get();
            if ($interactions) {
                $user = MyTokenManager::currentUser($request);
                UserInteractions::create([
                    'interaction_id' => $interactions[0]->id, 'user_id' => $user->id,
                ]);
                return PillInteractionResource::collection($interactions);
            } else {
                return response()->json(['errorMessage' => 'Pill Interaction Data not found'], 404);
            }
        } else {
            return response()->json(['error' => 'Can not detect your image']);
        }
    }
    public function PillInteractionUserHistory(Request $request)
    {
        $user = MyTokenManager::currentUser($request);
        $userHistory = DB::select('select * from user_interactions where user_id = ?', [$user->id]);
        $userHistoryData = [];
        foreach ($userHistory as $history) {
            $interaction = DB::select('select * from pill_interactions where id = ?', [$history->interaction_id]);
            if (!empty($interaction)) {
                $firstPillData = Pill::where('id', $interaction[0]->pill_1_id)->first();
                $secondPillData = Pill::where('id', $interaction[0]->pill_2_id)->first();

                if ($firstPillData && $secondPillData) {
                    $userHistoryData[] = [
                        'firstPillData' => $firstPillData,
                        'secondPillData' => $secondPillData,
                        'interactionData' => $interaction[0],
                    ];
                }
            }
        }

        if (!empty($userHistoryData)) {
            return response()->json([
                'message' => 'Pill history data retrieved successfully',
                'userHistoryData' => $userHistoryData,
            ], 200);
        } else {
            return response()->json(['errorMessage' => 'Pill Interaction Data not found'], 404);
        }
    }
    public function ShowPillInteractionUserHistory(Request $request)
    {
        $interaction_id = $request->input('interaction_id');
        if (!$interaction_id) {
            return response()->json(['error' => 'Pill interaction not provided'], 400);
        }
        $pillInteractionData = DB::select('select * from pill_interactions where id=? ', [$interaction_id]);
        $firstPillData = Pill::where('id', $pillInteractionData[0]->pill_1_id)->first();
        $secondPillData = Pill::where('id', $pillInteractionData[0]->pill_2_id)->first();
        if ($pillInteractionData and $firstPillData and $secondPillData) {
            return response()->json([
                'message' => 'Pill Intearction data retrieved successfully',
                'firstPillData' => $firstPillData,
                'secondPillData' => $secondPillData,
                'pillInteractionData' => $pillInteractionData,
            ], 200);
        } else {
            return response()->json(['errorMessage' => 'Pill Interaction Data not found'], 404);
        }
    }
}
