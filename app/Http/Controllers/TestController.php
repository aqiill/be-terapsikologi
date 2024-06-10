<?php

namespace App\Http\Controllers;


use App\Models\AnswerKeys;
use App\Models\Answers;
use App\Models\Summaries;
use App\Models\Students;
use App\Models\Questions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        // if (!$test) {
        //     return response()->json(['message' => 'Data jawaban siswa tidak ditemukan'], 404);
        // }

        $least_time = Answers::where('student_id', $student_id)->orderBy('created_at', 'asc')->first();

        // Define ranges
        $ranges = [
            range(1, 60),
            range(61, 114),
            range(115, 174),
            range(175, 204),
            range(205, 234),
            range(235, 264),
            range(265, 286),
            range(287, 306),
        ];

        // Flatten the ranges into a single array of question_ids
        $question_ids = [];
        foreach ($ranges as $range) {
            $question_ids = array_merge($question_ids, $range);
        }

        $first_answers = Answers::where('student_id', $student_id)
            ->whereIn('question_id', $question_ids)
            ->orderBy('created_at', 'asc')
            ->get()
            ->keyBy('question_id');

        $summary_count = Summaries::where('student_id', $student_id)->count();

        // Define the start of each range for easier mapping
        $range_starts = [1, 61, 115, 175, 205, 235, 265, 287];

        // Map first answers to each category based on the start of the range
        $time_first_answers = [];
        $categories = ['ocean', 'riasec', 'visual', 'induction', 'quatitative_reasoning', 'math', 'reading', 'memory'];

        foreach ($range_starts as $index => $start) {
            $time_first_answers[$categories[$index]] = $first_answers->filter(function ($item, $key) use ($start) {
                return $key >= $start && $key < ($start + 60); // Assuming ranges of 60 for each category
            })->first();
        }

        // data
        $data = [
            'test' => $test,
            'summary' => $summary_count,
            'least_time' => $least_time,
            'time_first_answers' => $time_first_answers,
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

        // $number = $question_id % 60 ? $question_id % 60 : 60;
        if ($question_id > 0 && $question_id <= 60) {
            $number = $question_id;
        } elseif ($question_id > 60 && $question_id <= 114) {
            $number = $question_id - 60;
        } elseif ($question_id > 114 && $question_id <= 174) {
            $number = $question_id - 114;
        } elseif ($question_id > 174 && $question_id <= 204) {
            $number = $question_id - 174;
        } elseif ($question_id > 204 && $question_id <= 234) {
            $number = $question_id - 204;
        } elseif ($question_id > 234 && $question_id <= 264) {
            $number = $question_id - 234;
        } elseif ($question_id > 264 && $question_id <= 286) {
            $number = $question_id - 264;
        } elseif ($question_id > 286 && $question_id <= 306) {
            $number = $question_id - 286;
        }

        $category_id = $this->determineCategory($question_id);

        $question = Questions::where('id', $question_id)->first();
        $student_answers = Answers::where('question_id', $question->id)->where('student_id', $student_id)->first();
        $answer_choices = AnswerKeys::where('question_id', $question_id)->first();
        // $total_question = Questions::whereIn('category_id', $category_id)->orderBy('id', 'asc')->get();

        $total_question = Questions::leftJoin('answers', function ($join) use ($student_id) {
            $join->on('questions.id', '=', 'answers.question_id')
                ->where('answers.student_id', '=', $student_id);
        })
            ->select('questions.*', DB::raw('IF(answers.answer IS NOT NULL, true, false) as chosen'))
            ->whereIn('questions.category_id', $category_id)
            ->orderBy('questions.id', 'asc')
            ->get();

        // $first_answers = Answers::where('student_id', $student_id)->first();
        $first_answers = Answers::join('questions', 'answers.question_id', '=', 'questions.id')
            ->where('answers.student_id', $student_id)
            ->whereIn('questions.category_id', function ($query) use ($question_id) {
                $query->select('category_id')
                    ->from('questions')
                    ->where('id', $question_id);
            })
            ->orderBy('answers.created_at', 'asc')
            ->select('answers.created_at as waktu_jawab')
            ->first();

        $data = [
            "number" => $number,
            "question_id" => $question_id,
            "first_answers" => $first_answers ? $first_answers->waktu_jawab : null,
            "total_question" => $total_question,
            "question" => $question,
            "student_answer" => $student_answers ? $student_answers->answer : null,
            "last_id" => $total_question->last()->id,
            "first_id" => $total_question->first()->id,
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
            'answer' => 'required',
            'created_at' => 'required'
        ]);

        $answer = Answers::updateOrCreate(
            ['question_id' => $question_id, 'student_id' => $student_id],
            ['answer' => $request->answer, 'created_at' => $request->created_at]
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
