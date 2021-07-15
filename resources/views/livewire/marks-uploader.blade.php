<div>
    <div>
        <div class="h6">Marks uploader</div> 

        @forelse ($exams as $e)
            @if ($loop->first)
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th scope="col">Exam Code</th>
                            <th scope="col">Description</th>
                            <th scope="col">Subjects</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>

                    
            @endif
                        <tr>
                            <td>{{ $e->short_name}}</td>
                            <td>{{ $e->name}}</td>
                            <td>{{ $e->exam_schedules->count()}}</td>
                            <td class="d-flex justify-content-center" wire:click.prevent="show_modal( {{ $e->id }} )">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-upload" viewBox="0 0 16 16">
                                    <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/>
                                    <path d="M7.646 1.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 2.707V11.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708l3-3z"/>
                                </svg>
                            </td>
                        </tr>
                
            @if ($loop->last)
                    </tbody>
                </table>
            @endif
        @empty
            <div>
                Currently there are no active examinations
            </div>
        @endforelse
    </div>
    <!-- Modal -->
    <div wire:ignore.self class="modal fade" id="upload-marks" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" x-data>
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Upload marks [Exam code: {{ $exam ? $exam->short_name : '' }}]</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form wire:submit.prevent="process_upload">
                    <div class="modal-body">
                        <table class="table table-bordered table-sm">
                            <tbody>
                                <tr>
                                    <td>Exam</td>
                                    <td ><span class="text-truncate">{{ $exam ? $exam->name : '' }}</span></td>
                                </tr>
                                <tr>
                                    <td>Academic year</td>
                                    <td>{{ $exam ? $exam->academic_year : '' }}</td>
                                </tr>
                                <tr>
                                    <td>Regulation</td>
                                    <td>{{ $exam ? $exam->semester->regulation->short_name : '' }}</td>
                                </tr>
                                <tr>
                                    <td>Semester</td>
                                    <td>{{ $exam ? $exam->semester->name : '' }}</td>
                                </tr>
                                <tr>
                                    <td>Subjects</td>
                                    <td>{{ $exam ? $exam->exam_schedules->count() : '' }}</td>
                                </tr>
                                <tr>
                                    <td>Exam category</td>
                                    <td class="text-capitalize">{{ $exam ? $exam->exam_category : '' }}</td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="form-group" x-show="!$wire.close_modal">
                            <label for="datafile" class="form-label">Select data file (.csv)</label>
                            <input class="form-control" type="file" id="datafile" accept=".csv" wire:model='datafile'>
                        </div>
                        @if ($errors->any())
                            <div class="mt-2">
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
                        <div class="mt-2" x-show="!$wire.close_modal">
                            <button class="btn btn-success" type="submit">Upload</button>
                            <button class="btn btn-danger" type="reset" wire:click="reset_datafile">Cancel</button>
                        </div>

                        <div x-show="$wire.total_records" class="mt-2">
                            <table class="table table-bordered table-sm">
                                <tbody>
                                    <tr>
                                        <td>Total records</td>
                                        <td>{{ $total_records }}</td>
                                    </tr>
                                    <tr>
                                        <td>Valid records</td>
                                        <td>{{ $total_records ? $total_records - $invalid_records : ''}}</td>
                                    </tr>
                                    <tr>
                                        <td>Invalid records</td>
                                        <td>{{ $invalid_records}}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-success" wire:click.prevent="store" x-show="!$wire.close_modal">
                            <span class="spinner-border spinner-border-sm" role="status" wire:loading wire:target="store"></span>
                            Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script type="module">
        var el = document.getElementById('upload-marks')
        var upload_marks_modal = new bootstrap.Modal(el)
        window.addEventListener('show_upload_marks_modal', () => {
            upload_marks_modal.show()
        }) 
    </script>
</div>
