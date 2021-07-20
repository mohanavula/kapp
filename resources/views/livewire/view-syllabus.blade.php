<div>
    <!-- Modal -->
    <div wire:ignore.self class="modal fade" id="view-syllabus" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Syllabus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    {!! $syllabus !!}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script type="module">
        var el = document.getElementById('view-syllabus')
        var view_syllabus_modal = new bootstrap.Modal(el)
        window.addEventListener('view_syllabus_modal', () => {
            view_syllabus_modal.show()
        }) 
    </script>
</div>
