<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\api\Pill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PillController extends Controller
{
    public function pillDetectionData(Request $request)
    {
        $name = $request->query('name');
        if (!$name) {
            return response()->json(['error' => 'Pill name not provided'], 400);
        }
        $Pilldata = Pill::where('name', $name)->first();
        $pilldosagedata = DB::select('select * from pill_dosages where pill_id=?', [$Pilldata->id]);
        $contraindiacationsdata = DB::select('select * from contraindiacations where pill_id=?', [$Pilldata->id]);

        $side_effectsdata = DB::select('select * from side_effects where pill_id=?', [$Pilldata->id]);

        if ($Pilldata && $pilldosagedata) {
            return response()->json([
                'message' => 'Pill data retrieved successfully',
                "statusCode" => 200,
                'pillData' => $Pilldata,
                'pilldosagedata' => $pilldosagedata,
                'contraindiacationsdata' => $contraindiacationsdata,
                'side_effectsdata' => $side_effectsdata
            ], 200);
        } else {
            return response()->json(['errorMessage' => 'Pill not found'], 404);
        }
    }
}
