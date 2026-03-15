<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-red-600 leading-tight">
            {{ __('All User Requests (Admin)') }}
        </h2>
    </x-slot>

    <div class="py-12 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

        @error('stock_error')
            <div class="p-4 bg-red-100 border-l-4 border-red-600 text-red-800 font-bold rounded shadow-sm">
                {{ $message }}
            </div>
        @enderror

        @if(session('status'))
            <div class="p-4 bg-green-100 border-l-4 border-green-600 text-green-800 font-bold rounded shadow-sm">
                {{ session('status') }}
            </div>
        @endif

        @forelse($requests as $req)
            <div class="bg-white p-6 shadow-sm sm:rounded-lg border border-gray-200 mb-6">

                <div class="border-b pb-4 mb-4">
                    <div class="flex items-center gap-3">
                        <span class="text-lg font-bold text-gray-900">REQ-{{ str_pad($req->id, 4, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <div class="text-sm text-gray-500 mt-1">
                        Requested by <span class="font-bold text-gray-700">{{ $req->user->name }}</span> on {{ $req->created_at->format('d M Y, h:i A') }}
                    </div>
                    <div class="text-sm text-gray-600 mt-1">
                        <strong>Destination:</strong>
                        {{ $req->department->name ?? 'N/A' }}
                        &rarr; {{ $req->location->name ?? 'N/A' }}
                        &rarr; {{ $req->printer->name ?? 'N/A' }}
                    </div>
                    @if($req->notes)
                    <div class="mt-2 p-2 bg-gray-50 text-sm italic text-gray-600 border rounded">
                        "{{ $req->notes }}"
                    </div>
                    @endif
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-600">
                        <thead class="bg-gray-100 text-gray-900">
                            <tr>
                                <th class="py-3 px-4">Item Requested</th>
                                <th class="py-3 px-4 text-center">Qty Asked</th>
                                <th class="py-3 px-4 text-center">Qty Given</th>
                                <th class="py-3 px-4 text-center">Stock</th>
                                <th class="py-3 px-4">Current Status</th>
                                <th class="py-3 px-4 min-w-[300px]">Admin Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($req->items as $item)
                            <tr class="border-b last:border-0 hover:bg-gray-50">

                                <td class="py-3 px-4 font-bold">
                                    {{ $item->inventory->consumableType->name ?? 'Deleted Item' }}
                                    <span class="block text-xs text-gray-500 font-normal">{{ $item->inventory->color->name ?? 'N/A' }}</span>
                                </td>

                                <td class="py-3 px-4 text-center font-bold text-lg text-gray-700">{{ $item->quantity }}</td>
                                <td class="py-3 px-4 text-center font-bold text-lg text-green-600">{{ $item->fulfilled_quantity ?? '-' }}</td>
                                <td class="py-3 px-4 text-center {{ ($item->inventory->stock_level ?? 0) < $item->quantity ? 'text-red-600 font-bold' : 'text-gray-900 font-bold' }}">
                                    {{ $item->inventory->stock_level ?? 'N/A' }}
                                </td>

                                <td class="py-3 px-4">
                                    <span class="px-2 py-1 text-xs font-bold rounded-full uppercase
                                        {{ $item->status == 'Pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $item->status == 'Approved' ? 'bg-blue-100 text-blue-800' : '' }}
                                        {{ $item->status == 'Denied' ? 'bg-red-100 text-red-800' : '' }}
                                        {{ $item->status == 'Fulfilled' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $item->status == 'Recalled' ? 'bg-gray-200 text-gray-800' : '' }}
                                    ">
                                        {{ $item->status }}
                                    </span>

                                    @if($item->status == 'Denied')
                                        <div class="text-xs text-red-600 mt-1 italic">Reason: {{ $item->rejection_reason }}</div>
                                    @endif
                                    @if($item->status == 'Recalled')
                                        <div class="text-xs text-gray-600 mt-1 italic">Reason: {{ $item->recall_reason }} ({{ ucfirst($item->recall_action) }})</div>
                                    @endif
                                </td>

                                <td class="py-3 px-4">
                                    <form action="{{ route('admin.request-items.update', $item) }}" method="POST" class="flex flex-col gap-2">
                                        @csrf @method('PATCH')

                                        <div class="flex gap-2">
                                            <select name="status" class="status-select border-gray-300 rounded text-sm py-1 flex-1" onchange="toggleReasonFields(this)">
                                                <option value="Pending" {{ $item->status == 'Pending' ? 'selected' : '' }}>Pending</option>
                                                <option value="Approved" {{ $item->status == 'Approved' ? 'selected' : '' }}>Approved</option>
                                                <option value="Fulfilled" {{ $item->status == 'Fulfilled' ? 'selected' : '' }}>Fulfilled (Deduct Stock)</option>
                                                <option value="Denied" {{ $item->status == 'Denied' ? 'selected' : '' }}>Denied</option>
                                                @if($item->status == 'Fulfilled' || $item->status == 'Recalled')
                                                    <option value="Recalled" {{ $item->status == 'Recalled' ? 'selected' : '' }}>Recalled</option>
                                                @endif
                                            </select>
                                            <button type="submit" class="px-3 py-1 bg-red-600 text-white text-sm font-bold rounded hover:bg-red-700">Save</button>
                                        </div>

                                        <div class="fulfilled-fields mt-1" style="display: {{ $item->status == 'Fulfilled' ? 'block' : 'none' }};">
                                            <div class="flex items-center gap-2">
                                                <span class="text-xs text-gray-500 font-bold">Qty to Give:</span>
                                                <input type="number" name="fulfilled_quantity"
                                                       value="{{ $item->fulfilled_quantity ?? min($item->quantity, $item->inventory->stock_level ?? 0) }}"
                                                       min="1"
                                                       class="w-24 border-gray-300 rounded text-sm py-1 focus:ring-red-500 focus:border-red-500">
                                            </div>
                                        </div>

                                        <div class="denied-fields mt-1" style="display: {{ $item->status == 'Denied' ? 'block' : 'none' }};">
                                            <input type="text" name="rejection_reason" value="{{ $item->rejection_reason }}" placeholder="Reason for rejection..." class="w-full border-gray-300 rounded text-sm py-1 focus:ring-red-500 focus:border-red-500">
                                        </div>

                                        <div class="recalled-fields mt-1" style="display: {{ $item->status == 'Recalled' ? 'block' : 'none' }};">
                                            <input type="text" name="recall_reason" value="{{ $item->recall_reason }}" placeholder="Reason for recall..." class="w-full border-gray-300 rounded text-sm py-1 mb-1 focus:ring-red-500 focus:border-red-500">
                                            <select name="recall_action" class="w-full border-gray-300 rounded text-sm py-1 focus:ring-red-500 focus:border-red-500">
                                                <option value="restock" {{ $item->recall_action == 'restock' ? 'selected' : '' }}>Return to Stock (Add Inventory)</option>
                                                <option value="dispose" {{ $item->recall_action == 'dispose' ? 'selected' : '' }}>Dispose / Damaged (Write-off)</option>
                                            </select>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @empty
            <div class="bg-white p-6 shadow-sm sm:rounded-lg text-center text-gray-500 font-bold">
                No requests found in the system.
            </div>
        @endforelse
    </div>

    <script>
        function toggleReasonFields(selectElement) {
            const form = selectElement.closest('form');
            const fulfilledFields = form.querySelector('.fulfilled-fields');
            const deniedFields = form.querySelector('.denied-fields');
            const recalledFields = form.querySelector('.recalled-fields');

            // Hide all dynamic fields first
            fulfilledFields.style.display = 'none';
            deniedFields.style.display = 'none';
            recalledFields.style.display = 'none';

            // Show relevant fields based on the selected dropdown value
            if (selectElement.value === 'Fulfilled') {
                fulfilledFields.style.display = 'block';
            } else if (selectElement.value === 'Denied') {
                deniedFields.style.display = 'block';
            } else if (selectElement.value === 'Recalled') {
                recalledFields.style.display = 'block';
            }
        }
    </script>
</x-app-layout>
