<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Answers;
use App\Models\AnswerKeys;
use App\Models\Questions;
use App\Models\Summaries;

class ReportController extends Controller
{
    // private api-key qwe123qwe#
    private $api_key = 'qwe123qwe#';

    public function generate(Request $request, $student_id, $school_id)
    {
        if ($request->header('api-key') !== $this->api_key) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $student_answers = Answers::where('student_id', $student_id)->get();

        if ($student_answers->isEmpty()) {
            return response()->json(['message' => 'Data jawaban siswa tidak ditemukan'], 404);
        }

        // Inisialisasi nilai total untuk setiap kategori
        $totalScores = $this->initializeCategoryScores();

        // Menghitung skor berdasarkan jawaban
        $totalScores = $this->calculateScores($student_answers, $totalScores);

        // Menetapkan hasil kategori berdasarkan aturan yang ditentukan
        $categoryResults = $this->applyCategoryRules($totalScores);


        if (Summaries::where('student_id', $student_id)->exists()) {
            return response()->json(['message' => 'Data sudah ada'], 400);
        }
        // Menyimpan data summaries
        $this->saveSummary($student_id, $school_id, $categoryResults);

        return response()->json(['message' => 'Generate Report Berhasil']);
    }

    private function initializeCategoryScores()
    {
        return [
            'o' => 0,
            'ce' => 0,
            'ea' => 0,
            'an' => 0,
            'n' => 0,
            'r' => 0,
            'i' => 0,
            'a' => 0,
            's' => 0,
            'e' => 0,
            'c' => 0,
            'math' => 0,
            'visual' => 0,
            'memory' => 0,
            'reading' => 0,
            'induction' => 0,
            'quantitative_reasoning' => 0,
        ];
    }

    private function calculateScores($student_answers, $totalScores)
    {
        foreach ($student_answers as $answer) {
            $question_id = $answer->question_id;
            $question = Questions::where('id', $question_id)->first();

            if ($question_id >= 1 && $question_id <= 114) {
                if ($question->category_id == 1) {
                    $totalScores['o'] += intval($answer->answer);
                } elseif ($question->category_id == 2) {
                    $totalScores['ce'] += intval($answer->answer);
                } elseif ($question->category_id == 3) {
                    $totalScores['ea'] += intval($answer->answer);
                } elseif ($question->category_id == 4) {
                    $totalScores['an'] += intval($answer->answer);
                } elseif ($question->category_id == 5) {
                    $totalScores['n'] += intval($answer->answer);
                } elseif ($question->category_id == 6) {
                    $totalScores['r'] += intval($answer->answer);
                } elseif ($question->category_id == 7) {
                    $totalScores['i'] += intval($answer->answer);
                } elseif ($question->category_id == 8) {
                    $totalScores['a'] += intval($answer->answer);
                } elseif ($question->category_id == 9) {
                    $totalScores['s'] += intval($answer->answer);
                } elseif ($question->category_id == 10) {
                    $totalScores['e'] += intval($answer->answer);
                } elseif ($question->category_id == 11) {
                    $totalScores['c'] += intval($answer->answer);
                }
            } else {
                $answer_key = AnswerKeys::where('question_id', $question_id)->first();

                if ($answer_key && strtolower($answer->answer) == strtolower($answer_key->correct_answer)) {
                    if ($question->category_id == 12) {
                        $totalScores['visual'] += 1;
                    } elseif ($question->category_id == 13) {
                        $totalScores['induction'] += 1;
                    } elseif ($question->category_id == 14) {
                        $totalScores['quantitative_reasoning'] += 1;
                    } elseif ($question->category_id == 15) {
                        $totalScores['math'] += 1;
                    } elseif ($question->category_id == 16) {
                        $totalScores['reading'] += 1;
                    } elseif ($question->category_id == 17) {
                        $totalScores['memory'] += 1;
                    }
                }
            }

        }
        return $totalScores;
    }

    private function applyCategoryRules($totalScores)
    {
        $results = [];
        foreach ($totalScores as $category => $score) {
            $results[$category] = $score >= $this->getThreshold($category) ? '1' : '0';
        }
        return $results;
    }

    private function getThreshold($category)
    {
        // Aturan untuk ambang batas skor
        if (in_array($category, ['math', 'visual', 'memory', 'reading', 'induction', 'quantitative_reasoning'])) {
            return 7;  // Untuk kategori khusus keahlian
        }
        return 41;  // Untuk kategori umum
    }

    private function saveSummary($student_id, $school_id, $categoryResults)
    {
        $data = array_merge(['student_id' => $student_id, 'school_id' => $school_id], $categoryResults);
        Summaries::create($data);
    }
}
