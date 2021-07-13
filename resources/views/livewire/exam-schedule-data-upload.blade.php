<div x-data="{ isUploading: false, progress: 0, abort: @entangle('abort'), showFeedback: @entangle('show_feedback') }"
    x-on:livewire-upload-start="isUploading = true"
    x-on:livewire-upload-finish="isUploading = false"
    x-on:livewire-upload-error="isUploading = false"
    x-on:livewire-upload-progress="progress = $event.detail.progress">
    <form wire:submit.prevent="process">
        <div class="mb-3">
            <label for="datafile" class="form-label">Select data file (.csv)</label>
            <input class="form-control" type="file" id="datafile" accept=".csv" wire:model='datafile'>
        </div>
        <div x-show="isUploading">
            <progress max="100" x-bind:value="progress"></progress>
        </div>
        @error('datafile')
        <div class="alert alert-danger" role="alert">
            {{ $message }}
        </div> 
        @enderror
        
        <button class="btn btn-success" type="submit">Upload Schedule</button>

        <div x-show="showFeedback">
            <div x-show="abort">{{ $abort_message }}</div>
            <div class="h4">Processing... @if($abort) {{'Aborted'}} @endif</div>
            @foreach ($feedback_messages as $fm)
                <div>{{$fm['row_no']}}</div>
                <div>{{$fm['message']}}</div>
            @endforeach

        </div>
    </form>
</div>
