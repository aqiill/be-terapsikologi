<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Answers;
use App\Models\AnswerKeys;
use App\Models\Questions;
use App\Models\Summaries;
use App\Models\Students;
use App\Models\Majors;

class ReportController extends Controller
{
    // private api-key qwe123qwe#
    private $api_key = 'qwe123qwe#';

    public function generate(Request $request, $student_id, $school_id)
    {
        if ($request->header('api-key') !== $this->api_key) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        if ($school_id == 0) {
            $check = Students::where('id', $student_id)->first();
            if ($check->school_id != null) {
                return response()->json(['message' => 'Data siswa tidak ditemukan di sekolah tersebut'], 404);
            } else {
                $school_id = null;
            }
        } else {
            $check = Students::where('id', $student_id)->where('school_id', $school_id)->exists();
            if (!$check) {
                return response()->json(['message' => 'Data siswa tidak ditemukan di sekolah tersebut'], 404);
            }
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
        $this->saveSummary($student_id, $school_id, $categoryResults, $totalScores);

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

    private function saveSummary($student_id, $school_id, $categoryResults, $totalScores)
    {
        $total = $this->mapCategoryScores($totalScores);
        $data = array_merge(['student_id' => $student_id, 'school_id' => $school_id], $categoryResults, $total);
        Summaries::create($data);
    }

    private function mapCategoryScores($totalScores)
    {
        $newKeys = array_map(function ($key) {
            return 'total_' . $key;
        }, array_keys($totalScores));

        $newTotalScores = array_combine($newKeys, array_values($totalScores));
        return $newTotalScores;
    }

    public function categorize($category, $score)
    {
        $ranges = [
            'default' => [
                [1, 24, 'Sangat Rendah'],
                [25, 32, 'Rendah'],
                [33, 40, 'Cukup'],
                [41, 48, 'Tinggi'],
                [49, 60, 'Sangat Tinggi']
            ],
            'memory' => [
                [0, 4, 'Sangat Rendah'],
                [5, 6, 'Rendah'],
                [7, 9, 'Cukup'],
                [10, 16, 'Tinggi'],
                [17, PHP_INT_MAX, 'Sangat Tinggi']
            ],
            'induction' => [
                [0, 2, 'Sangat Rendah'],
                [3, 4, 'Rendah'],
                [5, 6, 'Cukup'],
                [7, 8, 'Tinggi'],
                [9, PHP_INT_MAX, 'Sangat Tinggi']
            ],
            'math' => [
                [0, 3, 'Sangat Rendah'],
                [4, 5, 'Rendah'],
                [6, 7, 'Cukup'],
                [8, 11, 'Tinggi'],
                [12, PHP_INT_MAX, 'Sangat Tinggi']
            ],
            'quantitative_reasoning' => [
                [0, 3, 'Sangat Rendah'],
                [4, 5, 'Rendah'],
                [6, 7, 'Cukup'],
                [8, 11, 'Tinggi'],
                [12, PHP_INT_MAX, 'Sangat Tinggi']
            ],
            'visual' => [
                [0, 2, 'Sangat Rendah'],
                [3, 4, 'Rendah'],
                [5, 6, 'Cukup'],
                [7, 10, 'Tinggi'],
                [11, PHP_INT_MAX, 'Sangat Tinggi']
            ],
            'reading' => [
                [0, 2, 'Sangat Rendah'],
                [3, 4, 'Rendah'],
                [5, 6, 'Cukup'],
                [7, 10, 'Tinggi'],
                [11, PHP_INT_MAX, 'Sangat Tinggi']
            ],
        ];

        // Memilih rentang nilai berdasarkan kategori, atau menggunakan default jika tidak ditemukan
        $selectedRange = $ranges[$category] ?? $ranges['default'];

        // Menentukan hasil berdasarkan rentang nilai
        foreach ($selectedRange as $range) {
            if ($score >= $range[0] && $score <= $range[1]) {
                return $range[2];
            }
        }
    }

    public function report(Request $request, $student_id)
    {
        if ($request->header('api-key') !== $this->api_key) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $summary = Summaries::where('student_id', $student_id)->first();

        if (!$summary) {
            return response()->json(['message' => 'Anda belum mengerjakan/mengenerate Tes!'], 404);
        }

        $student_data = Students::where('id', $student_id)->first();

        if ($student_data->student_name == null || $student_data->birth_date == null || $student_data->gender == null || $student_data->address == null || $student_data->province == null || $student_data->city == null || $student_data->contact == null) {
            return response()->json(['message' => 'Data siswa tidak lengkap'], 400);
        }

        $test_result = [
            'o' => $this->categorize('o', $summary->total_o),
            'ce' => $this->categorize('ce', $summary->total_ce),
            'ea' => $this->categorize('ea', $summary->total_ea),
            'an' => $this->categorize('an', $summary->total_an),
            'n' => $this->categorize('n', $summary->total_n),
            'r' => $this->categorize('r', $summary->total_r),
            'i' => $this->categorize('i', $summary->total_i),
            'a' => $this->categorize('a', $summary->total_a),
            's' => $this->categorize('s', $summary->total_s),
            'e' => $this->categorize('e', $summary->total_e),
            'c' => $this->categorize('c', $summary->total_c),
            'math' => $this->categorize('math', $summary->total_math),
            'visual' => $this->categorize('visual', $summary->total_visual),
            'memory' => $this->categorize('memory', $summary->total_memory),
            'reading' => $this->categorize('reading', $summary->total_reading),
            'induction' => $this->categorize('induction', $summary->total_induction),
            'quantitative_reasoning' => $this->categorize('quantitative_reasoning', $summary->total_quantitative_reasoning),
        ];

        // kecocokan
        $majors = $this->getRecommendations($student_data);
        $results_per_classification = $this->calculateCompatibility($majors, $summary);

        $data = [
            "results_per_classification" => $this->formatResults($results_per_classification),
            "hasil_tes" => $test_result,
        ];

        return response()->json($data);
    }

    private function getRecommendations($student)
    {
        $type = $student->recommendation_type;
        switch ($type) {
            case 'kemdikbud':
                $recommendation = ['S-1 Saintek', 'S-1 Soshum', 'D-4 Saintek', 'D-4 Soshum'];
                break;
            case 'kemenag':
                $recommendation = ['keagamaan'];
                break;
            case 'poltekkes':
                $recommendation = ['kesehatan'];
                break;
            case 'kedinasan':
                $recommendation = ['kedinasan'];
                break;
            default:
                return Majors::all();
        }
        return Majors::whereIn('classification', $recommendation)->get();
    }

    private function calculateCompatibility($majors, $summary)
    {
        $results = [];
        $classification = ['o', 'ce', 'ea', 'an', 'n', 'r', 'i', 'a', 's', 'e', 'c', 'math', 'visual', 'memory', 'reading', 'induction', 'quantitative_reasoning'];
        foreach ($majors as $major) {
            $compatibility = 0;
            foreach ($classification as $attribute) {
                $compatibility += $summary->$attribute == $major->$attribute ? 1 : 0;
            }
            $match_percentage = ($compatibility / count($classification)) * 100;
            if ($match_percentage > 70) {
                $results[$major->classification][] = [
                    'major_id' => $major->id,
                    'classification' => $major->classification,
                    'major' => $major->major,
                    'percentage' => $match_percentage,
                ];
            }
        }
        return $results;
    }

    private function formatResults($results_per_classification)
    {
        foreach ($results_per_classification as &$results_classification) {
            usort($results_classification, function ($a, $b) {
                return $b['percentage'] - $a['percentage'];
            });
            $results_classification = array_slice($results_classification, 0, 5);
        }
        return $results_per_classification;
    }

}
