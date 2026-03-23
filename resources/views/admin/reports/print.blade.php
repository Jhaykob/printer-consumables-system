<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Executive Inventory Audit - {{ date('Y-m-d') }}</title>
    @vite(['resources/css/app.css'])
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

    <style>
        /* This hides the browser's default headers and footers (URL, Page 1 of 2, etc.) */
        @page { size: auto;  margin: 0mm; }

        @media print {
            .no-print { display: none !important; }
            body { -webkit-print-color-adjust: exact; background: white; margin: 15mm; } /* Re-apply safe margin */
            .chart-container { page-break-inside: avoid; }
            .avoid-break { page-break-inside: avoid; }
            .shadow-sm { box-shadow: none !important; }
        }

        /* The Background Watermark */
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 8rem;
            color: rgba(220, 38, 38, 0.04); /* Very faint red */
            font-weight: 900;
            text-transform: uppercase;
            z-index: 0;
            pointer-events: none;
            white-space: nowrap;
        }

        .report-wrapper { position: relative; z-index: 10; }

        .status-fulfilled { color: #059669; font-weight: bold; }
        .status-denied { color: #dc2626; font-weight: bold; }
        .status-recalled { color: #4b5563; font-weight: bold; }
        .status-pending { color: #d97706; font-weight: bold; }
    </style>
</head>
<body class="bg-gray-100 p-4 sm:p-8 relative">

    <div class="watermark">CONFIDENTIAL</div>

    <div class="no-print max-w-5xl mx-auto mb-6 bg-white p-4 rounded-lg shadow-md border border-gray-200 flex flex-col md:flex-row justify-between items-center gap-4">

        <form method="GET" action="{{ route('admin.reports.print') }}" class="flex items-center gap-2">
            <label class="font-black text-gray-700 uppercase text-sm tracking-widest hidden sm:block">Timeframe:</label>
            <select name="timeframe" id="printTimeframe" onchange="toggleCustomPrintDates()" class="border-gray-300 rounded bg-gray-50 text-sm py-2 font-bold focus:ring-red-500 shadow-sm">
                <option value="today" {{ $timeframe === 'today' ? 'selected' : '' }}>Today</option>
                <option value="week" {{ $timeframe === 'week' ? 'selected' : '' }}>Last 7 Days</option>
                <option value="month" {{ $timeframe === 'month' ? 'selected' : '' }}>This Month</option>
                <option value="year" {{ $timeframe === 'year' ? 'selected' : '' }}>This Year</option>
                <option value="all" {{ $timeframe === 'all' ? 'selected' : '' }}>Historical (All Time)</option>
                <option value="custom" {{ $timeframe === 'custom' ? 'selected' : '' }}>Custom Range</option>
            </select>

            <div id="printCustomDates" class="{{ $timeframe === 'custom' ? 'flex' : 'hidden' }} items-center gap-2">
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="border-gray-300 rounded text-sm py-1">
                <span class="text-xs text-gray-500 font-bold">TO</span>
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="border-gray-300 rounded text-sm py-1">
            </div>

            <button type="submit" class="px-3 py-2 bg-gray-800 text-white text-sm font-bold rounded shadow-sm hover:bg-gray-700">Apply</button>
        </form>

        <div class="flex gap-2">
            <button onclick="window.print()" class="px-4 py-2 bg-gray-200 text-gray-800 font-bold rounded shadow hover:bg-gray-300 transition text-sm">
                Print
            </button>
            <button onclick="generateDirectPDF()" class="px-4 py-2 bg-red-600 text-white font-bold rounded shadow hover:bg-red-700 transition text-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Download PDF
            </button>
        </div>
    </div>

    <div id="pdf-content" class="report-wrapper max-w-5xl mx-auto bg-white p-10 shadow-sm border-t-8 border-red-600 print:border-t-4 print:border-red-600">

        <div class="flex justify-between items-end border-b-2 pb-6 mb-8 border-gray-200">
            <div>
                <h1 class="text-4xl font-black text-gray-900 tracking-tighter uppercase">Inventory Audit</h1>
                <p class="text-gray-500 font-bold uppercase text-xs tracking-widest mt-1">Printer Consumables Management System</p>
            </div>
            <div class="text-right border-l-4 border-gray-100 pl-4">
                <p class="text-lg font-bold text-gray-800">{{ now()->format('d M Y') }}</p>
                <p class="text-xs font-black uppercase tracking-wider mt-1 text-red-600">
                    Timeframe:
                    @if($timeframe == 'custom') {{ strtoupper($dateLabel) }}
                    @elseif($timeframe == 'today') TODAY
                    @elseif($timeframe == 'week') LAST 7 DAYS
                    @elseif($timeframe == 'month') THIS MONTH
                    @elseif($timeframe == 'year') THIS YEAR
                    @else HISTORICAL (ALL TIME)
                    @endif
                </p>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-8 mb-10 chart-container items-center">
            <div>
                <h2 class="text-sm font-black text-gray-400 uppercase tracking-widest mb-2">Request Volume Analysis</h2>
                @if($statusTotals->count() > 0)
                    <div id="piechart_3d" style="width: 100%; height: 250px;"></div>
                @else
                    <div class="h-[250px] flex items-center justify-center bg-gray-50 border border-dashed border-gray-200 rounded text-gray-400 font-bold italic text-sm">
                        No data recorded for this timeframe.
                    </div>
                @endif
            </div>
            <div class="bg-gray-50 p-6 rounded-lg border border-gray-100">
                <h2 class="text-sm font-black text-gray-400 uppercase tracking-widest mb-4">Summary Totals</h2>
                <div class="space-y-4">
                    <div class="flex justify-between items-center border-b border-gray-200 pb-2">
                        <span class="text-gray-600 font-bold uppercase text-xs">Items Fulfilled:</span>
                        <span class="text-xl font-black text-green-600">
                            {{ $allActivity->where('status', 'Fulfilled')->sum(function($item) { return $item->fulfilled_quantity ?? $item->quantity; }) }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center border-b border-gray-200 pb-2">
                        <span class="text-gray-600 font-bold uppercase text-xs">Denied Requests:</span>
                        <span class="text-xl font-black text-red-600">{{ $allActivity->where('status', 'Denied')->count() }}</span>
                    </div>
                    <div class="flex justify-between items-center pb-1">
                        <span class="text-gray-600 font-bold uppercase text-xs">Recalled Items:</span>
                        <span class="text-xl font-black text-gray-600">{{ $allActivity->where('status', 'Recalled')->count() }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-12 avoid-break">
            <h2 class="text-lg font-black text-gray-800 mb-4 border-l-4 border-gray-900 pl-3 uppercase tracking-wide">Detailed Distribution Log</h2>
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-gray-100 text-gray-700 uppercase font-bold border-y-2 border-gray-300">
                        <th class="p-3 border-x border-gray-200">Date</th>
                        <th class="p-3 border-r border-gray-200">Department</th>
                        <th class="p-3 border-r border-gray-200">Consumable Item</th>
                        <th class="p-3 border-r border-gray-200 text-center">Qty Asked</th>
                        <th class="p-3 border-r border-gray-200 text-center">Qty Given</th>
                        <th class="p-3 border-r border-gray-200 text-right">Final Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($allActivity as $row)
                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                        <td class="p-2 border-x border-gray-200 text-gray-600">{{ \Carbon\Carbon::parse($row->updated_at)->format('d/m/y') }}</td>
                        <td class="p-2 border-r border-gray-200 font-bold text-gray-800">{{ $row->dept_name }}</td>
                        <td class="p-2 border-r border-gray-200 italic text-gray-700">{{ $row->item_name }}</td>
                        <td class="p-2 border-r border-gray-200 text-center text-gray-500">{{ $row->quantity }}</td>
                        <td class="p-2 border-r border-gray-200 text-center font-black text-gray-900 text-sm">
                            @if($row->status === 'Fulfilled')
                                {{ $row->fulfilled_quantity ?? $row->quantity }}
                            @else
                                <span class="text-gray-300 font-normal">-</span>
                            @endif
                        </td>
                        <td class="p-2 border-r border-gray-200 text-right uppercase font-black text-[10px]">
                            <span class="status-{{ strtolower($row->status) }}">{{ $row->status }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-6 text-center text-gray-400 italic font-bold border border-gray-200">
                            No requests recorded during this timeframe.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="grid grid-cols-2 gap-8 avoid-break">
            <div>
                <h2 class="text-lg font-black text-red-600 mb-4 uppercase italic border-l-4 border-red-600 pl-3">Critical Restock Items</h2>
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-red-50 text-red-800 font-black uppercase tracking-wider border-y-2 border-red-200">
                            <th class="p-2 border-x border-red-100">Model</th>
                            <th class="p-2 border-r border-red-100 text-center">Current Stock</th>
                            <th class="p-2 border-r border-red-100 text-center">Threshold</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($lowStock as $ls)
                        <tr class="border-b border-red-100">
                            <td class="p-2 border-x border-red-100 font-bold text-gray-800">{{ $ls->consumableType->name }}</td>
                            <td class="p-2 border-r border-red-100 text-center text-red-600 font-black text-sm">{{ $ls->stock_level }}</td>
                            <td class="p-2 border-r border-red-100 text-center text-gray-500">{{ $ls->threshold }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="p-3 border border-gray-200 text-center text-green-600 font-bold italic">No low stock detected.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div>
                <h2 class="text-lg font-black text-gray-800 mb-4 uppercase tracking-wide border-l-4 border-gray-800 pl-3">Consumption Velocity</h2>
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-gray-100 text-gray-800 font-black uppercase tracking-wider border-y-2 border-gray-300">
                            <th class="p-2 border-x border-gray-200">Model</th>
                            <th class="p-2 border-r border-gray-200 text-center">Total Consumed</th>
                            <th class="p-2 border-r border-gray-200 text-center">Avg / Mo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($consumptionStats as $cs)
                        <tr class="border-b border-gray-200">
                            <td class="p-2 border-x border-gray-200 font-bold text-gray-800">{{ $cs->name }}</td>
                            <td class="p-2 border-r border-gray-200 text-center font-bold text-gray-600">{{ $cs->total_consumed }}</td>
                            <td class="p-2 border-r border-gray-200 text-center font-black text-red-600">{{ $cs->avg_per_month }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="p-3 border border-gray-200 text-center text-gray-400 font-bold italic">No consumption data for this timeframe.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-12 pt-6 border-t border-gray-200 text-center">
            <p class="text-[10px] text-gray-400 uppercase tracking-widest font-bold">End of Management Report</p>
        </div>
    </div>

    <script type="text/javascript">
      // Custom Date Toggle UI
      function toggleCustomPrintDates() {
          const select = document.getElementById('printTimeframe');
          const customDiv = document.getElementById('printCustomDates');
          if(select.value === 'custom') {
              customDiv.classList.remove('hidden');
              customDiv.classList.add('flex');
          } else {
              customDiv.classList.add('hidden');
              customDiv.classList.remove('flex');
              select.form.submit();
          }
      }

      // Direct PDF Download using html2pdf
      function generateDirectPDF() {
          const element = document.getElementById('pdf-content');
          const opt = {
              margin:       10,
              filename:     'Inventory_Audit_Report_{{ date('Ymd') }}.pdf',
              image:        { type: 'jpeg', quality: 0.98 },
              html2canvas:  { scale: 2, useCORS: true },
              jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
          };
          html2pdf().set(opt).from(element).save();
      }

      // Google Charts
      @if($statusTotals->count() > 0)
          google.charts.load("current", {packages:["corechart"]});
          google.charts.setOnLoadCallback(drawChart);

          function drawChart() {
            var data = google.visualization.arrayToDataTable([
              ['Status', 'Count'],
              @foreach($statusTotals as $st)
              ['{{ $st->status }}', {{ $st->count }}],
              @endforeach
            ]);

            var options = {
              title: '',
              is3D: true,
              colors: ['#059669', '#dc2626', '#4b5563', '#d97706', '#3b82f6'],
              chartArea: {width: '100%', height: '90%'},
              legend: {position: 'bottom', textStyle: {fontSize: 11, bold: true}},
              backgroundColor: 'transparent'
            };

            var chart = new google.visualization.PieChart(document.getElementById('piechart_3d'));
            chart.draw(data, options);
          }
      @endif
    </script>
</body>
</html>
