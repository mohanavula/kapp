@extends('layouts.app')

@section('content')
    <div class="card">
        <h3 class="card-header">Examinations Dashboard</h3>
        <div class="card-body">
            <div class="row">
                <div class="col">
                    Active Examinations

                </div>
                <div class="col">
                    Actions
                </div>
            </div>

            <!-- Nav tabs -->
            <ul class="nav nav-tabs" id="exams-nav" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="scheduler-tab" data-bs-toggle="tab" data-bs-target="#scheduler" type="button" role="tab">Schedules</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="registrations-tab" data-bs-toggle="tab" data-bs-target="#registrations" type="button" role="tab">Registrations</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="marks-tab" data-bs-toggle="tab" data-bs-target="#marks" type="button" role="tab">Marks</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="manage-tab" data-bs-toggle="tab" data-bs-target="#manage" type="button" role="tab">Manage</button>
                </li>
            </ul>
            
            <!-- Tab panes -->
            <div class="tab-content p-3">
                <div class="tab-pane fade show active" id="scheduler" role="tabpanel">
                    @livewire('exams-scheduler')
                </div>
                <div class="tab-pane fade" id="registrations" role="tabpanel">
                    Registrations tab here
                </div>
                <div class="tab-pane fade" id="marks" role="tabpanel">
                    @livewire('marks-uploader')
                </div>
                <div class="tab-pane fade" id="manage" role="tabpanel">
                    manage examinations
                </div>
            </div>                                                
        </div>
    </div>
@endsection