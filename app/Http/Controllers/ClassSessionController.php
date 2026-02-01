<?php

namespace App\Http\Controllers;

use App\Models\ClassSession;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ClassSessionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $classSession = ClassSession::with('enrolments.student')->find($id);

        if (!$classSession) {
            return response()->json([
                'message' => `Class session with id $id not found`,
                'errors' => [],
            ], 404);
        }

        $enrolments = $classSession->enrolments;
        $studentAttendanceItems = $enrolments->map(function ($enrolment) {

            $student = $enrolment->student;

            return [
                'studentId' => $student->id,
                'classEnrolmentId' => $enrolment->id,
                'nameHiragana' => $student->name_hiragana,
            ];
        });

        return response()->json([
            'data' => [
                'classSessionId' => $classSession->id,
                'className' => $classSession->classTemplate->label_ja,
                'dayOfWeek' => $classSession->date->dayOfWeekIso,
                'date' => $classSession->date->toDateString(),
                'startTime' => $classSession->start_time,
                'endTime' => $classSession->end_time,
                'studentAttendanceItems' => $studentAttendanceItems,
            ],
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
