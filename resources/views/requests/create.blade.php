<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-red-600 leading-tight">Create New Request</h2>
    </x-slot>

    <div class="py-12 max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white p-8 shadow-sm sm:rounded-lg border-t-4 border-red-600">

            <form action="{{ route('requests.store') }}" method="POST">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8 border-b pb-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">1. Department</label>
                        <select name="department_id" id="department_select" class="w-full border-gray-300 focus:border-red-500 rounded-md" required>
                            <option value="" disabled selected>-- Choose Department --</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}">{{ $department->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">2. Location</label>
                        <select name="printer_location_id" id="location_select" class="w-full border-gray-300 focus:border-red-500 rounded-md bg-gray-50" required disabled>
                            <option value="" disabled selected>-- Select Dept First --</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">3. Printer</label>
                        <select name="printer_id" id="printer_select" class="w-full border-gray-300 focus:border-red-500 rounded-md bg-gray-50" required disabled>
                            <option value="" disabled selected>-- Select Loc First --</option>
                        </select>
                    </div>
                </div>

                <div id="items-section" class="mb-6" style="display: none;">
                    <h3 class="font-bold text-gray-700 mb-4">4. Items Needed</h3>

                    <div id="items-container" class="space-y-4 mb-4">
                        </div>

                    <button type="button" id="add-item-btn" class="mb-6 text-sm text-red-600 font-bold hover:underline">
                        + Add Another Item
                    </button>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700">Additional Notes (Optional)</label>
                    <textarea name="notes" rows="3" class="w-full border-gray-300 focus:border-red-500 rounded-md"></textarea>
                </div>

                <div class="flex justify-end gap-4 border-t pt-4">
                    <a href="{{ route('requests.index') }}" class="px-4 py-2 text-gray-600 hover:underline">Cancel</a>
                    <button type="submit" id="submit-btn" class="px-6 py-2 bg-red-600 text-white rounded font-bold hover:bg-red-700" disabled>
                        Submit Request
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const deptSelect = document.getElementById('department_select');
            const locSelect = document.getElementById('location_select');
            const printerSelect = document.getElementById('printer_select');
            const itemsSection = document.getElementById('items-section');
            const itemsContainer = document.getElementById('items-container');
            const addItemBtn = document.getElementById('add-item-btn');
            const submitBtn = document.getElementById('submit-btn');

            let rowCount = 0;
            let availableInventory = [];

            // 1. Department Changes -> Fetch Locations
            deptSelect.addEventListener('change', function() {
                const deptId = this.value;

                // Reset downstream
                locSelect.innerHTML = '<option value="" disabled selected>Loading...</option>';
                printerSelect.innerHTML = '<option value="" disabled selected>-- Select Loc First --</option>';
                locSelect.disabled = true;
                printerSelect.disabled = true;
                itemsSection.style.display = 'none';
                submitBtn.disabled = true;

                fetch(`/api/departments/${deptId}/locations`)
                    .then(response => response.json())
                    .then(data => {
                        locSelect.innerHTML = '<option value="" disabled selected>-- Select Location --</option>';
                        if(data.length === 0) {
                            locSelect.innerHTML = '<option value="" disabled selected>No printers in this dept.</option>';
                        } else {
                            data.forEach(loc => {
                                locSelect.innerHTML += `<option value="${loc.id}">${loc.name}</option>`;
                            });
                            locSelect.disabled = false;
                            locSelect.classList.remove('bg-gray-50');
                        }
                    });
            });

            // 2. Location Changes -> Fetch Printers
            locSelect.addEventListener('change', function() {
                const deptId = deptSelect.value;
                const locId = this.value;

                printerSelect.innerHTML = '<option value="" disabled selected>Loading...</option>';
                printerSelect.disabled = true;
                itemsSection.style.display = 'none';
                submitBtn.disabled = true;

                fetch(`/api/departments/${deptId}/locations/${locId}/printers`)
                    .then(response => response.json())
                    .then(data => {
                        printerSelect.innerHTML = '<option value="" disabled selected>-- Select Printer --</option>';
                        data.forEach(printer => {
                            printerSelect.innerHTML += `<option value="${printer.id}">${printer.name}</option>`;
                        });
                        printerSelect.disabled = false;
                        printerSelect.classList.remove('bg-gray-50');
                    });
            });

            // 3. Printer Changes -> Fetch Inventory
            printerSelect.addEventListener('change', function() {
                const printerId = this.value;
                itemsContainer.innerHTML = '';
                rowCount = 0;

                fetch(`/api/printers/${printerId}/inventory`)
                    .then(response => response.json())
                    .then(data => {
                        availableInventory = data;
                        if(availableInventory.length === 0) {
                            alert('No inventory mapped to this printer.');
                            itemsSection.style.display = 'none';
                            submitBtn.disabled = true;
                        } else {
                            itemsSection.style.display = 'block';
                            submitBtn.disabled = false;
                            addRow();
                        }
                    });
            });

            // Helper to build options (Stock info REMOVED)
            function generateInventoryOptions() {
                let options = '<option value="" disabled selected>-- Choose Item --</option>';
                availableInventory.forEach(inv => {
                    const colorName = inv.color ? ` (${inv.color.name})` : '';
                    options += `<option value="${inv.id}">${inv.consumable_type.name}${colorName}</option>`;
                });
                return options;
            }

            function addRow() {
                const newRow = document.createElement('div');
                newRow.className = 'item-row flex gap-4 items-end mt-2 pt-2';
                if (rowCount > 0) newRow.classList.add('border-t', 'border-dashed');

                newRow.innerHTML = `
                    <div class="flex-1">
                        <select name="items[${rowCount}][inventory_id]" class="w-full border-gray-300 focus:border-red-500 rounded-md" required>
                            ${generateInventoryOptions()}
                        </select>
                    </div>
                    <div class="w-32">
                        <input type="number" name="items[${rowCount}][quantity]" value="1" min="1" class="w-full border-gray-300 focus:border-red-500 rounded-md" required placeholder="Qty">
                    </div>
                    ${rowCount > 0 ? `
                    <button type="button" class="px-3 py-2 bg-gray-200 text-gray-600 rounded hover:bg-red-100 hover:text-red-600 font-bold remove-btn">X</button>
                    ` : '<div class="w-10"></div>'}
                `;

                itemsContainer.appendChild(newRow);
                rowCount++;
            }

            addItemBtn.addEventListener('click', addRow);
            itemsContainer.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-btn')) {
                    e.target.closest('.item-row').remove();
                }
            });
        });
    </script>
</x-app-layout>
