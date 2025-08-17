<x-filament-widgets::widget>
    <x-filament::section>
        <div class="space-y-6">
            <!-- Filters -->
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-bold tracking-tight">Employee Attendance Performance</h2>
                <div class="flex items-center space-x-2">
                    <select wire:model.live="filter" class="text-sm border-gray-300 rounded-lg shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                        @foreach ($this->getFilters() as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- First Row: Two Charts -->
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <!-- Work Hours Distribution Chart -->
                <div class="p-4 bg-white rounded-lg shadow">
                    <div
                        x-data="{
                            chart: null,
                            init() {
                                let options = {{ json_encode($this->getOptions()['work_hours']) }};
                                let data = {{ json_encode($this->getData()['charts']['work_hours']) }};

                                options.labels = data.labels;
                                options.series = data.datasets[0].data;

                                if (data.datasets[0].colors) {
                                    options.colors = data.datasets[0].colors;
                                }

                                this.chart = new ApexCharts($refs.workHoursChart, options);
                                this.chart.render();

                                $wire.on('filterChanged', () => {
                                    let allData = $wire.entangle('data').defer;
                                    let updatedData = allData.charts.work_hours;
                                    this.chart.updateOptions({
                                        labels: updatedData.labels
                                    });
                                    this.chart.updateSeries(updatedData.datasets[0].data);
                                });
                            }
                        }"
                        x-ref="workHoursChart"
                        wire:ignore
                        class="h-80"
                    ></div>
                </div>

                <!-- Clock-in Time Analysis Chart -->
                <div class="p-4 bg-white rounded-lg shadow">
                    <div
                        x-data="{
                            chart: null,
                            init() {
                                let options = {{ json_encode($this->getOptions()['clock_in_times']) }};
                                let data = {{ json_encode($this->getData()['charts']['clock_in_times']) }};

                                options.series = [{
                                    name: data.datasets[0].name,
                                    data: data.datasets[0].data
                                }];
                                options.xaxis.categories = data.labels;

                                if (data.datasets[0].color) {
                                    options.colors = [data.datasets[0].color];
                                }

                                this.chart = new ApexCharts($refs.clockInChart, options);
                                this.chart.render();

                                $wire.on('filterChanged', () => {
                                    let allData = $wire.entangle('data').defer;
                                    let updatedData = allData.charts.clock_in_times;
                                    this.chart.updateOptions({
                                        xaxis: {
                                            categories: updatedData.labels
                                        }
                                    });
                                    this.chart.updateSeries([{
                                        name: updatedData.datasets[0].name,
                                        data: updatedData.datasets[0].data
                                    }]);
                                });
                            }
                        }"
                        x-ref="clockInChart"
                        wire:ignore
                        class="h-80"
                    ></div>
                </div>
            </div>

            <!-- Second Row: Two Charts -->
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <!-- Attendance Completion Rate Chart -->
                <div class="p-4 bg-white rounded-lg shadow">
                    <div
                        x-data="{
                            chart: null,
                            init() {
                                let options = {{ json_encode($this->getOptions()['completion_rate']) }};
                                let data = {{ json_encode($this->getData()['charts']['completion_rate']) }};

                                options.series = data.datasets.map(dataset => ({
                                    name: dataset.name,
                                    data: dataset.data,
                                    color: dataset.color
                                }));
                                options.xaxis.categories = data.labels;

                                this.chart = new ApexCharts($refs.completionRateChart, options);
                                this.chart.render();

                                $wire.on('filterChanged', () => {
                                    let allData = $wire.entangle('data').defer;
                                    let updatedData = allData.charts.completion_rate;
                                    this.chart.updateOptions({
                                        xaxis: {
                                            categories: updatedData.labels
                                        }
                                    });
                                    this.chart.updateSeries(updatedData.datasets.map(dataset => ({
                                        name: dataset.name,
                                        data: dataset.data
                                    })));
                                });
                            }
                        }"
                        x-ref="completionRateChart"
                        wire:ignore
                        class="h-80"
                    ></div>
                </div>

                <!-- Top Employees Chart -->
                <div class="p-4 bg-white rounded-lg shadow">
                    <div
                        x-data="{
                            chart: null,
                            init() {
                                let options = {{ json_encode($this->getOptions()['top_employees']) }};
                                let data = {{ json_encode($this->getData()['charts']['top_employees']) }};

                                options.series = [{
                                    name: data.datasets[0].name,
                                    data: data.datasets[0].data
                                }];
                                options.xaxis.categories = data.labels;

                                if (data.datasets[0].color) {
                                    options.colors = [data.datasets[0].color];
                                }

                                this.chart = new ApexCharts($refs.topEmployeesChart, options);
                                this.chart.render();

                                $wire.on('filterChanged', () => {
                                    let allData = $wire.entangle('data').defer;
                                    let updatedData = allData.charts.top_employees;
                                    this.chart.updateOptions({
                                        xaxis: {
                                            categories: updatedData.labels
                                        }
                                    });
                                    this.chart.updateSeries([{
                                        name: updatedData.datasets[0].name,
                                        data: updatedData.datasets[0].data
                                    }]);
                                });
                            }
                        }"
                        x-ref="topEmployeesChart"
                        wire:ignore
                        class="h-80"
                    ></div>
                </div>
            </div>

            <!-- Summary Stats Cards -->
            <div class="grid grid-cols-1 gap-4 mt-4 md:grid-cols-2 lg:grid-cols-4">
                <div class="p-4 bg-white rounded-lg shadow">
                    <h3 class="text-sm font-medium text-gray-500">Average Daily Attendance Rate</h3>
                    <div class="flex items-baseline mt-1">
                        <span class="text-2xl font-semibold text-primary-600">
                            {{ number_format(collect($this->getData()['charts']['completion_rate']['datasets'][0]['data'])->avg(), 1) }}%
                        </span>
                    </div>
                </div>

                <div class="p-4 bg-white rounded-lg shadow">
                    <h3 class="text-sm font-medium text-gray-500">Early Arrivals</h3>
                    <div class="flex items-baseline mt-1">
                        <span class="text-2xl font-semibold text-primary-600">
                            {{ $this->getData()['charts']['clock_in_times']['datasets'][0]['data'][0] + $this->getData()['charts']['clock_in_times']['datasets'][0]['data'][1] }}
                        </span>
                        <span class="ml-1 text-sm text-gray-500">employees</span>
                    </div>
                </div>

                <div class="p-4 bg-white rounded-lg shadow">
                    <h3 class="text-sm font-medium text-gray-500">Full Workday (8+ hrs)</h3>
                    <div class="flex items-baseline mt-1">
                        <span class="text-2xl font-semibold text-primary-600">
                            {{ $this->getData()['charts']['work_hours']['datasets'][0]['data'][3] + $this->getData()['charts']['work_hours']['datasets'][0]['data'][4] }}
                        </span>
                        <span class="ml-1 text-sm text-gray-500">records</span>
                    </div>
                </div>

                <div class="p-4 bg-white rounded-lg shadow">
                    <h3 class="text-sm font-medium text-gray-500">Incomplete Records</h3>
                    <div class="flex items-baseline mt-1">
                        <span class="text-2xl font-semibold text-primary-600">
                            {{ round(collect($this->getData()['charts']['completion_rate']['datasets'][1]['data'])->avg(), 1) }}%
                        </span>
                        <span class="ml-1 text-sm text-gray-500">avg</span>
                    </div>
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
