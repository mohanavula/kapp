<?php

namespace App\Http\Livewire;

use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class ViewSyllabus extends Component
{

    protected $listeners = ['view_syllabus' => 'view_syllabus'];

    public $syllabus;

    public function view_syllabus($sid) {
        $this->syllabus = Storage::disk('syllabus')->get('1521101.html');
        $this->dispatchBrowserEvent('view_syllabus_modal');
    }

    public function render()
    {
        return view('livewire.view-syllabus');
    }
}
