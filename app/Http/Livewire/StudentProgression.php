<?php

namespace App\Http\Livewire;

use App\Models\Student;
use Livewire\Component;
use PhpParser\Node\Stmt\Label;

class StudentProgression extends Component
{
    public $regdno;
    public $student;
    public $show_progression = false;

    public function get_progression() {
        
        $this->show_progression = false;
        $this->resetValidation();
        $this->student = '';
        $this->validate([
            'regdno' => 'required|exists:students,regdno'
        ]);


        $this->student = Student::with('aggregates')->firstWhere('regdno', $this->regdno);
        $labels = [];
        $data = [];
        foreach($this->student->aggregates as $a) {
            array_push($labels, $a->semester->short_name);
            array_push($data, $a->sgpa);
        }

        $datasets = [];
        array_push($datasets, [
            'label' => 'SGPA',
            'data' => $data,
            'borderColor' => 'rgb(75, 192, 192)',
            'borderWidth' => 1.5,
            'tension' => 0.2
        ]);
        $this->dispatchBrowserEvent('update_student_progression_chart', [
            'labels' => $labels,
            'datasets' => $datasets
        ]);
        $this->show_progression = true;
        
    }

    public function render()
    {
        return view('livewire.student-progression');
    }
}
