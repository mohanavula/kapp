<div x-data="{ showChart: @entangle('show_chart')}">
    <!-- Modal -->
    <div wire:ignore.self class="modal fade" id="subject-progression" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $title }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    {{ $message }}
                    <canvas id="myChart" class="p-4 m-4" x-show="showChart"></canvas>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    
    <script src=" https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.4.1/chart.min.js"></script>
    <script type="module">
        var el = document.getElementById('subject-progression')
        var subject_progression_modal = new bootstrap.Modal(el)
        window.addEventListener('show_subject_progression_modal', () => {
            subject_progression_modal.show()
        })

        window.addEventListener('update_chart', (payload) => {
            console.log(payload)
            window.myChart.data.labels = payload.detail.labels
            window.myChart.data.datasets = payload.detail.datasets
            window.myChart.update()
        }) 

        var ctx = document.getElementById('myChart');
        window.myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: [],
                datasets: []
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        display: true,
                        title: {
                            display: true,
                            text: 'Percentage Students',
                            // color: 'rgba(255, 159, 64, 1)'
                        }
                    },

                    // x: {
                    //     display: true,
                    //     title: {
                    //         display: true,
                    //         text: 'Percentage Marks',
                    //         color: 'rgba(255, 159, 64, 1)'
                    //     }
                    // }
                }
            }
        });
    </script>
</div>
