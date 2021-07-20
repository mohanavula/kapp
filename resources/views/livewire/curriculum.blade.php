<div>
    @forelse($regulations as $r)
        @if ($loop->first)
            <div class="lead">Programs</div>
            <div class="card-group" x-data="{ selectedRegulation: @entangle('regulation_id') }">
        @endif
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">{{ $r->program->short_name . " (" . $r->short_name . ")" }}</h5>
                        <p class="card-text">{{ $r->name }}</p>
                        <button wire:click="selectRegulation({{ $r->id }})" type="button" class="btn" x-bind:class="selectedRegulation == {{ $r->id }} ? 'btn-danger' : 'btn-outline-danger'">View</button>
                    </div>
                    <div class="card-footer">
                        <p class="card-text"><small class="text-muted">{{ 'Semesters: ' . $r->total_semesters . '  ' . 'Credits: ' . $r->total_credits  }}</small></p>
                    </div>
                </div>
        @if ($loop->last)
            </div>
        @endif
    @empty
        <div class="alert alert-warning my-4" role="alert">
            Sorry. Nothing to show yet.
        </div>
    @endforelse

    <!--  Explorer -->
    @if ($regulation)
    <div x-data="{ selectedSpecialiation: @entangle('specialization_id'), selectedSemester: @entangle('semester_id')}">
      <div class="row ms-2 my-4">
        <a class="text-decoration-none border-bottom border-1 col mx-2" data-bs-toggle="collapse" href="#about" role="button">
          About
        </a>
        <a class="text-decoration-none border-bottom border-1 col mx-2" data-bs-toggle="collapse" href="#regulations" role="button">
          Regulations
        </a>
        <a class="text-decoration-none border-bottom border-1 col mx-2" data-bs-toggle="collapse" href="#curriculum" role="button">
          Curriculum
        </a>
        <a class="text-decoration-none border-bottom border-1 col mx-2" data-bs-toggle="collapse" href="#syllabus" role="button">
          Subjects & Syllabus
        </a>
      </div >
      <div class="collapse my-2" id="about" wire:ignore.self>
        <div class="card card-body">
          About the regulation
        </div>
      </div>
      <div class="collapse my-2" id="regulations" wire:ignore.self>
        <div class="card card-body">
          Regulations
        </div>
      </div>
      
      <!-- curriculum -->
      <div class="collapse my-2" id="curriculum" wire:ignore.self>
        <div class="card card-body">
          <div class="row">
            <div class="col">
              Specialization
              <div class="btn-group" role="group">
                @forelse ($specializations as $s)
                  <button type="button" class="btn" x-bind:class="selectedSpecialiation == {{ $s->id }} ? 'btn-danger' : 'btn-outline-danger'" wire:click="$set('specialization_id', {{ $s->id }})">{{ $s->short_name }}</button>
                @empty
                  <div>Sorry. Nothing to show.</div>
                @endforelse
              </div>
            </div>
            <div class="col">
              Semester
              <div class="btn-group" role="group">
                @forelse ($semesters as $s)
                  <button type="button" class="btn" x-bind:class="selectedSemester == {{ $s->id }} ? 'btn-danger' : 'btn-outline-danger'" wire:click="$set('semester_id', {{ $s->id }})">{{ $s->semester_number }}</button>
                @empty
                  <div>Sorry. Nothing to show.</div>
                @endforelse
              </div>
            </div>
          </div>
          @forelse ($this->filteredCurricula as $c)
              @if ($loop->first)
                  <table class="table table-sm table-borderless table-striped table-hover caption-top my-4">
                      <caption>{{ $this->specializations->find($specialization_id)->name  . ' - ' . $this->semesters->find($semester_id)->name}}</caption>
                      <thead class="table-light">
                          <tr>
                              <th scope="col">Code</th>
                              <th scope="col">Title</th>
                              <th scope="col">L-T-P</th>
                              <th scope="col">IM</th>
                              <th scope="col">EM</th>
                              <th scope="col">Credits</th>
                              <th scope="col">Type</th>
                              <th></th>
                          </tr>
                      </thead>
                      <tbody>
              @endif
                          <tr>
                              <td>
                                @foreach ($c->subjects as $s)
                                  <div>{{ $s->subject_code }}</div>
                                @endforeach
                              </td>
                              <td>
                                @foreach ($c->subjects as $s)
                                  <div>{{ $s->name }}</div>
                                @endforeach
                              </td>
                              {{-- <td>{{ $this->subject_title($c->id) }}</td> --}}
                              {{-- <td>{{ $c->subjects[0]->name }}</td> --}}
                              <td>{{ $c->lectures . '-' . $c->tutorials . '-' . $c->practicals }}</td>
                              <td>{{ $c->internal_exam_marks }}</td>
                              <td>{{ $c->end_exam_marks }}</td>
                              <td>{{ $c->credits }}</td>
                              <td>{{ $c->subject_offering_type->description }}</td>
                              <td>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-graph-up me-2" viewBox="0 0 16 16" style="cursor: pointer" wire:click="show_subject_progression_modal({{ $s->id }})" title="View performance">
                                  <path fill-rule="evenodd" d="M0 0h1v15h15v1H0V0zm10 3.5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 .5.5v4a.5.5 0 0 1-1 0V4.9l-3.613 4.417a.5.5 0 0 1-.74.037L7.06 6.767l-3.656 5.027a.5.5 0 0 1-.808-.588l4-5.5a.5.5 0 0 1 .758-.06l2.609 2.61L13.445 4H10.5a.5.5 0 0 1-.5-.5z"/>
                                </svg>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-earmark-text" viewBox="0 0 16 16" style="cursor: pointer" wire:click="view_syllabus({{ $s->id }})" title="View syllabus">
                                  <path d="M5.5 7a.5.5 0 0 0 0 1h5a.5.5 0 0 0 0-1h-5zM5 9.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 0 1h-2a.5.5 0 0 1-.5-.5z"/>
                                  <path d="M9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4.5L9.5 0zm0 1v2A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5z"/>
                                </svg>

                              </td>
                          </tr>
      
              @if ($loop->last)
                      </tbody>
                  </table>
              @endif
          @empty
              <div class="alert alert-warning my-4" role="alert">
                  Sorry. Nothing to show yet.
              </div>
          @endforelse
        </div>
      </div>
      
      <!-- Syllabus -->
      <div class="collapse my-2" id="syllabus" wire:ignore.self>
        <div class="card card-body">
          Syllabus
        </div>
      </div>
    </div>
    @endif

    @livewire('subject-progression') 
    @livewire('view-syllabus') 
</div>
