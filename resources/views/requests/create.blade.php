<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-red-600 leading-tight uppercase tracking-wide">
            Create New Request
        </h2>
    </x-slot>

    <div class="py-12 max-w-5xl mx-auto sm:px-6 lg:px-8">

        @php
            $inventoryData = $inventories->map(function($inv) {
                return [
                    'id' => $inv->id,
                    'consumable_type_id' => $inv->consumable_type_id, // Needed for Printer filtering
                    'name' => ($inv->consumableType->name ?? 'Unknown') . ' (' . ($inv->color->name ?? 'N/A') . ')'
                ];
            })->values()->toJson();

            $locationData = $locations->values()->toJson();

            $printerData = $printers->map(function($p) {
                return [
                    'id' => $p->id,
                    'name' => $p->name ?? $p->model,
                    'department_id' => $p->department_id,
                    'location_id' => $p->printer_location_id ?? $p->location_id,
                    'compatible_types' => $p->consumableTypes->pluck('id')->toArray() // The secret sauce!
                ];
            })->values()->toJson();
        @endphp

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-t-4 border-red-600"
             x-data="requestForm({{ $inventoryData }}, {{ $locationData }}, {{ $printerData }})">

            <form action="{{ route('requests.store') }}" method="POST" class="p-8">
                @csrf

                @if($canSubmitOnBehalf)
                <div class="mb-8 p-5 bg-gray-50 border border-gray-200 rounded-lg">
                    <label class="flex items-center gap-2 cursor-pointer mb-4">
                        <input type="checkbox" name="is_on_behalf" value="1" x-model="onBehalf" class="rounded border-gray-300 text-red-600 shadow-sm focus:ring-red-500">
                        <span class="text-sm font-black text-gray-800 uppercase tracking-widest">Submit on behalf of another user</span>
                    </label>

                    <div x-show="onBehalf" x-cloak class="pl-6 border-l-2 border-red-600 space-y-4" x-transition>
                        <div class="flex gap-6">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="on_behalf_type" value="existing" x-model="onBehalfType" class="text-red-600 focus:ring-red-500">
                                <span class="text-xs font-bold text-gray-600 uppercase tracking-wider">Existing User</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="on_behalf_type" value="new" x-model="onBehalfType" class="text-red-600 focus:ring-red-500">
                                <span class="text-xs font-bold text-gray-600 uppercase tracking-wider">Guest User</span>
                                <p class="text-[10px] text-gray-500 mt-1 uppercase tracking-wider">Will be recorded as a guest on this ticket.</p>
                            </label>
                        </div>

                        <div x-show="onBehalfType === 'existing'" x-cloak>
                            <select name="existing_user_id" class="block w-full sm:w-1/2 border-gray-300 rounded focus:ring-red-500 focus:border-red-500 shadow-sm text-sm">
                                <option value="">-- Select Existing User --</option>
                                @foreach($users as $u)
                                    <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div x-show="onBehalfType === 'new'" x-cloak>
                            <input type="text" name="new_user_name" placeholder="Enter full name (e.g., John Doe)" class="block w-full sm:w-1/2 border-gray-300 rounded focus:ring-red-500 focus:border-red-500 shadow-sm text-sm">
                        </div>
                    </div>
                </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 border-b border-gray-100 pb-8">

                    <div>
                        <label class="block text-xs font-black text-gray-600 uppercase tracking-widest mb-2">1. Department</label>
                        <select name="department_id" x-model="selectedDepartment" @change="selectedLocation = ''; selectedPrinter = ''; resetItems()" required class="block w-full border-gray-300 rounded focus:ring-red-500 focus:border-red-500 shadow-sm text-sm">
                            <option value="">-- Select Department --</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-black text-gray-600 uppercase tracking-widest mb-2">2. Location</label>
                        <select name="location_id" x-model="selectedLocation" @change="selectedPrinter = ''; resetItems()" :disabled="!selectedDepartment" required class="block w-full border-gray-300 rounded focus:ring-red-500 focus:border-red-500 shadow-sm text-sm disabled:bg-gray-100 disabled:text-gray-400">
                            <option value="">-- Select Location --</option>
                            <template x-for="loc in filteredLocations" :key="loc.id">
                                <option :value="loc.id" x-text="loc.name"></option>
                            </template>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-black text-gray-600 uppercase tracking-widest mb-2">3. Printer (Optional)</label>
                        <select name="printer_id" x-model="selectedPrinter" @change="resetItems()" :disabled="!selectedLocation" class="block w-full border-gray-300 rounded focus:ring-red-500 focus:border-red-500 shadow-sm text-sm disabled:bg-gray-100 disabled:text-gray-400">
                            <option value="">-- Select Printer --</option>
                            <template x-for="printer in filteredPrinters" :key="printer.id">
                                <option :value="printer.id" x-text="printer.name || printer.model"></option>
                            </template>
                        </select>
                    </div>
                </div>

                <div class="mb-8 border-b border-gray-100 pb-8">
                    <label class="block text-xs font-black text-gray-600 uppercase tracking-widest mb-4">4. Items Needed</label>

                    <div class="space-y-4">
                        <template x-for="(row, index) in rows" :key="row.id">
                            <div class="flex items-center gap-4">

                                <div class="flex-grow">
                                    <select x-model="row.inventory_id" :name="'items['+index+'][inventory_id]'" required class="block w-full border-gray-300 rounded focus:ring-red-500 focus:border-red-500 shadow-sm text-sm">
                                        <option value="">-- Choose Item --</option>
                                        <template x-for="item in filteredItems" :key="item.id">
                                            <option :value="item.id"
                                                    x-text="item.name"
                                                    :disabled="isItemSelected(item.id, row.id)">
                                            </option>
                                        </template>
                                    </select>
                                </div>

                                <div class="w-24 shrink-0">
                                    <input type="number" x-model="row.quantity" :name="'items['+index+'][quantity]'" min="1" required class="block w-full border-gray-300 rounded focus:ring-red-500 focus:border-red-500 shadow-sm text-sm text-center">
                                </div>

                                <div class="w-10 shrink-0">
                                    <button type="button" @click="removeRow(row.id)" x-show="rows.length > 1" class="w-full h-10 flex items-center justify-center bg-gray-100 text-gray-500 hover:bg-red-100 hover:text-red-600 rounded transition-colors" title="Remove Item">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                    <div x-show="rows.length <= 1" class="w-full h-10"></div>
                                </div>

                            </div>
                        </template>
                    </div>

                    <div class="mt-4">
                        <button type="button" @click="addRow()" class="text-xs font-bold text-red-600 uppercase tracking-wider hover:text-red-800 transition-colors flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                            Add Another Item
                        </button>
                    </div>
                </div>

                <div class="mb-8">
                    <label class="block text-xs font-black text-gray-600 uppercase tracking-widest mb-2">Additional Notes (Optional)</label>
                    <textarea name="notes" rows="3" class="block w-full border-gray-300 rounded focus:ring-red-500 focus:border-red-500 shadow-sm text-sm resize-none"></textarea>
                </div>

                <div class="flex items-center justify-end gap-4 border-t border-gray-100 pt-6">
                    <a href="{{ route('requests.index') }}" class="text-xs font-bold text-gray-500 uppercase tracking-wider hover:text-gray-800 transition-colors">Cancel</a>
                    <button type="submit" class="px-8 py-3 bg-red-600 text-white font-black uppercase tracking-widest text-xs rounded shadow-md hover:bg-red-700 hover:-translate-y-0.5 transition-all">
                        Submit Request
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('requestForm', (inventoryList, locationList, printerList) => ({
                onBehalf: false,
                onBehalfType: 'existing',

                selectedDepartment: '',
                selectedLocation: '',
                selectedPrinter: '',

                allLocations: locationList,
                allPrinters: printerList,
                availableItems: inventoryList,

                rows: [
                    { id: Date.now(), inventory_id: '', quantity: 1 }
                ],

                // 1. STRICT: Filter Locations based on Printers assigned to the selected Department
                get filteredLocations() {
                    if (!this.selectedDepartment) return [];

                    // Find location IDs that actually have a printer in this department
                    let validLocationIds = this.allPrinters
                        .filter(p => p.department_id == this.selectedDepartment)
                        .map(p => p.location_id);

                    // Strictly return only matching locations (No fallback!)
                    return this.allLocations.filter(loc => validLocationIds.includes(loc.id));
                },

                // 2. STRICT: Filter Printers based on both selected Department and selected Location
                get filteredPrinters() {
                    if (!this.selectedLocation) return [];

                    // Strictly return only matching printers (No fallback!)
                    return this.allPrinters.filter(p =>
                        p.department_id == this.selectedDepartment &&
                        p.location_id == this.selectedLocation
                    );
                },

                // 3. STRICT: Filter Items based on the Compatible Consumables of the selected Printer
                get filteredItems() {
                    // If no specific printer is selected, allow all items
                    if (!this.selectedPrinter) return this.availableItems;

                    let printer = this.allPrinters.find(p => p.id == this.selectedPrinter);

                    // STRICT: If the printer doesn't have compatible types set up, show NO items!
                    if (!printer || !printer.compatible_types || printer.compatible_types.length === 0) {
                        return [];
                    }

                    // Filter down to only items that match the exact consumable type of the printer
                    return this.availableItems.filter(item => printer.compatible_types.includes(item.consumable_type_id));
                },

                addRow() {
                    if (this.rows.length < this.filteredItems.length) {
                        this.rows.push({ id: Date.now(), inventory_id: '', quantity: 1 });
                    } else {
                        alert("You have added all available unique items.");
                    }
                },

                removeRow(id) {
                    if (this.rows.length > 1) {
                        this.rows = this.rows.filter(row => row.id !== id);
                    }
                },

                // Erase items if user changes printer to prevent ordering incompatible toner!
                resetItems() {
                    this.rows = [{ id: Date.now(), inventory_id: '', quantity: 1 }];
                },

                isItemSelected(itemId, currentRowId) {
                    return this.rows.some(row => row.inventory_id == itemId && row.id != currentRowId);
                }
            }));
        });
    </script>
</x-app-layout>
