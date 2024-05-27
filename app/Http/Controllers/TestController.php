<?php

namespace App\Http\Controllers;


use App\Models\AnswerKeys;
use App\Models\Answers;
use App\Models\Summaries;
use App\Models\Students;
use App\Models\Questions;
use Illuminate\Http\Request;

class TestController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function checkBio($student_id)
    {
        $user = Students::where('id', $student_id)->first();

        if (
            is_null($user->student_name) || is_null($user->birth_date) ||
            is_null($user->gender) || is_null($user->address) ||
            is_null($user->province) || is_null($user->city) || is_null($user->contact)
        ) {
            return true;
        }
    }

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

    public function formUnified(Request $request, $question_id, $student_id)
    {
        if (Students::where('id', $student_id)->count() == 0) {
            return response()->json(['message' => 'Data siswa tidak ditemukan'], 404);
        }

        if ($this->checkBio($student_id)) {
            return response()->json(['message' => 'Data biodata siswa belum lengkap'], 400);
        }

        if ($question_id > 306 || $question_id <= 0) {
            return response()->json(['message' => 'Data pertanyaan tidak ditemukan'], 404);
        }

        $time_limit = 600;
        if ($question_id > 114) {
            $time_limit = 360;
        }

        $first_id = $this->calculateFirstId($question_id);

        $time_student_answers = Answers::where('question_id', $first_id)->where('student_id', $student_id)->first();

        if ($time_student_answers) {
            $current_time = strtotime(date('Y-m-d H:i:s'));
            $time_answered = strtotime($time_student_answers->created_at);
            $difference = $current_time - $time_answered;

            if ($difference > $time_limit) {
                return response()->json(['message' => 'Times out, go to next tes!'], 400);
            }

        }

        $number = $question_id % 60 ? $question_id % 60 : 60;
        $category_id = $this->determineCategory($question_id);

        $question = Questions::where('id', $question_id)->first();
        $student_answers = Answers::where('question_id', $question->id)->where('student_id', $student_id)->first();
        $answer_choices = AnswerKeys::where('question_id', $question_id)->first();
        $total_question = Questions::whereIn('category_id', $category_id)->orderBy('id', 'asc')->get();

        $data = [
            "number" => $number,
            "question_id" => $question_id,
            "total_question" => $total_question->count(),
            "question" => $question,
            "student_answer" => $student_answers ? $student_answers->answer : null,
            "last_id" => $total_question->last()->id,
            "answer_choices" => $answer_choices ? $answer_choices : null,
        ];

        return response()->json($data, 200);
    }

    private function calculateFirstId($id)
    {
        if ($id <= 60) {
            return 1;
        } elseif ($id <= 114) {
            return 61;
        } elseif ($id <= 174) {
            return 115;
        } elseif ($id <= 204) {
            return 175;
        } elseif ($id <= 234) {
            return 205;
        } elseif ($id <= 264) {
            return 235;
        } elseif ($id <= 286) {
            return 265;
        } elseif ($id <= 306) {
            return 287;
        }
    }

    private function determineCategory($id)
    {
        if ($id <= 60) {
            return [1, 2, 3, 4, 5];
        } elseif ($id <= 114) {
            return [6, 7, 8, 9, 10, 11];
        } elseif ($id <= 174) {
            return [12];
        } elseif ($id <= 204) {
            return [13];
        } elseif ($id <= 234) {
            return [14];
        } elseif ($id <= 264) {
            return [15];
        } elseif ($id <= 286) {
            return [16];
        } elseif ($id <= 306) {
            return [17];
        }
    }

    public function submitUnified(Request $request, $question_id, $student_id)
    {
        if (Students::where('id', $student_id)->count() == 0) {
            return response()->json(['message' => 'Data siswa tidak ditemukan'], 404);
        }

        if ($this->checkBio($student_id)) {
            return response()->json(['message' => 'Data biodata siswa belum lengkap'], 400);
        }

        $this->validate($request, [
            'answer' => 'required'
        ]);

        $answer = Answers::updateOrCreate(
            ['question_id' => $question_id, 'student_id' => $student_id],
            ['answer' => $request->answer]
        );

        $message = 'Lanjut ke soal selanjutnya!';
        if (in_array($question_id, [60, 114, 174, 204, 234, 264, 286, 306])) {
            $message = "Sub Tes ini selesai! Lanjutkan ke sub tes berikutnya.";
        }

        if ($question_id > 306) {
            $message = 'Semua Sub Tes Selesai.';
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'next_id' => $question_id > 306 ? null : $question_id + 1
        ], 200);
    }
    //
}
