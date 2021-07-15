<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Exam;
use App\Models\ExamSchedule;
use App\Models\Curriculum;
use App\Models\Student;
use Illuminate\Support\Facades\DB;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use PhpParser\Node\Stmt\Continue_;

class MarksUploader extends Component
{
    use WithFileUploads;

    public $datafile;
    public $exams;
    public $exam;

    public $validation_messages = [];
    public $validated_data = [];

    public $total_records;
    public $invalid_records;

    public $close_modal = false;

    public function reset_datafile() {
        $this->total_records = '';
        $this->invalid_records = '';
        $this->datafile = '';
        $this->resetValidation('datafile');
    }

    public function updatedDatafile() {
        $this->total_records = '';
        $this->invalid_records = '';
        $this->validated_data = [];
        $this->validation_messages = [];
        $this->validate([
            'datafile' => 'required|file|mimes:csv|max:5120'
        ]);
    }

    public function process_upload() {
        $this->validation_messages = [];
        $this->data = [];
        $this->resetErrorBag();

        $this->validate([
            'datafile' => 'required|file|mimes:csv|max:5120',
        ]);

        $path = $this->datafile->store('data');
        $data = Excel::toCollection(null, $path)[0];
        $this->total_records = count($data) - 1;

        $curricula = Curriculum::with('subjects')->where('semester_id', $this->exam->semester_id)->get();
        $subjects = $curricula->pluck('subjects')->collapse()->pluck('id', 'subject_code');


        foreach($data->splice(1) as $key => $d) {
            $regdno = $d[0];
            $subject_code = $d[1];
            $marks = $d[2];
            
            // validate: regdno
            if (is_null($regdno)) {
                array_push($this->validation_messages, [
                    'row_no' => $key + 2,
                    'valid' => false,
                    'field' => 'Regdno',
                    'message' => 'Required field Regdno is not given.'
                ]);
                continue;
            }
            $student = Student::where('regdno', $regdno)->get()->first();
            if (is_null($student)) {
                array_push($this->validation_messages, [
                    'row_no' => $key + 2,
                    'valid' => false,
                    'field' => 'Regdno',
                    'message' => 'Required field Regdno is not found. No such Regdno: ' . $regdno
                ]);
                continue;
            }

            // validate: subject_code
            if (is_null($subject_code)) {
                array_push($this->validation_messages, [
                    'row_no' => $key + 2,
                    'valid' => false,
                    'field' => 'Subject code',
                    'message' => 'Required field Subject Code is not given.'
                ]);
                continue;
            }
            if ($subjects->keys()->search($subject_code) === false) {
                array_push($this->validation_messages, [
                    'row_no' => $key + 2,
                    'valid' => false,
                    'field' => 'Subject code',
                    'message' => 'Required field Subject Code is not found. No such Subject code: ' . $subject_code
                ]);
                continue;
            }

            // validate: marks 
            if (is_null($marks)) {
                array_push($this->validation_messages, [
                    'row_no' => $key + 2,
                    'valid' => false,
                    'field' => 'Marks',
                    'message' => 'Required field Marks is not given.'
                ]);
                continue;
            }
            if(!is_int($marks)) {
                array_push($this->validation_messages, [
                    'row_no' => $key + 2,
                    'valid' => false,
                    'field' => 'Marks',
                    'message' => 'Required field Marks is not valid. Prove integer number.'
                ]);
                continue;
            }

            if ($marks >= 0 ) {
                array_push($this->validated_data, [
                    'exam_id' => $this->exam->id,
                    'student_id' => $student->id,
                    'subject_id' => $subjects[$subject_code],
                    'end_exam_marks' => $marks
                ]);
            }
        }
        $this->invalid_records = count($this->validation_messages);

    }

    public function store() {
        if (count($this->validated_data) == 0 ) return;
        // foreach($this->validated_data as $vd) {
        //     if ($vd[2] < 0) continue;
        //     DB::table('end_exam_marks')->insert([
        //         'exam_id' => $this->exam->id,
        //         'student_id' => $vd[0],
        //         'subject_id' => $vd[1],
        //         'end_exam_marks' => $vd[2]
        //     ]);
        // }
        DB::table('end_exam_marks')->insertOrIgnore($this->validated_data);
        $this->close_modal = true;
    }

    public function show_modal($exam_id) {
        $this->exam = $this->exams->firstWhere('id', $exam_id);
        $this->dispatchBrowserEvent('show_upload_marks_modal');
    }

    public function mount() {
        $this->exams = Exam::with(['exam_schedules', 'semester'])->where('status', 'ACTIVE')->get();
    }

    public function render()
    {
        return view('livewire.marks-uploader');
    }
}
