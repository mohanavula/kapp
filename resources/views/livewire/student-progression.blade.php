<div x-data="{ showProgression: @entangle('show_progression') }">
    {{-- Success is as dangerous as failure. --}}
    <div>Students progression</div>
    <form wire:submit.prevent="get_progression">
        <div class="form-group">
            <label for="regdno" class="form-label">Student Register Number</label>
            <input class="form-control" type="text" id="regdno" wire:model.defer='regdno' style="width: 200px;"">
        </div>
        <div class="mt-2">
            <button class="btn btn-success" type="submit">Show</button>
        </div>
    </form>
    @if ($errors->any())
    <div>Register number not found</div>
    {{-- <div class="alert alert-danger alert-dismissible mt-2">
        <strong>Something's not good.</strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div> --}}
    @endif

    <div class="row mt-2" x-show="showProgression">
        <div class="col col-md-4">
            <table class="table table-bordered">
                <tr>
                    <td>Register number</td>
                    <td>{{ $student->regdno ?? '' }}</td>
                </tr>
                <tr>
                    <td>Name</td>
                    <td>{{ ($student->given_name ?? '') . ' ' . ($student->surname ?? '') }}</td>
                </tr>
                <tr>
                    <td>Email</td>
                    <td>{{ $student->email ?? ''}}</td>
                </tr>
                <tr>
                    <td>Phone</td>
                    <td>{{ $student->phone ?? ''}}</td>
                </tr>
                <tr>
                    <td>Join year</td>
                    <td>{{ $student->join_year ?? ''}}</td>
                </tr>
                <tr>
                    <td>Gender</td>
                    <td>{{ $student->gender ?? ''}}</td>
                </tr>
                <tr>
                    <td>Admission</td>
                    <td>{{ $student->admission_category ?? '' }}</td>
                </tr>
                <tr>
                    <td>Regulation</td>
                    <td>{{ $student->regulation->short_name ?? '' }}</td>
                </tr>
                <tr>
                    <td>Specialization</td>
                    <td>{{ $student->specialization->short_name ?? '' }}</td>
                </tr>
            </table>
            
        </div>
        <div class="col col-md-8">
            <div>
                <canvas id="progressionChart" class="p-4 m-4"></canvas>
            </div>
        </div>
    </div>
    <script src=" https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.4.1/chart.min.js"></script>
    <script type="module">
        let ctx = document.getElementById('progressionChart')
        window.studentProgressionChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: [],
                datasets: []
            },
            options: {
                scales: {
                    y: {
                        display: true,
                        title: {
                            display: true,
                            text: 'GPA'
                        }
                    },
                }
            }
        })

        window.addEventListener('update_student_progression_chart', (payload) => {
            window.studentProgressionChart.destroy()
            let ctx = document.getElementById('progressionChart')
            window.studentProgressionChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: payload.detail.labels,
                    datasets: payload.detail.datasets
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                            display: true,
                            title: {
                                display: true,
                                text: 'GPA'
                            }
                        },
                    }
                }
            })
            // window.studentProgressionChart.data.labels = payload.detail.labels
            // window.studentProgressionChart.data.datasets = payload.detail.datasets
            window.studentProgressionChart.update()
        })

    </script>

</div>
