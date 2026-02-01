<?php

namespace App\Http\Controllers\v1;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 422);
        }
        
        // TODO:: modify later
        $tokenName = 'sanctumToken';
        $token = $user->createToken($tokenName);

        // demo reset
        foreach ($user->students as $student) {
            $student->reschedules_used = 0;
            $student->save();
        }

        return response()->json([
            'message' => 'Logged in successfully',
            'data' => [
                'token' => $token->plainTextToken,
                'user' => $user,
            ],
        ], 200);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully',
        ], 200);
    }

    public function me(Request $request): JsonResponse
    {
        $user = User::with('students.classEnrolments.classSession.classTemplate')->find($request->user()->id);

        $students = $user->students;
        foreach ($students as $student) {
            foreach ($student->classEnrolments as $class) {
                $className = explode("（", $class->classSession->classTemplate->label_ja);
                $dayOfWeek = $class->classSession->classTemplate->day_of_week;
                $class['class_name'] = $className[0];
                $class['day_of_week'] = $dayOfWeek;
            }
        }

        return response()->json([
            'data' => [
                'id' => $user->id,
                'email' => $user->email,
                'phoneNumber' => $user->phone_number,
                'students' => $students,
            ]
        ], 200);
    }
}
