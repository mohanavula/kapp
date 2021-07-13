@extends('layouts.app')

@section('content')
    <div class="card">
        <h3 class="card-header">Examinations</h3>
        <div class="card-body">
            
            @livewire('exams-scheduler')
        </div>
    </div>
@endsection