<?php

namespace App\Http\Controllers\api;

use App\Helpers\MyTokenManager;
use App\Http\Controllers\Controller;
use App\Models\api\Pill;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class PillController extends Controller
{
    public function pillDetectionData(Request $request)
    {
        if ($request->hasFile('img')) {
            $img = $request->file('img');
            $response = Http::attach(
                'img',
                file_get_contents($img->path()),
                $img->getClientOriginalName()
            )->post('http://127.0.0.1:5000/detect');
            if ($response->successful()) {
                $data = $response->json();
                $pillData = Pill::where('name', $data['Class Name'])->first();
                if ($pillData) {
                    return response()->json([
                        'message' => 'Pill data retrieved successfully',
                        'pillData' => $pillData,
                    ], 200);
                } else {
                    return response()->json(['errorMessage' => 'Pill not found'], 404);
                }
            } else {
                return response()->json(['error' => 'Error processing the request'], $response->status());
            }
        }
    }
    public function pillDetectionDosageData(Request $request)
    {
        $id = $request->input('id');
        if (!$id) {
            return response()->json(['error' => 'Pill id not provided'], 400);
        }
        $Pilldata = Pill::where('id', $id)->first();
        $pilldosagedata = DB::select('select * from pill_dosages where pill_id=?', [$Pilldata->id]);

        if ($Pilldata && $pilldosagedata) {
            return response()->json([
                'message' => 'Pill data retrieved successfully',
                'pillData'=> $Pilldata,
                'pilldosagedata' => $pilldosagedata,
            ], 200);
        } else {
            return response()->json(['errorMessage' => 'Pill not found'], 404);
        }
    }
    public function pillDetectionContraindiacationsData(Request $request)
    {
        $id = $request->input('id');
        if (!$id) {
            return response()->json(['error' => 'Pill id not provided'], 400);
        }
        $Pilldata = Pill::where('id', $id)->first();
        $contraindiacationsdata = DB::select('select * from contraindiacations where pill_id=?', [$Pilldata->id]);
        if ($Pilldata && $contraindiacationsdata) {
            return response()->json([
                'message' => 'Pill data retrieved successfully',
                'pillData' => $Pilldata,
                'contraindiacationsdata' => $contraindiacationsdata,
            ], 200);
        } else {
            return response()->json(['errorMessage' => 'Pill not found'], 404);
        }
    }
    public function pillDetectionSideEffectsData(Request $request)
    {
        $id = $request->input('id');
        if (!$id) {
            return response()->json(['error' => 'Pill id not provided'], 400);
        }
        $Pilldata = Pill::where('id', $id)->first();
        $side_effectsdata = DB::select('select * from side_effects where pill_id=?', [$Pilldata->id]);
        if ($Pilldata && $side_effectsdata) {
            return response()->json([
                'message' => 'Pill data retrieved successfully',
                'pillData' => $Pilldata,
                'side_effectsdata' => $side_effectsdata
            ], 200);
        } else {
            return response()->json(['errorMessage' => 'Pill not found'], 404);
        }
    }
    public function pillInteractionData(Request $request)
    {
        $firstPill = $request->input('firstPill');
        $secondPill = $request->input('secondPill');
        if (!$firstPill or !$secondPill) {
            return response()->json(['error' => 'Pill name not provided'], 400);
        }
        $firstPillData = Pill::where('name', $firstPill)->first();
        $secondPillData = Pill::where('name', $secondPill)->first();
        if (!$firstPillData or !$secondPillData) {
            return response()->json(['error' => 'Pill name not found'], 400);
        }
        $pillInteractionData = DB::select('select * from pill_interactions where pill_1_id =? and pill_2_id=? ', [$firstPillData->id, $secondPillData->id]);
        $user = MyTokenManager::currentUser($request);
        DB::insert('insert into user_interactions(interaction_id,user_id) values (?,?)', [$pillInteractionData[0]->id, $user->id]);
        if ($pillInteractionData) {
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
