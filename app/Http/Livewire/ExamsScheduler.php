<?php

namespace App\Http\Livewire;

use App\Models\Regulation;
use App\Models\Curriculum;
use App\Models\Exam;
use App\Models\Subject;
use DateTime;
use Illuminate\Console\Scheduling\Schedule;
use Livewire\Component; 
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;

class ExamsScheduler extends Component
{
    use WithFileUploads;
    
    public $datafile;
    public $regulation;
    // public $show_feedback = false;
    public $validation_messages = [];
    
    public $academic_year;
    public $regulation_name;
    public $semester_number;
    public $semester_id;

    public $schedule = [];
    public $records = 0;
    public $invalid_records = 0;

    public $exam_category = '';
    public $exam_categories = [];

    public function update_academic_year($inc) {
        $this->academic_year = $this->academic_year + $inc;
    }

    public function updatedDatafile() {
        $this->schedule = [];
        $this->validation_messages = [];
        $this->validate([
            'datafile' => 'required|file|mimes:csv|max:5120',
        ]);
    }

    public function reset_datafile() {
        $this->datafile = '';
        $this->schedule = [];
        $this->validation_messages = [];
        $this->records = 0;
        $this->invalid_records = 0;

    }

    public function store() {
        // store the schedule
        $exam = Exam::create([
            'semester_id' => $this->semester_id,
            'short_name' => $this->get_examination_code(),
            'name' => $this->semester_id,
            'academic_year' => $this->academic_year,
            'exam_category' => $this->exam_category,
        ]);

        foreach($this->schedule as $s) {
            $exam->exam_schedules()->create([
                'specialization_id' => $s['specialization_id'],
                'subject_id' => $s['subject']['id'],
                'schedule_date' => $s['schedule_date'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // reset datafile
        $this->reset_datafile();
    }

    public function process() {
        // $this->show_feedback = true;
        $this->validation_messages = [];
        $this->schedule = [];
        $this->resetErrorBag();

        $this->validate([
            'datafile' => 'required|file|mimes:csv|max:5120',
        ]);

        $path = $this->datafile->store('data');
        $data = Excel::toCollection(null, $path)[0];
        
        $this->records = count($data) - 1;
        $this->academic_year = $data[0][0];
        $this->regulation_name = $data[0][1];
        $this->semester_number = $data[0][2];
        
        $this->validate([
            'academic_year' => 'required|numeric|integer|min:2014',
            'regulation_name' => 'required|string|exists:regulations,short_name',
            'semester_number' => 'required|numeric|integer|min:1'
        ]);
        
        
        // semester number
        $this->regulation = Regulation::where('short_name', $this->regulation_name)->get()->first();
        $semester_numbers = $this->regulation->semesters->pluck('semester_number');
        if ($semester_numbers->search($this->semester_number) === false) {
            $this->addError('semester_number', 'Required field Semester number is not found. No such semester number: ' . $this->semester_number . '.');
            return;
        }
        $this->semester_id = $this->regulation->semesters()->where('semester_number', $this->semester_number)->first()->id;
        
        // process schedule
        
        // specializations
        $specialization_ids = $this->regulation->program->specializations->pluck('id', 'short_name');
        $curricula = Curriculum::with('subjects')->where('semester_id', $this->semester_id)->get();
        // $row_no = 1;
        foreach($data->splice(1) as $key => $d) {
            // $row_no++;
            
            // specialization
            $specialization_short_name = $d[0];
            if (is_null($specialization_short_name)) {
                array_push($this->validation_messages, [
                    'row_no' => $key + 2,
                    'valid' => false,
                    'field' => 'Specialization',
                    'message' => 'Required field Specialization is not given.'
                ]);
                continue;
            }
            if ($specialization_ids->keys()->search($specialization_short_name) === false) {
                array_push($this->validation_messages, [
                    'row_no' => $key + 2,
                    'valid' => false,
                    'field' => 'Specialization',
                    'message' => 'Required field Specialization is not found. No such Specialization: ' . $specialization_short_name
                ]);
                continue;
            }

            // subject_code
            $subject_code = $d[1];
            if (is_null($subject_code)) {
                array_push($this->validation_messages, [
                    'row_no' => $key + 2,
                    'valid' => false,
                    'field' => 'Subject code',
                    'message' => 'Required field Subject code is not given.'
                ]);
                continue;
            }
            $subject_codes = $curricula->where('specialization_id', $specialization_ids[$specialization_short_name])->pluck('subjects')->collapse()->pluck('subject_code');
            if ($subject_codes->search($subject_code) === false) {
                array_push($this->validation_messages, [
                    'row_no' => $key + 2,
                    'valid' => false,
                    'field' => 'Subject code',
                    'message' => 'Required field Subject Code is not found. No such Subject Code: ' . $subject_code
                ]);
                continue;
            }

            // schedule date
            $date = $d[2];
            if (is_null($date)) {
                array_push($this->validation_messages, [
                    'row_no' => $key + 2,
                    'valid' => false,
                    'field' => 'Schedule date',
                    'message' => 'Required field Schedule date is not given.'
                ]);
                continue;
            }

            if(!$this->is_date($date)) {
                array_push($this->validation_messages, [
                    'row_no' => $key + 2,
                    'valid' => false,
                    'field' => 'Schedule date',
                    'message' => 'Required field schedule date: ' . $date . ' is not valid. Provide date in YYYY-MM-DD format.'
                ]);
                continue;
            }

            array_push($this->schedule, [
                'row_no' => $key + 2,
                'specialization_id' => $specialization_ids[$specialization_short_name],
                'specialization_short_name' => $specialization_short_name,
                'subject' => Subject::where('subject_code', $subject_code)->get()->first(),
                'schedule_date' => $date
            ]);
        }
        $this->invalid_records = count($this->validation_messages);
    }

    private function is_date($d) {
        return true;
    }

    public function mount() {
        $this->exam_categories = [
            'REGULAR'=> 'Regular End Examinations',
            'SUPPLEMENTARY'=> 'Supplementary End Examinations',
            'MIDTERM' => 'Midterm Examinations'
        ];
        $this->exam_category = 'REGULAR';
        $this->academic_year = now()->year;
    }

    public function render()
    {
        return view('livewire.exams-scheduler');
    }

    private function get_random_string($len = 8) {
        $str = 'abcdefghijklmnopqrstuvwxyz';
        return substr(str_shuffle($str), 0, $len);
    }

    // generate 8-char radom code
    private function get_examination_code() {
        $unique = false;
        while (!$unique) {
            $code = $this->get_random_string();
            $n = Exam::where('short_name', $code)->get()->count();
            if ($n == 0) $unique = true; 
        }
        return $code;
    }

    private function get_examination_name() {
        $start_date = $this->get_start_date();
        return $this->regulation->program->short_name . '-' 
            . $this->regulation->short_name . '-' 
            . $this->exam_categories[$this->exam_category] . ' of ' 
            . date_format(DateTime::createFromFormat('Y-m-d', $this->start_date), 'M-Y');
    }

    private function get_start_date() {
        return array_multisort(array_column($this->schedule, 'schedule_date'))[0];
    }
}
