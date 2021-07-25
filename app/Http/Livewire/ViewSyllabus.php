<?php

namespace App\Http\Livewire;

use App\Models\Subject;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class ViewSyllabus extends Component
{

    protected $listeners = ['view_syllabus' => 'view_syllabus'];

    public $syllabus;
    public $syllabus_found;
    public $subject_name;
    public $subject_id;

    public function view_syllabus($subject_id) {

        $subject = Subject::find($subject_id);
        $subject_code = $subject->subject_code;
        $this->subject_id = $subject->id;
        $this->subject_name = $subject->name;
        if (Storage::disk('syllabus')->exists($subject_code . '.html')) {
            $this->syllabus = Storage::disk('syllabus')->get($subject_code . '.html');
            $this->syllabus_found = true;
        }
        else {
            $this->syllabus = '<p>Sorry. Syllabus could not be found.</p>';
            $this->syllabus_found = false;
            $this->subject_name = 'Not found';
        }
        
        $this->dispatchBrowserEvent('view_syllabus_modal');
        $this->emitTo('curriculum-reviews', 'get-stars', $subject_id);
    }

    // public function mount($subject_id) {
    //     $this->view_syllabus($subject_id);
    // }

    public function render()
    {
        return view('livewire.view-syllabus');
    }
}
