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
                    <div>
                        <span>{{ $message }}</span>
                        <span class="ms-2" style="cursor: pointer" x-on:click="downloadPlot()">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-download" viewBox="0 0 16 16">
                                <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/>
                                <path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z"/>
                              </svg>
                        </span>
                    </div>
                    <div>
                        <canvas id="myChart" class="p-4 m-4" x-show="showChart"></canvas>
                    </div>
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
            window.myChart.data.labels = payload.detail.labels
            window.myChart.data.datasets = payload.detail.datasets
            window.myChart.update()
        })
        
        window.downloadPlot = function () {
            var a = document.createElement('a');
            a.href = window.myChart.toBase64Image('image/png', 1);
            a.download = 'plot.png';
            a.click();
        }

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
                },
                animation: {
                    onComplete: function() {
                        
                    }
                }
            }
        });
    </script>
</div>
