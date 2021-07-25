<?php

namespace App\Http\Livewire;

use App\Models\Regulation;
use App\Models\Review;
use App\Models\Specialization;
use App\Models\Subject;
use Carbon\Carbon;
use Livewire\Component;

class CurriculumReviews extends Component
{
    public $obj_type;
    public $obj_id;
    public $subject;

    public $stars;
    public $email;
    public $title;
    public $review;

    protected $listeners = ['get-stars' => 'fetch_subject_reviews'];

    public function fetch_subject_reviews($subject_id) {
        $this->obj_id = $subject_id;
        $this->subject = Subject::with('reviews')->firstWhere('id', $subject_id);
        
    }

    public function resetForm() {
        $this->stars = 0;
        $this->email = '';
        $this->title = '';
        $this->review = '';
        $this->resetValidation();
    }

    public function store() {
        $this->validate([
            'stars' => 'required|numeric|min:1',
            'email' => 'required|email',
            'title' => 'required',
            'review' => 'required',
        ]);

        Review::insert([
            'subject_id' => $this->obj_id,
            'category' => 'syllabus',
            'author_email' => $this->email,
            'stars' => $this->stars,
            'review' => $this->review,
            'updated_at' => now(),
            'created_at' => now()
        ]);
        $this->fetch_subject_reviews($this->obj_id);
        $this->resetForm();
    }

    // public function getReviewsProperty() {
    //     if (is_null($this->obj_type) | is_null($this->obj_id)) return null;

    //     if ($this->obj_type == 'subject') {
    //         return Subject::with('reviews')->firstWhere('id', $this->obj_id);
    //     }
    //     elseif ($this->obj_type == 'regulation') {
    //         return Regulation::with('reviews')->firstWhere('id', $this->obj_id);
    //     }
    //     else {
    //         return Specialization::with('reviews')->firstWhere('id', $this->obj_id);
    //     }
    // }

    // public function mount($obj_type, $obj_id) {
    //     $this->obj_type = $obj_type;
    //     $this->obj_id = $obj_id;
    // }

    public function render()
    {
        return view('livewire.curriculum-reviews');
    }
}
