<?php

namespace App\Http\Livewire;

use App\Models\Regulation;
use App\Models\Curriculum;
use App\Models\Subject;
use DateTime;
use Livewire\Component; 
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;

use function PHPUnit\Framework\isNull;

class ExamScheduleDataUpload extends Component
{
    use WithFileUploads;
    
    public $datafile;
    public $errors;
    public $regulation;
    public $academic_year;
    public $semester;
    public $abort_message;
    public $abort = false;
    public $show_feedback = false;
    public $feedback_messages = [];

    public function process() {
        
        $this->validate([
            'datafile' => 'required|file|mimes:csv|max:5120',
        ]);

        $this->show_feedback = true;
        $path = $this->datafile->store('data');
        $data = Excel::toCollection(null, $path)[0];
        
        // acadmic academic year
        $this->academic_year = $data[0][0];
        if (is_null($this->academic_year)) {
            $this->abort = true;
            $this->abort_message = 'Academic year is not given.';
            return;
        }

        // regulation
        $regulation_short_name = $data[0][1]; 
        if (is_null($regulation_short_name)) {
            $this->abort = true;
            $this->abort_message = 'Required field Regulation is not given.';
            return;
        }
        $this->regulation = Regulation::with(['program'])->where('short_name', strtoupper($regulation_short_name))->get()->first();
        if (is_null($this->regulation)) {
            $this->abort = true;
            $this->abort_message = 'Required field regulation is not found. No such regulation: ' . $regulation_short_name . '.';
            return;
        }

        // semester
        $semester_numbers = $this->regulation->semesters->pluck('semester_number');
        $semester_number = intval($data[0][2]);
        if (is_null($semester_number)) {
            $this->abort = true;
            $this->abort_message = 'Required field Semester number is not given.';
            return;
        }
        if ($semester_numbers->search($semester_number) === false) {
            $this->abort = true;
            $this->abort_message = 'Required field Semester number is not found. No such semester number: ' . $semester_number . '.';
            return;
        }
        $semester_id = $this->regulation->semesters()->where('semester_number', $semester_number)->first()->id;

        // process schedule

        // specializations
        $specialization_short_names = $this->regulation->program->specializations->pluck('id', 'short_name');
        $curricula = Curriculum::with('subjects')->where('semester_id', $semester_id)->get();
        $row_no = 1;
        foreach($data->splice(1) as $d) {
            $row_no++;
            
            // specialization
            $specialization_short_name = $d[0];
            if (is_null($specialization_short_name)) {
                array_push($this->feedback_messages, [
                    'row_no' => $row_no,
                    'message' => 'Required field Specialization is not given.'
                ]);
                continue;
            }

            if ($specialization_short_names->keys()->search($specialization_short_name) === false) {
                array_push($this->feedback_messages, [
                    'row_no' => $row_no,
                    'message' => 'Required field Specialization is not found. No such Specialization: ' . $specialization_short_name
                ]);
                continue;
            }

            // subject_code
            $subject_code = $d[1];
            $subject_codes = $curricula->where('specialization_id', $specialization_short_names[$specialization_short_name])->pluck('subjects')->collapse()->pluck('subject_code');
            if ($subject_codes->search($subject_code) === false) {
                array_push($this->feedback_messages, [
                    'row_no' => $row_no,
                    'message' => 'Required field Subject Code is not found. No such Subject Code: ' . $subject_code
                ]);
                continue;
            }

            // schedule date
            $date = $d[2];
            if (is_null($date)) {
                array_push($this->feedback_messages, [
                    'row_no' => $row_no,
                    'message' => 'Required field schedule date is not given.'
                ]);
                continue;
            }

            if(!$this->is_date($date)) {
                array_push($this->feedback_messages, [
                    'row_no' => $row_no,
                    'message' => 'Required field schedule date: ' . $date . ' is not valid. Provide date in YYYY-MM-DD format.'
                ]);
                continue;
            }

            array_push($this->feedback_messages, [
                'row_no' => $row_no,
                'message' => 'Valid'
            ]);

        }
    }

    private function is_date($d) {
        return true;
    }

    public function save()
    {
        $this->validate([
            '$datafile' => 'file|max:5120',
        ]);

        $this->datafile->store('data');
    }

    public function render()
    {
        return view('livewire.exam-schedule-data-upload');
    }
}
