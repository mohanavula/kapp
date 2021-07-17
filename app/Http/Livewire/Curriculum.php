<?php

namespace App\Http\Livewire;

use App\Models\Subject;
use App\Models\Curriculum as CurriculumModel;
use App\Models\Regulation;
use Illuminate\Support\Collection;
use Livewire\Component;


class Curriculum extends Component
{
    public $regulations = [];
    public Regulation $regulation;
    //public $curriculum;
    public $specializations;
    public $semesters;
    public $curricula;
    public $regulation_id = '';
    public $semester_id='';
    public $specialization_id='';

    public function show_subject_progression_modal($id) {
        $this->emit('show_subject_progression', $id);
    }

    public function selectRegulation($id) {
        $this->regulation_id = $id;
        $this->regulation = Regulation::find($id);
        $this->specializations = $this->regulation->program->specializations;
        $this->semesters = $this->regulation->semesters;

        $this->specialization_id = $this->specializations->count() > 0 ? $this->specializations[0]->id : null;
        $this->semester_id = $this->semesters->count() > 0 ? $this->semesters[0]->id : null;

        $this->getCurricula();
    }

    private function getCurricula() {
        $this->curricula = null;
        if ($this->specialization_id == null | $this->semester_id == null) return;
        $this->curricula = CurriculumModel::with(['subjects', 'subject_offering_type'])->whereIn('semester_id', $this->semesters->pluck('id'))->get();
        // $this->curricula = CurriculumModel::where('specialization_id', $this->specialization_id)
        //     ->where('semester_id', $this->semester_id)->get();
    }

    public function getFilteredCurriculaProperty() {
        if (!$this->curricula) return Collection::empty();
        return $this->curricula->filter(function($f) { 
            return ($f->specialization_id == $this->specialization_id) & ($f->semester_id == $this->semester_id);
        });
    }

    public function setSpecialization($specializaton_id) {
        $this->specialization_id = $specializaton_id;
        $this->getCurricula();
    }

    public function mount() {
        $this->regulations = Regulation::with('program')->get();
    }

    public function render()
    {
        return view('livewire.curriculum');
    }
}
