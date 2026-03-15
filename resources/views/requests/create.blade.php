<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-red-600 leading-tight">Create New Request</h2>
    </x-slot>

    <div class="py-12 max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white p-8 shadow-sm sm:rounded-lg border-t-4 border-red-600">

            <form action="{{ route('requests.store') }}" method="POST">
                @csrf

                <div id="items-container" class="space-y-4 mb-6">
                    <h3 class="font-bold text-gray-700 border-b pb-2">Items Needed</h3>

                    <div class="item-row flex gap-4 items-end">
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700">Select Item</label>
                            <select name="items[0][inventory_id]" class="w-full border-gray-300 focus:border-red-500 rounded-md" required>
                                <option value="" disabled selected>-- Choose from Inventory --</option>
                                @foreach($inventory as $inv)
                                    <option value="{{ $inv->id }}">
                                        {{ $inv->consumableType->name }}
                                        {{ $inv->color ? '('.$inv->color->name.')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="w-32">
                            <label class="block text-sm font-medium text-gray-700">Quantity</label>
                            <input type="number" name="items[0][quantity]" value="1" min="1" class="w-full border-gray-300 focus:border-red-500 rounded-md" required>
                        </div>
                    </div>
                </div>

                <button type="button" id="add-item-btn" class="mb-6 text-sm text-red-600 font-bold hover:underline">
                    + Add Another Item
                </button>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700">Additional Notes (Optional)</label>
                    <textarea name="notes" rows="3" class="w-full border-gray-300 focus:border-red-500 rounded-md"></textarea>
                </div>

                <div class="flex justify-end gap-4 border-t pt-4">
                    <a href="{{ route('requests.index') }}" class="px-4 py-2 text-gray-600 hover:underline">Cancel</a>
                    <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded font-bold hover:bg-red-700">
                        Submit Request
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let rowCount = 1;
            const container = document.getElementById('items-container');
            const addBtn = document.getElementById('add-item-btn');

            // Save the HTML of the first dropdown to clone it easily
            const dropdownHTML = document.querySelector('select[name="items[0][inventory_id]"]').innerHTML;

            addBtn.addEventListener('click', function() {
                const newRow = document.createElement('div');
                newRow.className = 'item-row flex gap-4 items-end mt-4 pt-4 border-t border-dashed';

                newRow.innerHTML = `
                    <div class="flex-1">
                        <select name="items[${rowCount}][inventory_id]" class="w-full border-gray-300 focus:border-red-500 rounded-md" required>
                            ${dropdownHTML}
                        </select>
                    </div>
                    <div class="w-32">
                        <input type="number" name="items[${rowCount}][quantity]" value="1" min="1" class="w-full border-gray-300 focus:border-red-500 rounded-md" required>
                    </div>
                    <button type="button" class="px-3 py-2 bg-gray-200 text-gray-600 rounded hover:bg-red-100 hover:text-red-600 font-bold remove-btn">
                        X
                    </button>
                `;

                container.appendChild(newRow);
                rowCount++;
            });

            // Event delegation to handle removing rows
            container.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-btn')) {
                    e.target.closest('.item-row').remove();
                }
            });
        });
    </script>
</x-app-layout>
