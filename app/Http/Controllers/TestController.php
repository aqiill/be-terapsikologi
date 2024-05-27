<?php

namespace App\Http\Controllers;


use App\Models\Answers;
use App\Models\Summaries;
use Illuminate\Http\Request;

class TestController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function index(Request $request, $student_id)
    {

        $test = Answers::where('student_id', $student_id)->max('question_id');

        if (!$test) {
            return response()->json(['message' => 'Data jawaban siswa tidak ditemukan'], 404);
        }

        $least_time = Answers::where('student_id', $student_id)->orderBy('created_at', 'asc')->first();

        $question_ids = [1, 61, 115, 175, 205, 235, 265, 287];

        $first_answers = Answers::where('student_id', $student_id)
            ->whereIn('question_id', $question_ids)
            ->orderBy('created_at', 'asc')
            ->get()
            ->keyBy('question_id');

        $summary_count = Summaries::where('student_id', $student_id)->count();

        // Menyiapkan data untuk response
        $data = [
            'test' => $test,
            'summary' => $summary_count,
            'least_time' => $least_time,
            'time_first_answers' => [
                'ocean' => $first_answers[1] ?? null,
                'riasec' => $first_answers[61] ?? null,
                'visual' => $first_answers[115] ?? null,
                'induction' => $first_answers[175] ?? null,
                'quatitative_reasoning' => $first_answers[205] ?? null,
                'math' => $first_answers[235] ?? null,
                'reading' => $first_answers[265] ?? null,
                'memory' => $first_answers[287] ?? null,
            ],
        ];

        return response()->json($data, 200);
    }


    //
}
