<?php

namespace App\Http\Livewire;

use App\Models\EndExamMark;
use App\Models\Specialization;
use App\Models\Subject;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class SubjectProgression extends Component
{
    public $subject_id;
    public $title;
    public $labels;
    public $data;
    public $message;
    public $show_chart = true;

    protected $listeners = ['show_subject_progression' => 'show_graph'];

    public function show_graph($id) {
        $bg_colors = [
            'rgba(255, 99, 132, 0.2)',
            'rgba(54, 162, 235, 0.2)',
            'rgba(255, 206, 86, 0.2)',
            'rgba(75, 192, 192, 0.2)',
            'rgba(153, 102, 255, 0.2)',
            'rgba(255, 159, 64, 0.2)'
        ];

        $border_colors = [
            'rgba(255, 99, 132, 1)',
            'rgba(54, 162, 235, 1)',
            'rgba(255, 206, 86, 1)',
            'rgba(75, 192, 192, 1)',
            'rgba(153, 102, 255, 1)',
            'rgba(255, 159, 64, 1)'
        ];

        $this->subject_id = $id;
        $this->title = Subject::find($id)->name;
        $datasets = [];
        
        // end exam marks : all students
        $marks = EndExamMark::with('student')->where('subject_id', $id)->get(); //->pluck('end_exam_marks');
        $hist = [10 => 0, 20  => 0, 30  => 0, 40  => 0, 50  => 0, 60  => 0, 70  => 0, 80  => 0, 90  => 0, 100  => 0];
        foreach($marks as $m) {
            $bin = intval(ceil(($m->end_exam_marks == 0 ? 5 : $m->end_exam_marks/0.7)/10) * 10);
            $hist[$bin]++;
        }

        $count = $marks->count();
        foreach($hist as $key => $h) {
            $hist[$key] = round(100 * $h / $count, 2);
        }
        $this->labels = array_keys($hist);
        $this->data = array_values($hist);

        array_push($datasets, [
            'label' => 'All',
            'data' => array_values($hist),
            'backgroundColor' => $bg_colors[5],
            'borderColor' => $border_colors[5],
            'borderWidth' => 1
        ]);

        // by specialization
        $hists = [];
        $specialization_ids = $marks->pluck('student')->pluck('specialization_id')->unique();
        $specializations = Specialization::whereIn('id', $specialization_ids)->get();
        foreach($specialization_ids as $s) {
            $hists[$s] = [10 => 0, 20  => 0, 30  => 0, 40  => 0, 50  => 0, 60  => 0, 70  => 0, 80  => 0, 90  => 0, 100  => 0];
        }
        foreach($marks as $m) {
                $bin = intval(ceil(($m->end_exam_marks == 0 ? 5 : $m->end_exam_marks/0.7)/10) * 10);
                $hists[$m->student->specialization_id][$bin]++;
            }
        // dd($datasets, $specialization_ids, $hists);
        
        
        foreach($specializations as $key => $s) {
            array_push($datasets, [
                'label' => $s->short_name,
                'data' => array_values($hists[$s->id]),
                'backgroundColor' => $bg_colors[$key],
                'borderColor' => $border_colors[$key],
                'borderWidth' => 1
            ]);
        }
        // dd($datasets);
        $this->message = 'Percentage marks vs Percentage students ';
        $this->show_chart = false;
        $this->dispatchBrowserEvent('show_subject_progression_modal');
        $this->dispatchBrowserEvent('update_chart', ['labels' => $this->labels, 'datasets' => $datasets]);
        $this->show_chart = true;
    }

    public function render()
    {
        return view('livewire.subject-progression');
    }
}
