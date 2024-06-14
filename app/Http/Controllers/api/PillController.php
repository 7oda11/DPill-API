<?php

namespace App\Http\Controllers\api;

use App\Helpers\MyTokenManager;
use App\Http\Controllers\Controller;
use App\Http\Requests\ImageDetectionRequest;
use App\Http\Requests\ImageInteractionRequest;
use App\Http\Requests\InteractionRequest;
use App\Http\Resources\PillInteractionResource;
use App\Http\Resources\PillResource;
use App\Http\Resources\UserPillDetectionResource;
use App\Http\Resources\UserPillInteractionsResource;
use App\Models\api\Pill;
use App\Models\PillInteraction;
use App\Models\UserInteractions;
use App\Models\UserPhotos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

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
                $imagePath = $img->store('images/userPillsPhotos', 'public');
                $user = MyTokenManager::currentUser($request);
                UserPhotos::create([
                    'path' => $imagePath,
                    'user_id' => $user->id,
                    'pill_id' => $pill->id
                ]);
                return new  PillResource($pill);
            } else {
                return response()->json([
                    'errorMessage' => $data['Class Name'] . ' ' . 'Pill have not additional information',
                    "statusCode" => 404
                ], 404);
            }
        } else {
            return response()->json([
                'errorMessage' => 'Error processing the image detection request, try again later.',
                "statusCode" => $response->status()
            ], $response->status());
        }
    }

    public function interactionIndex()
    {
        $pills = Pill::select('name')->get();
        if ($pills->isEmpty()) {
            return response()->json(['message' => 'No pills found in the database..'], 404);
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


        if ($pillInteractionData->isNotEmpty()) {
            $user = MyTokenManager::currentUser($request);
            UserInteractions::create([
                'interaction_id' => $pillInteractionData[0]->id,
                'user_id' => $user->id,
            ]);
            return PillInteractionResource::collection($pillInteractionData);
        } else {
            return response()->json([
                'errorMessage' => 'Pill Interaction Data not found',
                "statusCode" => 404,
            ], 404);
        }
    }

    public function imageInteraction(ImageInteractionRequest $request)
    {
        $images = [$request->file('img1'), $request->file('img2')];
        $responses = [];
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

            $pills_id = Pill::select('id')
                ->whereIn('name', [$detect_pill_1['Class Name'], $detect_pill_2['Class Name']])
                ->get();

            // Check if $pills_id has exactly 2 elements
            if ($pills_id->count() === 2) {
                $pillInteractionData = PillInteraction::whereIn('pill_1_id', [$pills_id[0]->id, $pills_id[1]->id])
                    ->whereIn('pill_2_id', [$pills_id[0]->id, $pills_id[1]->id])
                    ->get();

                if ($pillInteractionData->isNotEmpty()) {
                    $user = MyTokenManager::currentUser($request);
                    UserInteractions::create([
                        'interaction_id' => $pillInteractionData[0]->id,
                        'user_id' => $user->id,
                    ]);

                    return PillInteractionResource::collection($pillInteractionData);
                } else {
                    return response()->json([
                        'errorMessage' => 'No interactions have been found between those pills yet.',
                        "statusCode" => 404,
                    ], 404);
                }
            } else {
                return response()->json([
                    'errorMessage' => 'One or both pills not found in the database.',
                    "statusCode" => 404,
                ], 404);
            }
        } else {
            return response()->json([
                'errorMessage' => 'Can not detect your image',
                "statusCode" => 404,
            ], 404);
        }
    }

    //////////////////////////////////////////Interaction History////////////////////////////////////////
    public function PillInteractionUserHistory(Request $request)
    {
        $user = MyTokenManager::currentUser($request);
        $userInteractionsHistory = UserInteractions::where('user_id', $user->id)->get();

        if ($userInteractionsHistory) {

            return UserPillInteractionsResource::collection($userInteractionsHistory);
        } else {
            return response()->json([
                'errorMessage' => 'No Pills Interactions in your History Yet',
                "statusCode" => 404,
            ], 404);
        }
    }

    public function ShowPillInteractionUserHistory($id)
    {
        $interaction = PillInteraction::find($id);
        if (!$interaction) {
            return response()->json([
                'errorMessage' => 'Pill Interaction Data not found',
                "statusCode" => 404,
            ], 404);
        }
        return new PillInteractionResource($interaction);
    }



    public function DeletePillInteractionHistory(Request $request, $id)
    {
        $user = MyTokenManager::currentUser($request);
        $userInteraction = UserInteractions::find($id);
        if (!$userInteraction) {
            return response()->json([
                'errorMessage' => 'Pill Interaction Data not found',
                "statusCode" => 404,
            ], 404);
        }
        if ($user->id !== $userInteraction->user_id) {

            return response()->json([
                'errorMessage' => 'You Are Not Authorized to Delete This History Record',
                "statusCode" => 403,
            ], 403);
        }
        $userInteraction->delete();
        return response()->json(['message' => 'Deleted Pill Interaction Record Successfully'], 200);
    }


    ///////////////////////////Detection History/////////////////////////////////////////

    public function PillDetectionUserHistory(Request $request)
    {
        $user = MyTokenManager::currentUser($request);
        $userDetectionHistory = UserPhotos::where('user_id', $user->id)->get();

        if ($userDetectionHistory) {

            return UserPillDetectionResource::collection($userDetectionHistory);
        } else {
            return response()->json([
                'errorMessage' => 'No Pills Detections in your History Yet',
                "statusCode" => 404,
            ], 404);
        }
    }



    public function ShowPillDetectionUserHistory($id)
    {
        $pill = Pill::find($id);
        if (!$pill) {
            return response()->json([
                'errorMessage' => 'Pill Data not found',
                "statusCode" => 404,
            ], 404);
        }
        return new PillResource($pill);
    }

    public function DeletePillDetectionHistory(Request $request, $id)
    {
        $user = MyTokenManager::currentUser($request);
        $userDetection = UserPhotos::find($id);
        if (!$userDetection) {
            return response()->json(['errorMessage' => 'Pill detection Data not found', "statusCode" => 404], 404);
        }
        if ($user->id !== $userDetection->user_id) {

            return response()->json(['errorMessage' => 'You Are Not Authorized to Delete This History Record', "statusCode" => 403], 403);
        }
        $userDetection->delete();
        return response()->json(['message' => 'Deleted Pill Detection Record Successfully'], 200);
    }
}
