<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Regulation;
use App\Models\Semester;
use App\Models\Specialization;
use App\Models\Curriculum;
use App\Models\Exam;
use DateTime;
use Illuminate\Support\Collection;


class Examinations extends Component
{
    public $regulations;
    public $regulation;
    
    public $specializations;
    public $specialization;

    public $semesters;
    public $semester;

    public $curricula;
    
    public $regulation_id = '';
    public $semester_id ='';
    public $specialization_id ='';
    // public $curriculum_id = '';

    public $academic_year;
    public $start_date;
    public $end_date;
    public $exam_category = 'REGULAR';
    public $exam_categories;
    public $exam_short_name;
    public $exam_name;
    public $selected_subjects = [];
    public $selected_all_subjects = true;
    public $exam_dates = [];

    // validation states
    public $vs_name = null;
    public $vs_exam_short_name = null;
    public $vs_start_date = null;
    public $vs_end_date = null;
    public $vs_exam_dates = null;

    // schedule
    public $schedule = [];
    public $bagIndex = -1;
    public $bags = 0;

    public $toast_message;

    public function add_to_schedule() {
        // ensure some subjects are scheduled 
        $count = array_reduce($this->selected_subjects, function($carry, $z) {
            return $carry + ($z ? 1 : 0);
        }, 0);
        if ($count == 0) {
            $this->toast_message = 'Nothig to add. Schedule is empty. ';
            $this->dispatchBrowserEvent('showToast');
            return;
        }

        // check schedule dates
        foreach($this->vs_exam_dates as $key => $state) {
            if ($this->selected_subjects[$key] & ($state == null | $state == false)) {
                $this->toast_message = 'Exam dates not set or out of range. ';
                $this->dispatchBrowserEvent('showToast');
                return;
            }
        }

        $s = [];
        $s['specialization'] = ['id' => $this->specialization->id, 'short_name' => $this->specialization->short_name];
        $s['semester'] = ['id' => $this->semester->id, 'name' => $this->semester->name];
        $s['schedule'] = ['selected_subjects' => $this->selected_subjects, 'exam_dates' => $this->exam_dates, 'count' => $count];

        // update if already scheduled
        $repeat = false;
        foreach($this->schedule as $key => $sch) {
            if ($sch['specialization']['id'] == $this->specialization->id & $sch['semester']['id'] == $this->semester->id) {
                $this->schedule[$key]['schedule'] = $s['schedule'];
                $repeat = true;
                $this->toast_message = $this->specialization->short_name . '-' . $this->semester->name . ' schedule updated';
                $this->dispatchBrowserEvent('showToast');
                break;
            }
        }

        // add to schedule
        if(!$repeat) {
            $this->bagIndex += 1;
            $this->schedule[$this->bagIndex] = $s;
            $this->bags = count($this->schedule);
        }
        // dd($this->schedule); 
    }

    public function delete_bag($key) {
        unset($this->schedule[$key]);
        $this->bags = count($this->schedule);
    }

    public function update_academic_year($inc) {
        $this->academic_year = $this->academic_year + $inc;
    }

    // private function fill_subject_schedule() {
    //     if ($this->vs_start_date | $this->vs_end_date) return;
    //     if ($this->start_date < $this->end_date) return;
    // }

    // private function compare_dates() {
    //     if ($this->start_date > $this->end_date) {
    //         $this->vs_start_date = false;
    //         $this->vs_end_date = false;
    //     }
    //     else {
    //         $this->vs_start_date = true;
    //         $this->vs_end_date = true;
    //     }
    // }

    public function updatedStartDate() {
        $this->doValidateExamDates();
        $this->set_exam_name();
        // $this->vs_start_date = $this->validateDate($this->start_date);
        // if ($this->end_date == null | $this->vs_start_date == false) return;
        // $this->compare_dates();
    }

    public function updatedEndDate() {
        $this->doValidateExamDates();
        // $this->vs_end_date = $this->validateDate($this->end_date);
        // if ($this->start_date == null | $this->vs_end_date == false) return;
        // $this->compare_dates();
    }
 
    public function updatedRegulationId() {
        $this->regulation = $this->regulations->find($this->regulation_id);
        $this->specializations = $this->regulation->program->specializations;
        $this->specialization = ($this->specializations->count() > 0) ? $this->specializations[0] : null;
        $this->specialization_id = ($this->specializations->count() > 0) ? $this->specializations[0]->id : null;
        $this->semesters = $this->regulation->semesters;
        $this->semester = ($this->semesters->count() > 0) ? $this->semesters[0] : null;
        $this->semester_id = ($this->semesters->count() > 0) ? $this->semesters[0]->id : null;
        $this->setCurricula();
        $this->set_eaxm_dates();
        $this->set_subject_selections();
        $this->set_exam_name();
    }

    public function getSelectedState($code) {
        return $this->selected_subjects[$code];
    }

    public function updatedSpecializationId() {
        $this->specialization = $this->specializations->find($this->specialization_id);
        $this->setCurricula();
        $this->set_eaxm_dates();
        $this->set_subject_selections();
    }

    public function updatedExamCategory() {
        $this->set_exam_name();
    }

    public function updatedSemesterId() {
        $this->semester = $this->semesters->find($this->semester_id);
        $this->setCurricula();
        $this->set_eaxm_dates();
        $this->set_subject_selections();
    }

    public function updatedSelectedSubjects() {
        $this->selected_all_subjects = array_reduce($this->selected_subjects, function($carry, $z) {
            $carry = $carry & $z;
            return $carry;
        }, true);
    }

