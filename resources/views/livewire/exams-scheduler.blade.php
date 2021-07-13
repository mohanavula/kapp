<div x-data="{ isUploading: false, progress: 0, ir: @entangle('invalid_records') }"
    x-on:livewire-upload-start="isUploading = true"
    x-on:livewire-upload-finish="isUploading = false"
    x-on:livewire-upload-error="isUploading = false"
    x-on:livewire-upload-progress="progress = $event.detail.progress">
    <form wire:submit.prevent="process">
        <div class="d-flex align-items-end">
            <div class="form-group">
                <label class="form-label" for="academic-year">Academic Year</label>
                <div class="input-group">
                    <input type="text" class="form-control" id="academic-year" value="{{ $academic_year . '-' . ($academic_year + 1) }}" disabled>
                    <button type="button" class="btn  btn-outline-secondary" wire:click="update_academic_year(1)">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-lg" viewBox="0 0 16 16">
                            <path d="M8 0a1 1 0 0 1 1 1v6h6a1 1 0 1 1 0 2H9v6a1 1 0 1 1-2 0V9H1a1 1 0 0 1 0-2h6V1a1 1 0 0 1 1-1z"/>
                        </svg>
                    </button>
                    <button type="button" class="btn  btn-outline-secondary" wire:click="update_academic_year(-1)">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-dash-lg" viewBox="0 0 16 16">
                            <path d="M0 8a1 1 0 0 1 1-1h14a1 1 0 1 1 0 2H1a1 1 0 0 1-1-1z"/>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="form-group ms-2">
                <label class="form-label" for="exam-category">Select Exam Category</label>
                <select class="form-select" id="exam-category" wire:model="exam_category">
                    <option value="REGULAR" selected>Regular End Examinations</option>
                    <option value="SUPPLEMENTARY">Supplementary End Examinations</option>
                    <option value="MIDTERM">Midterm Examinations</option>
                </select>
            </div>
            <div class="form-group ms-2">
                <label for="datafile" class="form-label">Select data file (.csv)</label>
                <input class="form-control" type="file" id="datafile" accept=".csv" wire:model='datafile'>
            </div>
            <div>
                <button class="btn btn-success ms-2" type="submit">Upload</button>
                <button class="btn btn-danger ms-2" type="reset" x-show="ir > 0" wire:click="reset_datafile">Cancel</button>
            </div>
        </div>
        <div x-show="isUploading">
            <progress max="100" x-bind:value="progress"></progress>
        </div>
        @if ($errors->any())
            <div class="col mt-2">
                <div class="alert alert-danger alert-dismissible fade show">
                    <strong>Something's not good.</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    <ul class="list-group">
                        @foreach ($errors->all() as $error)
                            <li class="list-group-item">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        
    </form>

    <!-- validated data -->
    @if (count($schedule) > 0)
        <div class="my-3">
            <ul class="list-group list-group-horizontal-md">
                <li class="list-group-item flex-fill">{{ 'Academic year: ' . $academic_year . '-' . ($academic_year + 1) }}</li>
                <li class="list-group-item flex-fill">{{ 'Regulation: ' . $regulation_name }}</li>
                <li class="list-group-item flex-fill">{{ 'Semester: ' . $semester_number }}</li>
                <li class="list-group-item flex-fill">{{ 'Found: '. $records . ' records' }}</li>
                <li class="list-group-item flex-fill">{{ 'Valid: ' . count($schedule) . ' records' }}</li>
                <li class="list-group-item flex-fill">{{ 'Invalid: ' . ($records - count($schedule)) . ' records' }}</li>
              </ul>
        </div>
    
    @foreach ($schedule as $s)
        @if ($loop->first)
            <div class="row row-cols-1 row-cols-md-3 row-cols-lg-6 g-2">
        @endif
        <div class="col">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <h6 class="card-title me-auto">{{ $s['subject']['subject_code'] }}</h5>
                        <small>{{ $s['specialization_short_name'] }}</small>
                    </div>
                    <p class="card-text text-truncate" title="{{ $s['subject']['name'] }}">{{ $s['subject']['name'] }}</p>
                </div>
                <div class="card-footer">
                    <p class="card-text">{{ $s['schedule_date'] }}</p>
                </div>
            </div>
        </div>
        @if ($loop->last)
        </div>
        @endif

    @endforeach
    @endif
    <!-- errors -->
    @foreach ($validation_messages as $vm)
        @if ($loop->first)
            <div class="h6 my-3">Unfortunately there are some errors in the data.</div>
            <table class="table table-sm table-bordered">
                <thead>
                    <tr>
                        <th scope="col">Row No</th>
                        <th scope="col">Field</th>
                        <th scope="col">Message</th>
                    </tr>
                </thead>
                <tbody>
        @endif 
                <tr>
                    <td>{{ $vm['row_no'] }}</td>
                    <td>{{ $vm['field'] }}</td>
                    <td>{{ $vm['message'] }}</td>
                </tr>
        @if ($loop->last)
            </tbody>
            </table>
        @endif
    @endforeach

    @if (count($schedule) > 0)
        <div class="row justify-content-end mt-4">
            <div class="col">
                <button class="btn btn-success me-4" wire:click.prevenet="store">Make Schedule</button>
            </div>
        </div>
        
    @endif

</div>

