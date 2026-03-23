<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col lg:flex-row justify-between lg:items-center gap-4">
            <h2 class="font-semibold text-xl text-red-600 leading-tight">System Analytics & Reports</h2>

            <div class="flex flex-col md:flex-row items-center gap-4">
                <div class="flex items-center gap-2 md:border-r md:pr-4 border-gray-300">
                    <a href="{{ route('admin.reports.print', ['timeframe' => $timeframe, 'start_date' => request('start_date'), 'end_date' => request('end_date')]) }}"
                        target="_blank"
                        class="px-3 py-1.5 bg-gray-800 text-white text-sm font-bold rounded hover:bg-gray-700 transition flex items-center gap-1 shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                        Open Report
                    </a>
                </div>

                <form method="GET" action="{{ route('admin.reports.index') }}" class="flex items-center gap-2">
                    <label
                        class="text-sm font-bold text-gray-700 uppercase tracking-wide hidden sm:block">Filter:</label>
                    <select name="timeframe" id="timeframeSelect" onchange="toggleCustomDates()"
                        class="border-gray-300 rounded text-sm py-1 font-bold focus:ring-red-500 shadow-sm">
                        <option value="today" {{ $timeframe === 'today' ? 'selected' : '' }}>Today</option>
                        <option value="week" {{ $timeframe === 'week' ? 'selected' : '' }}>Last 7 Days</option>
                        <option value="month" {{ $timeframe === 'month' ? 'selected' : '' }}>This Month</option>
                        <option value="year" {{ $timeframe === 'year' ? 'selected' : '' }}>This Year</option>
                        <option value="all" {{ $timeframe === 'all' ? 'selected' : '' }}>All Time</option>
                        <option value="custom" {{ $timeframe === 'custom' ? 'selected' : '' }}>Custom Range</option>
                    </select>

                    <div id="customDates" class="{{ $timeframe === 'custom' ? 'flex' : 'hidden' }} items-center gap-2">
                        <input type="date" name="start_date" value="{{ request('start_date') }}"
                            class="border-gray-300 rounded text-sm py-1 focus:ring-red-500 shadow-sm">
                        <span class="text-xs text-gray-500 font-bold">TO</span>
                        <input type="date" name="end_date" value="{{ request('end_date') }}"
                            class="border-gray-300 rounded text-sm py-1 focus:ring-red-500 shadow-sm">
                    </div>

                    <button type="submit"
                        class="px-3 py-1 bg-red-600 text-white text-sm font-bold rounded hover:bg-red-700 shadow-sm">Apply</button>
                </form>
            </div>
        </div>

        <script>
            function toggleCustomDates() {
                const select = document.getElementById('timeframeSelect');
                const customDiv = document.getElementById('customDates');
                if (select.value === 'custom') {
                    customDiv.classList.remove('hidden');
                    customDiv.classList.add('flex');
                } else {
                    customDiv.classList.add('hidden');
                    customDiv.classList.remove('flex');
                    select.form.submit();
                }
            }
        </script>
    </x-slot>

    <div class="py-12 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-lg shadow border-b-4 border-green-500">
                <div class="text-gray-500 text-sm font-bold uppercase mb-1 tracking-wider">Items Fulfilled</div>
                <div class="text-4xl font-black text-gray-900">{{ $deptUsage->sum('total_qty') }}</div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow border-b-4 border-yellow-400">
                <div class="text-gray-500 text-sm font-bold uppercase mb-1 tracking-wider">Pending Requests</div>
                <div class="text-4xl font-black text-yellow-600">{{ $pendingCount }}</div>
            </div>

            <div
                class="bg-white p-6 rounded-lg shadow border-b-4 {{ $lowStock->count() > 0 ? 'border-red-600' : 'border-gray-300' }}">
                <div class="text-gray-500 text-sm font-bold uppercase mb-1 tracking-wider">Low Stock Alerts</div>
                <div class="text-4xl font-black {{ $lowStock->count() > 0 ? 'text-red-600' : 'text-gray-400' }}">
                    {{ $lowStock->count() }}</div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-lg shadow border-t-4 border-red-600 lg:col-span-2">
                <h3 class="font-bold text-gray-700 mb-4 uppercase tracking-wide">Consumption by Department</h3>
                <div class="relative h-64 w-full">
                    <canvas id="consumptionChart"></canvas>
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow border-t-4 border-red-600">
                <h3 class="font-bold text-gray-700 mb-4 uppercase tracking-wide">Request Status Breakdown</h3>
                <div class="relative h-64 w-full flex justify-center">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <div class="bg-white p-6 rounded-lg shadow border-t-4 border-red-600">
                <h3 class="font-bold text-gray-700 mb-4 flex items-center gap-2 uppercase tracking-wide">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                    Top Consumed Items
                </h3>
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="py-2 px-4 uppercase text-xs text-gray-500">Consumable Model</th>
                            <th class="py-2 px-4 text-center uppercase text-xs text-gray-500">Total Fulfilled</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topItems as $item)
                            <tr class="border-b last:border-0 hover:bg-gray-50">
                                <td class="py-3 px-4 font-bold text-gray-800">{{ $item->name }}</td>
                                <td class="py-3 px-4 text-center font-bold text-lg">{{ $item->total_qty }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="py-4 text-center text-gray-500 italic">No items fulfilled in
                                    this timeframe.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="bg-white p-6 rounded-lg shadow border-t-4 border-red-600">
                <h3 class="font-bold text-red-600 mb-4 flex items-center gap-2 uppercase tracking-wide">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                        </path>
                    </svg>
                    Restock Required
                </h3>
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="py-2 px-4 uppercase text-xs text-gray-500">Item</th>
                            <th class="py-2 px-4 text-center uppercase text-xs text-gray-500">Current</th>
                            <th class="py-2 px-4 text-center uppercase text-xs text-gray-500">Threshold</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($lowStock as $inv)
                            <tr class="border-b last:border-0 hover:bg-red-50">
                                <td class="py-3 px-4 font-bold">{{ $inv->consumableType->name }} <span
                                        class="text-xs text-gray-500 font-normal">({{ $inv->color->name ?? 'N/A' }})</span>
                                </td>
                                <td class="py-3 px-4 text-center font-bold text-red-600">{{ $inv->stock_level }}</td>
                                <td class="py-3 px-4 text-center text-gray-500">{{ $inv->threshold }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-4 text-center text-green-600 font-bold">Inventory is
                                    healthy!</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // --- 1. Department Bar Chart ---
            const barCtx = document.getElementById('consumptionChart').getContext('2d');
            new Chart(barCtx, {
                type: 'bar',
                data: {
                    labels: {!! $chartLabels !!},
                    datasets: [{
                        label: 'Total Items Consumed',
                        data: {!! $chartData !!},
                        backgroundColor: 'rgba(220, 38, 38, 0.8)', // Tailwind red-600
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                }
            });

            // --- 2. Request Status Doughnut Chart ---
            const pieCtx = document.getElementById('statusChart').getContext('2d');

            const statusLabels = {!! $statusLabels !!};
            const statusData = {!! $statusData !!};

            const colorMapping = {
                'Pending': '#fbbf24', // Yellow-400
                'Approved': '#3b82f6', // Blue-500
                'Fulfilled': '#059669', // Emerald-600 (Green)
                'Denied': '#dc2626', // Red-600
                'Recalled': '#4b5563' // Gray-600
            };

            const bgColors = statusLabels.map(label => colorMapping[label] || '#cbd5e1');

            new Chart(pieCtx, {
                type: 'doughnut',
                data: {
                    labels: statusLabels,
                    datasets: [{
                        data: statusData,
                        backgroundColor: bgColors,
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '60%',
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        });
    </script>
</x-app-layout>
