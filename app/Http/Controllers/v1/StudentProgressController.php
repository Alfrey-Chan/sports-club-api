<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\SkillStudentProgress;
use App\Models\TrackEventCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudentProgressController extends Controller
{
    public function myProgress(string $id): JsonResponse
    {
        $categories = TrackEventCategory::all();

        $data = SkillStudentProgress::where('student_id', $id)->get()->groupBy('track_skill_id');
        return response()->json([
            'data' => $data,
        ], 200);
    }
}
