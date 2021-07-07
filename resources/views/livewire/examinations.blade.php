<div style="position: relative" x-data="{ 
        selectedSubjects: @entangle('selected_subjects'), 
        vsStartDate: @entangle('vs_start_date'), 
        vsEndDate: @entangle('vs_end_date'), 
        vsExamDates: @entangle('vs_exam_dates'), 
        bags: @entangle('bags'), 
    }">
    <div>Create examinations schedule</div>
    <div class="row my-2 g-2">
        <div class="col-md">
            @forelse ($regulations as $r)
                @if ($loop->first)
                <div class="form-floating">
                    <select class="form-select" id="regulation" wire:model="regulation_id" x-bind:disabled="bags > 0">
                @endif
                    <option value="{{ $r->id }}">{{ $r->program->short_name . " - " . $r->short_name}}</option>
                @if ($loop->last)
                    </select>
                    <label for="regulation">Select Regulation</label>
                </div>
                @endif
            @empty
                <div class="form-floating">
                    <select class="form-select" id="regulation" disabled>
                        <option>Regulations data not available ...</option>
                    </select>
                    <label for="regulation">Select Regulation</label>
                </div>
            @endforelse
        </div>
        <div class="col-md">
            @forelse ($specializations as $s)
                @if ($loop->first)
                <div class="form-floating">
                    <select class="form-select" id="specialization" wire:model="specialization_id">
                @endif
                    <option value="{{ $s->id }}">{{ $s->name}}</option>
                @if ($loop->last)
                    </select>
                    <label for="regulation">Select Specialization</label>
                </div>
                @endif
            @empty
            <div class="form-floating">
                <select class="form-select" id="specialization" disabled>
                    <option>Specializations data not available ...</option>
                </select>
                <label for="specialization">Select Regulation</label>
            </div>
            @endforelse
        </div>
        <div class="col-md">
            @forelse ($semesters as $s)
                @if ($loop->first)
                <div class="form-floating">
                    <select class="form-select" id="semester" wire:model="semester_id">
                @endif
                    <option value="{{ $s->id }}">{{ $s->name}}</option>
                @if ($loop->last)
                    </select>
                    <label for="regulation">Select Semester</label>
                </div>
                @endif
            @empty
            <div class="form-floating">
                <select class="form-select" id="semester" disabled>
                    <option>Semesters data not available ...</option>
                </select>
                <label for="semester">Select Regulation</label>
            </div>
            @endforelse
        </div>
    </div>

    <div class="row my-2 g-2">
        <div class="col-md">
            <div class="d-flex ">
                <div class=" input-group">
                    <div class="form-floating flex-grow-1">
                        <input type="text" class="form-control" id="academic-year" value="{{ $academic_year . '-' . ($academic_year + 1) }}" disabled>
                        <label for="academic-year">Academic Year</label>
                    </div>
                    <button type="button" class="btn  btn-outline-secondary" wire:click="update_academic_year(1)" x-bind:disabled="bags > 0">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-lg" viewBox="0 0 16 16">
                            <path d="M8 0a1 1 0 0 1 1 1v6h6a1 1 0 1 1 0 2H9v6a1 1 0 1 1-2 0V9H1a1 1 0 0 1 0-2h6V1a1 1 0 0 1 1-1z"/>
                        </svg>
                    </button>
                    <button type="button" class="btn  btn-outline-secondary" wire:click="update_academic_year(-1)" x-bind:disabled="bags > 0">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-dash-lg" viewBox="0 0 16 16">
                            <path d="M0 8a1 1 0 0 1 1-1h14a1 1 0 1 1 0 2H1a1 1 0 0 1-1-1z"/>
                        </svg>
                    </button>
                </div>
                <div>
                </div>
            </div>
        </div>
        <div class="col-md">
            <div class="form-floating">
                <input type="date" class="form-control" x-bind:class="vsStartDate == false ? 'border-danger' : '' " id="start_date" wire:model="start_date">
                <label for="academic-year">Start Date</label>
            </div>
        </div>
        <div class="col-md">
            <div class="form-floating">
                <input type="date" class="form-control" x-bind:class="vsEndDate == false ? 'border-danger' : '' " id="end_date" wire:model="end_date">
                <label for="academic-year">End Date</label>
            </div>
        </div>
    </div>

    <div class="row my-2 g-2">
        <div class="col-md">
            <div class="col-md">
                <div class="form-floating">
                    <select class="form-select" id="exam-category" wire:model="exam_category" x-bind:disabled="bags > 0">
                        <option value="REGULAR">Regular End Examinations</option>
                        <option value="SUPPLEMENTARY">Supplementary End Examinations</option>
                        <option value="MIDTERM">Midterm Examinations</option>
                    </select>
                    <label for="exam-category">Select Exam Category</label>
                </div>
            </div>
        </div>
        <div class="col-md">
            <div class="form-floating">
                <input type="text" class="form-control" id="short-name" wire:model="exam_short_name" disabled>
                <label for="short-name">Examination Code</label>
            </div>
        </div>
        <div class="col-md">
            <div class="form-floating">
                <input type="text" class="form-control" id="exam-name" wire:model="exam_name" disabled>
                <label for="exam-name">Examination Name</label>
            </div>
        </div>
    </div>

    <!-- bags -->
    <div class="my-4">
    @foreach ($schedule as $key => $s)
            @if ($loop->first)
            <ul class="list-group list-group-horizontal">
            @endif
                <li class="list-group-item">
                    <div><span class="rounded-pill badge bg-light text-dark"><small class="text-muted">{{ $s['schedule']['count'] }}</span> subjects</small></div>
                    <div><h4>{{$s['specialization']['short_name'] }}</h4></div>
                    <div class="text-muted"><small>{{ $s['semester']['name'] }}</small></div>
                    <div class="bg-light ">
                        <button class="col btn btn-sm float-end" wire:click.prevent="delete_bag({{ $key }})">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                                <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
                                <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
                            </svg>
                        </button>
                        {{-- <button class="btn btn-sm btn-outline-danger" wire:click.prevent="view_bag({{ $key }})">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-return-left" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M14.5 1.5a.5.5 0 0 1 .5.5v4.8a2.5 2.5 0 0 1-2.5 2.5H2.707l3.347 3.346a.5.5 0 0 1-.708.708l-4.2-4.2a.5.5 0 0 1 0-.708l4-4a.5.5 0 1 1 .708.708L2.707 8.3H12.5A1.5 1.5 0 0 0 14 6.8V2a.5.5 0 0 1 .5-.5z"/>
                            </svg>
                        </button> --}}
                        
                    </div>
                </li>
            @if ($loop->last)
            </ul>
            @endif
            
    @endforeach
    </div>

    <!-- Exam schedule -->
    <div>
        <button class="btn btn-outline-danger" wire:click="add_to_schedule">Add</button>
    </div>
    @forelse ($curricula ? $curricula : [] as $c)
        @if ($loop->first)
        <table class="table table-sm table-borderless table-striped caption-top my-4">
            <caption>Examination Schedule</caption>
            <thead class="table-light">
                <tr>
                    <th scope="col"><input class="form-check-input" type="checkbox" value="1" wire:model="selected_all_subjects"></th>
                    <th scope="col">Code</th>
                    <th scope="col">Title</th>
                    <th scope="col">IM</th>
                    <th scope="col">EM</th>
                    <th scope="col">Credits</th>
                    <th scope="col">Exam Date</th>
                </tr>
            </thead>
            <tbody>
        @endif
            @foreach ($c->subjects ? $c->subjects : [] as $s)
                <tr>
                    <td><input class="form-check-input" type="checkbox" 
                        value="{{ $selected_subjects[$s->subject_code] }}" 
                        wire:model="selected_subjects.{{ $s->subject_code }}">
                    </td>
                    <td>{{ $s->subject_code }}</td>
                    <td>{{ $s->name }}</td>
                    <td>{{ $c->internal_exam_marks }}</td>
                    <td>{{ $c->end_exam_marks }}</td>
                    <td>{{ $c->credits }}</td>
                    <td><input class="form-control" 
                        x-bind:disabled="!selectedSubjects['{{ $s->subject_code }}']" 
                        x-bind:class="vsExamDates['{{ $s->subject_code }}'] == false ? 'form-control border-danger' : 'form-control' "
                        type="date" name="exam-date" id="exam-date" 
                        value="{{ $exam_dates[$s->subject_code] }}" 
                        wire:model="exam_dates.{{ $s->subject_code }}">
                    </td>
                </tr>
            @endforeach
        @if ($loop->last)
            </tbody>
        </table>
        @endif
    @empty
        
    @endforelse



    <!-- toast -->
    <div class="toast position-fixed top-0 end-0 m-4 bg-light" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
          <strong class="me-auto">Examinations</strong>
          <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
          {{ $toast_message }}
        </div>
      </div>

      <script type="module">
            var el = document.querySelector(".toast")
            var toast = new bootstrap.Toast(el)
            window.addEventListener("showToast", () => {
                toast.show()
            });
      </script>
</div>