    public function updatedExamDates() {
        $this->doValidateExamDates();
        // $state = null;
        // foreach($this->exam_dates as $key => $ed) {
        //     $state = $this->validateDate($ed);
        //     if ($state & $this->vs_start_date & $this->vs_end_date) {
        //         $state = ($ed >= $this->start_date) & ($ed <= $this->end_date);
        //     }
        //     $this->vs_exam_dates[$key] = $state;
        // }
    }

    public function doValidateExamDates() {
        if ($this->start_date == null | $this->end_date == null) return;
        $this->vs_start_date = $this->validateDate($this->start_date);
        $this->vs_end_date = $this->validateDate($this->end_date);
        if ($this->start_date == false | $this->end_date == false) return;

        // start and end dates
        if ($this->start_date < $this->end_date) {
            $this->vs_start_date = true;
            $this->vs_end_date = true;
        }
        else {
            $this->vs_start_date = false;
            $this->vs_end_date = false;
        }

        // exam dates
        $state = null;
        foreach($this->exam_dates as $key => $ed) {
            if ($ed) {
                $state = $this->validateDate($ed);
                if ($state & $this->vs_start_date & $this->vs_end_date) {
                    $state = ($ed >= $this->start_date) & ($ed <= $this->end_date);
                }
                $this->vs_exam_dates[$key] = $state;
            }
        }
    }

    public function selected_subjects_count($subjects) {
        return array_reduce($subjects, function($carry, $z) {
            $carry += $z ? 1 : 0;
        }, 0);
    }

    public function updatedSelectedAllSubjects() {
        $this->selected_subjects = array_map( function() { return $this->selected_all_subjects; }, $this->selected_subjects);
    }

    // public function getFilteredCurriculaProperty() {
    //     if (!$this->curricula) return Collection::empty();
    //     $this->curricula = Curriculum::with(['subjects'])
    //         ->where('semester_id', $this->semester_id)
    //         ->where('specialization_id', $this->specialization_id)
    //         ->get();
    //     return $this->curricula;
    // }

    public function setCurricula() {
        $this->curricula = ($this->semester == null | $this->specialization == null) ? 
            Collection::empty() : 
            Curriculum::with(['subjects'])->where('specialization_id', $this->specialization->id)->where('semester_id', $this->semester->id)->get();
        // if ($this->regulation->id == 2) dd($this->regulation, $this->specialization, $this->semester, $this->curricula);
    }

    public function set_eaxm_dates() {
        $this->exam_dates = [];
        $this->vs_exam_dates = [];

        // fetch schedule if already done
        foreach($this->schedule as $sch) {
            if ($sch['specialization']['id'] == $this->specialization->id & $sch['semester']['id'] == $this->semester->id) {
                $this->exam_dates = $sch['schedule']['exam_dates'];
                $this->selected_subjects = $sch['schedule']['selected_subjects'];
                $this->selected_all_subjects = $sch['schedule']['count'] == count($sch['schedule']['selected_subjects']);
                $this->doValidateExamDates();
                return;
            }
        }

        // create empty schedule
        foreach($this->curricula as $c) {
            foreach($c->subjects as $s) {
                $this->exam_dates["$s->subject_code"] = ($this->start_date == null) ? null : $this->start_date;
                $this->vs_exam_dates["$s->subject_code"] = null;
            }
        }
    }

    public function set_subject_selections() {

        // skip if already scheduled
        // fetch schedule if already done
        foreach($this->schedule as $sch) 
            if ($sch['specialization']['id'] == $this->specialization->id & $sch['semester']['id'] == $this->semester->id) 
                return;


        $this->selected_all_subjects = true;
        $this->selected_subjects = [];
        if ($this->curricula == null) return;
        foreach($this->curricula as $c) {
            foreach($c->subjects as $s) {
                $this->selected_subjects[ "$s->subject_code"] = true;
            }
        }
    }

    public function set_exam_name() {
        if ($this->start_date == null) return;
        $this->exam_name = $this->regulation->program->short_name . '-' 
            . $this->regulation->short_name . '-' 
            . $this->exam_categories[$this->exam_category] . ' of ' 
            . date_format(DateTime::createFromFormat('Y-m-d', $this->start_date), 'M-Y');
    }

    public function mount() {

        // base data
        $this->regulations = Regulation::with('program')->get();
        $this->regulation = ($this->regulations->count() > 0) ? $this->regulations[0] : null;
        $this->specializations = ($this->regulation == null ) ? null : $this->regulation->program->specializations;
        $this->specialization = ($this->specializations->count() > 0) ? $this->specializations[0] : null;
        $this->semesters = ($this->regulation == null ) ? null : $this->regulation->semesters;
        $this->semester = ($this->semesters->count() > 0) ? $this->semesters[0] : null;

        // init curriculum
        $this->setCurricula();
        $this->set_eaxm_dates();
        $this->set_subject_selections();
        $this->academic_year = now()->year;
        $this->exam_short_name = $this->get_examination_code();

        $this->exam_categories['REGULAR'] = 'Regular End Examinations';
        $this->exam_categories['SUPPLEMENTARY'] = 'Supplementary End Examinations';
        $this->exam_categories['MIDTERM'] = 'Midterm Examinations';
    }

    public function render()
    {
        return view('livewire.examinations');
    }

    private function validateDate($date, $format = 'Y-m-d'){
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }

    private function get_random_string($len = 8) {
        $str = 'abcdefghijklmnopqrstuvwxyz';
        return substr(str_shuffle($str), 0, $len);
    }

    private function get_examination_code() {
        $unique = false;
        while (!$unique) {
            $code = $this->get_random_string();
            $n = Exam::where('short_name', $code)->get()->count();
            if ($n == 0) $unique = true; 
        }
        return $code;
    }
}
