<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Administrators;
use App\Models\Students;
use App\Models\Schools;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $apiKey = $request->header('api-key');
        if ($apiKey !== 'qwe123qwe#') {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

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
}
