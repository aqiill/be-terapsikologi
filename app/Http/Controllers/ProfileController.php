<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Students;


class ProfileController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function updateProfile(Request $request, $student_id)
    {
        $this->validate($request, [
            'student_name' => 'required',
            'final_score' => 'required',
            'student_email' => 'required',
            'address' => 'required',
            'province' => 'required',
            'city' => 'required',
            'birth_date' => 'required',
            'gender' => 'required',
            'contact' => 'required',
        ]);

        $student = Students::find($student_id);

        if ($student) {
            $student->update($request->all());
            return response()->json($student);
        } else {
            return response()->json(['message' => 'Data siswa tidak ditemukan'], 404);
        }

    }

    //
}
