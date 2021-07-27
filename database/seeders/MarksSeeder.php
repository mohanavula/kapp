<?php

namespace Database\Seeders;

use App\Models\Curriculum;
use App\Models\Regulation;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;

use function PHPUnit\Framework\returnSelf;

class MarksSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $batch = 2015;
        $semesters = [1, 2, 3, 4, 5, 6, 7, 8];
        $regulation = Regulation::firstWhere('short_name', 'R15UG');

        $semester_id = 0;
        foreach($semesters as $s) {
            echo "\n";
            echo 'Processing semester: ' . $s;

            // semester
            $semester = $regulation->semesters->firstWhere('semester_number', $s);

            // fetch data
            // file name format: batch-semester-im.csv
            
            $file_im = $batch . '-marks/' . $batch . '-' . $s . '-im.csv';
            $file_em = $batch . '-marks/' . $batch . '-' . $s . '-em.csv';
            $marks_im = Excel::toCollection(null, storage_path($file_im))[0]->splice(1);
            $marks_em = Excel::toCollection(null, storage_path($file_em))[0]->splice(1);

            // keyed collecton
            $marks_im = $marks_im->map(function ($row) {
                return ['regdno' => $row[0], 'subject_code' => (string) $row[1], 'im' => $row[2]];
            });
            $marks_em = $marks_em->map(function ($row) {
                return ['regdno' => $row[0], 'subject_code' => (string) $row[1], 'em' => $row[2]];
            });

            // get all students
            $regdnos = $marks_im->pluck('regdno')->unique();

            $specialization_id = 0;
            foreach($regdnos as $regdno) {
                $student = Student::firstWhere('regdno', $regdno);
                if (is_null($student)) {
                    echo 'Student not found: ' . $regdno;
                    echo "\n";
                    continue;
                }

                // process student marks
                // $regdno = $student->regdno;
                // $im = $marks_im->where('regdno', $regdno)->pluck('im', 'subject_code');
                // $em = $marks_em->where('regdno', $regdno)->pluck('em', 'subject_code');
                // $marks = $im->mergeRecursive($em);

                // curriculum
                $subjects = $marks_im->where('regdno', $regdno)->pluck('subject_code')->unique(); // $marks->keys(); //->map(function($s) {
                $curricula = collect();
                foreach($subjects as $s) {
                    $curricula->put($s, Subject::with('curricula')->firstWhere('subject_code', $s)->curricula->firstWhere('specialization_id', $student->specialization_id));
                }

                // total credits in semester
                if ($semester_id != $semester->id | $specialization_id != $student->specialization_id) {
                    $total_credits = Curriculum::where('specialization_id', $student->specialization_id)
                        ->where('semester_id', $semester->id)->sum('credits');
                    $specialization_id = $student->specialization_id;
                    $semester_id = $semester->id;    
                }

                // grades
                $marks_records = collect();
                $sgpa = 0.0;
                $semester_credits = 0.0;
                foreach($subjects as $subject_code) {
                    $max_im = $curricula[$subject_code]->internal_exam_marks;
                    $max_em = $curricula[$subject_code]->end_exam_marks;
                    $im = $marks_im->where('regdno', $regdno)->where('subject_code', $subject_code)->pluck('im'); //[0]; // $marks[$subject_code][0]; // $marks_im->where('regdno', $regdno)->where('subject_code', 's' . $subject_code)[0]->im;
                    $im = $im->count() > 0 ? $im[0] : null;
                    $em = $marks_em->where('regdno', $regdno)->where('subject_code', $subject_code)->pluck('em'); // [0]; // $marks[$subject_code][1]; // $marks_em->where('regdno', $regdno)->where('subject_code', 's' . $subject_code)[0]->em;
                    $em = $em->count() > 0 ? $em[0] : null;
                    $required = true;
                    if ($max_em > 0) {
                        $passed = ($em >= 0.35 * $max_em) & ($im + $em >= 40);
                    }
                    elseif ($max_im > 0) {
                        $passed = $im >= 0.40 * $max_im;
                    }
                    elseif ($marks_im == 0 & $max_em == 0) {
                        $passed = true;
                        $required = false;
                    }

                    $credits = $passed ? $curricula[$subject_code]->credits : 0;
                    $marks_records->push([
                        'subject_id' => $curricula[$subject_code]->pivot->subject_id,
                        'student_id' => $student->id,
                        'im' => $im,
                        'em' => $em,
                        'credits' => $credits,
                        'grade' => $this->grade($im + $em),
                        'passed' => $passed,
                        'required' => $required,
                    ]);

                    // aggregates
                    $sgpa += $credits*$this->grade_points($im + $em)/$total_credits;
                    $semester_credits += $credits;
                    
                    
                }
                $aggregate_record = [
                    'semester_id' => $semester->id,
                    'student_id' => $student->id,
                    'sgpa' => $sgpa,
                    'cgpa' => 0.0,
                    'semester_credits' => $semester_credits,
                    'cumulative_credits' => 0.0
                ];

                DB::table('marks')->insertOrIgnore($marks_records->toArray());
                DB::table('aggregates')->insertOrIgnore($aggregate_record);
                // echo $regdno . ': ' . $sgpa . "\n";
            }
        }
    }

    private function grade($marks) {
        if ($marks >= 95) 
            return 'A+';
        elseif ($marks >= 90)
            return 'A';
        elseif ($marks >= 85)
            return 'A-';
        elseif ($marks >= 80)
            return 'B+';
        elseif ($marks >= 75)
            return 'B';
        elseif ($marks >= 70)
            return 'B-';
        elseif ($marks >= 65)
            return 'C+';
        elseif ($marks >= 60)
            return 'C';
        elseif ($marks >= 55)
            return 'C-';
        elseif ($marks >= 50)
            return 'D+';
        elseif ($marks >= 45)
            return 'D';
        elseif ($marks >= 40)
            return 'D-';
        else
            return 'F';
    }

    private function grade_points($marks) {
        if ($marks >= 95) 
            return 10.0;
        elseif ($marks >= 90)
            return 9.5;
        elseif ($marks >= 85)
            return 9.0;
        elseif ($marks >= 80)
            return 8.5;
        elseif ($marks >= 75)
            return 8.0;
        elseif ($marks >= 70)
            return 7.5;
        elseif ($marks >= 65)
            return 7.0;
        elseif ($marks >= 60)
            return 6.5;
        elseif ($marks >= 55)
            return 6.0;
        elseif ($marks >= 50)
            return 5.5;
        elseif ($marks >= 45)
            return 5.0;
        elseif ($marks >= 40)
            return 4.5;
        else
            return 0.0;
    }

    public function check_subjects() {
        $batch = 2015;
        $semesters = [1, 2, 3, 4, 5, 6, 7, 8];
        $regulation = Regulation::firstWhere('short_name', 'R15UG');

        foreach($semesters as $s) {
            echo "\n";
            echo 'Processing semester: ' . $s;

            // semester
            $semester = $regulation->semesters->firstWhere('semester_number', $s);

            // fetch data
            // file name format: batch-semester-im.csv
            
            $file_im = $batch . '-marks/' . $batch . '-' . $s . '-im.csv';
            $file_em = $batch . '-marks/' . $batch . '-' . $s . '-em.csv';
            $marks_im = Excel::toCollection(null, storage_path($file_im))[0]->splice(1);
            $marks_em = Excel::toCollection(null, storage_path($file_em))[0]->splice(1);

            // keyed collecton
            $marks_im = $marks_im->map(function ($row) {
                return ['regdno' => $row[0], 'subject_code' => (string) $row[1], 'im' => $row[2]];
            });
            $marks_em = $marks_em->map(function ($row) {
                return ['regdno' => $row[0], 'subject_code' => (string) $row[1], 'em' => $row[2]];
            });
            
            $subject_codes = $marks_im->pluck('subject_code')->unique();
            foreach($subject_codes as $subject_code) {
                $obj = Subject::firstWhere('subject_code', $subject_code);
                if (is_null($obj)) echo "\n Semetser: " . $s . " Subject_code: " . $subject_code . " not found in im";
            }
            
            $subject_codes = $marks_em->pluck('subject_code')->unique();
            foreach($subject_codes as $subject_code) {
                $obj = Subject::firstWhere('subject_code', $subject_code);
                if (is_null($obj)) echo "\n Semetser: " . $s . " Subject_code: " . $subject_code . " not found in em";
            }
        }
    }
}
