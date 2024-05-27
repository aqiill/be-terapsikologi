<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\Administrators;
use App\Models\Students;
use App\Models\Schools;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $email = $request->input('email');
        $password = $request->input('password');

        $user = Administrators::where('admin_email', $email)->first();
        $userType = 'administrator';

        if (!$user) {
            $user = Schools::where('school_email', $email)->first();
            $userType = 'school';
        }

        if (!$user) {
            $user = Students::where('student_email', $email)->first();
            $userType = 'student';
        }

        if (!$user || !Hash::check($password, $user->password)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        return response()->json(['message' => 'Login successful', 'user' => $user, 'user_type' => $userType]);
    }

    public function register(Request $request)
    {
        $data = $request->all();

        // Validate data
        $validator = Validator::make($data, [
            'email' => 'required|email|unique:students,student_email',
            'password' => 'required|min:6',
            'final_score' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Registration failed: ' . implode(' ', $validator->errors()->all())
            ], 400);
        }

        // manual school 
        if (!empty($data['manual_school_name'])) {
            $result = $this->processManualSchool($data['manual_school_name']);
            if (isset($result['error'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => $result['error']
                ], $result['code']);
            }
            $schoolId = $result['school_id'];
        } else {
            $schoolId = $data['school'];
            if (!Schools::find($schoolId)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Registration failed: School not found'
                ], 404);
            }
        }

        // Create student
        $studentData = $this->createStudentData($schoolId, $data);
        Students::create($studentData);

        return response()->json([
            'status' => 'success',
            'message' => 'Registration successful'
        ], 201);
    }

    private function processManualSchool($manualSchoolName)
    {
        $email = str_replace(' ', '', strtolower($manualSchoolName)) . '@gmail.com';

        if (Schools::where('school_email', $email)->exists()) {
            return ['error' => 'Registration failed: School already registered', 'code' => 400];
        }

        try {
            $schoolData = Schools::create([
                'school_name' => strtolower($manualSchoolName),
                'npsn' => '00000000',
                'school_email' => $email,
                'password' => Hash::make('12345678#'),
                'province' => '13',
                'city' => '1376',
                'address' => 'Jl. Jalan Jeruk No 54',
                'operator_name' => $manualSchoolName,
                'contact' => '081234567890',
                'role' => 'school',
                'payment_status' => 'n',
            ]);
            return ['school_id' => $schoolData->id];
        } catch (\Exception $e) {
            return ['error' => 'Registration failed: ' . $e->getMessage(), 'code' => 500];
        }
    }

    private function createStudentData($schoolId, $data)
    {
        return [
            'school_id' => $schoolId,
            'school_status' => 'pending',
            'final_score' => $data['final_score'],
            'student_email' => $data['email'],
            'password' => Hash::make($data['password']),
            'payment_status' => 'n',
            'recommendation_type' => 'kemdikbud',
            'role' => 'student'
        ];
    }

}
