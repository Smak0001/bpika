<x-app-layout>
    <x-index-with-actions>
        <x-slot name="actions">
            <x-label style="font-size: 3vh; padding-top: 10px; color: black">{{ __('Code') }}</x-label><span style="font-size: 2vh; padding-top: 10px; color: black">{{ $station->code }}</span>
            <x-label style="font-size: 3vh; padding-top: 10px; color: black">{{ __('Name') }}</x-label><span style="font-size: 2vh; padding-top: 10px; color: black">{{ $station->name }}</span>
            <x-label style="font-size: 3vh; padding-top: 10px; color: black">{{ __('City') }}</x-label><span style="font-size: 2vh; padding-top: 10px; color: black">{{ $station->city }}</span>
            <x-label style="font-size: 3vh; padding-top: 10px; color: black">{{ __('Timezone') }}</x-label><span style="font-size: 2vh; padding-top: 10px; color: black">{{ $station->timezone }}</span>
            {{-- Divider --}}
            <div class="border-t border-gray-100 w-full mb-3"></div>
            <x-label style="font-size: 3vh; padding-top: 10px; color: deepskyblue">{{ __('PET') }}</x-label>
            <x-label style="font-size: 2vh; padding-top: 10px; color: deepskyblue"><span id="pet-data-span"></span> °C</x-label>
            <x-label style="font-size: 3vh; padding-top: 20px; color: blue">{{ __('Temperature') }}</x-label>
            <x-label style="font-size: 2vh; padding-top: 10px; color: blue">{{ $station->measurements[count($station->measurements) - 1]->th_temp}} °C</x-label>
            <x-label style="font-size: 3vh; padding-top: 20px; color: darkblue">{{ __('Humidity') }}</x-label>
            <x-label style="font-size: 2vh; padding-top: 10px; color: darkblue">{{ $station->measurements[count($station->measurements) - 1]->th_hum}}%</x-label>
            <x-label style="font-size: 3vh; padding-top: 20px; color: black">{{ __('Wind direction') }}:</x-label>
            <img style="transform: rotate({{ $station->measurements[count($station->measurements) - 1]->wind_dir}}deg)" src="/img/arrow.png" style>
        </x-slot>


        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-3">
            <div class="p-6 text-gray-900" style="width: 75%; height: 75%">
                <canvas class="mb-3" id="PET_chart"></canvas>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900" style="width: 75%; height: 75%">
                <canvas class="mb-3" id="New_chart"></canvas>
            </div>
        </div>


    </x-index-with-actions>
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@^3"></script>
        <script src="https://cdn.jsdelivr.net/npm/luxon@^2"></script>
        <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-luxon@^1"></script>
        <script>

            const chartId = 'PET_chart';
            const station = '{{ $station->code }}';
            const ctx = document.getElementById('PET_chart');
            const ctxHumidityChart = document.getElementById('New_chart');

            const config = {
                type: 'line',
                data: {},
                responsive: true,
                maintainAspectRatio: false,
                options: {
                    scales: {
                        x: {
                            type: 'time',
                            time: {
                                parser: 'M/dd/yyyy H:mm:ss',
                                tooltipFormat: 'H:mm',
                                unit: 'day'
                            },
                        }
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'right',
                            align: 'start'
                        },
                        tooltip: {
                            callbacks: {
                                title: function(tooltipItem) {
                                    return tooltipItem[0].dataset.label;
                                },
                                label: function(tooltipItem) {
                                    return tooltipItem.label + " : " + Math.round(tooltipItem.formattedValue * 10) / 10 + "°C";
                                }
                            }
                        }
                    }
                }
            };

            const configHumidityChart = {
                type: 'line',
                data: {},
                options: {
                    scales: {
                        x: {
                            type: 'time',
                            time: {
                                parser: 'M/dd/yyyy H:mm:ss',
                                tooltipFormat: 'H:mm',
                                unit: 'day'
                            },
                        }
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'right',
                            align: 'start'
                        },
                        tooltip: {
                            callbacks: {
                                title: function(tooltipItem) {
                                    return tooltipItem[0].dataset.label;
                                },
                                label: function(tooltipItem) {
                                    return tooltipItem.label + " : " + Math.round(tooltipItem.formattedValue * 10) / 10 + "%";
                                }
                            }
                        }
                    }
                }
            };

            const myChart = new Chart(ctx, config);

            const humidityChart = new Chart(ctxHumidityChart, configHumidityChart);

            let petTemp = 0;
            const petDataSpan = document.getElementById('pet-data-span');

            loadData = function(data) {
                if (data.column == 'pet') {
                    petTemp = data.data[data.data.length - 1].y;
                    petTemp = (Math.round(petTemp * 10)) / 10;
                    petDataSpan.innerText = petTemp;
                }
                myChart.data.datasets.push({
                    label: data.column,
                    borderColor: data.column == 'pet' ? '#2ea8db' : '#064e6c',
                    data: data.data
                });
                myChart.update();
            }

            loadDataNewChart = function(data) {
                humidityChart.data.datasets.push({
                    label: data.column,
                    borderColor: data.column == 'pet' ? '#2ea8db' : '#064e6c',
                    data: data.data
                });
                humidityChart.update();
            }

            addEventListener('load', function() {
                try {
                    let today = new Date();
                    let sevenDaysAgo = new Date();
                    sevenDaysAgo.setDate(today.getDate() - 7);
                    let timeString = sevenDaysAgo.toISOString();
                    let url = `/api/stations/${station}/measurements?startDate=${timeString}&grouping=hourly&column=th_temp`;

                    fetch(url)
                        .then(response => response.text())
                        .then(text => loadData(JSON.parse(text)));
                    url = `/api/stations/${station}/measurements?startDate=${timeString}&grouping=hourly&column=pet`;

                    fetch(url)
                        .then(response => response.text())
                        .then(text => loadData(JSON.parse(text)));
                } catch (error) {
                    console.error(`Download error: ${error.message}`);
                }

                try {
                    let today = new Date();
                    let sevenDaysAgo = new Date();
                    sevenDaysAgo.setDate(today.getDate() - 7);
                    let timeString = sevenDaysAgo.toISOString();
                    let url = `/api/stations/${station}/measurements?startDate=${timeString}&grouping=hourly&column=th_hum`;

                    fetch(url)
                        .then(response => response.text())
                        .then(text => loadDataNewChart(JSON.parse(text)));
                } catch (error) {
                    console.error(`Download error: ${error.message}`);
                }

            });

        </script>
    @endpush

</x-app-layout>
