<?php

namespace App\Http\Controllers\v1;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\StudentClassEnrolment;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {   
        $students = Student::query()
            ->select(['id', 'name_hiragana'])
            ->paginate(10);

        return response()->json([
            'data' => $students,
        ], 200);
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
        $studentData = Student::with([
                'user:id,email,phone_number', 
                'classEnrolments.classSession.classTemplate:id,label_ja,day_of_week,start_time,end_time'
            ])->find($id);
        
        $classes = $studentData->classEnrolments->map(function (StudentClassEnrolment $enrolment) {
            $classTemplate = $enrolment->classSession->classTemplate;
            return [
                'enrolmentId' => $enrolment->id,
                'name' => $classTemplate->label_ja,
                'startTime' => $classTemplate->start_time,
                'endTime' => $classTemplate->end_time,
                'dayOfWeek' => $classTemplate->day_of_week,
            ];
        });

        return response()->json([
            'data' => [
                'id' => $studentData->id,
                'email' => $studentData->user->email,
                'phone' => $studentData->user->phone_number,
                'age' => $studentData->date_of_birth->age,
                'nameHiragana' => $studentData->name_hiragana,
                'firstName' => $studentData->first_name_ja, 
                'lastName' => $studentData->last_name_ja,
                'overrideAllowed' => $studentData->override_allowed,
                'monthlyRescheduleLimit' => $studentData->monthly_reschedule_limit,
                'reschedulesUsed' => $studentData->reschedules_used,
                'classes' => $classes,
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
