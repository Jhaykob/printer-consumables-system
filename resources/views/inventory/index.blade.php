<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-red-600 leading-tight">
            {{ __('Manage Inventory') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @error('duplicate') <div class="p-4 bg-red-100 text-red-800 font-bold rounded">{{ $message }}</div> @enderror

            @can('manage-inventory')
            <div class="p-6 bg-white shadow sm:rounded-lg border-l-4 border-red-600">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Add Inventory Item</h3>

                <form action="{{ route('inventory.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                    @csrf

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Consumable Type</label>
                        <select name="consumable_type_id" id="type_select" class="w-full border-gray-300 focus:border-red-500 focus:ring-red-500 rounded-md shadow-sm" required>
                            <option value="" disabled selected>-- Select Type --</option>
                            @foreach($types as $type)
                                <option value="{{ $type->id }}" data-requires-color="{{ $type->category->requires_color ? 'true' : 'false' }}">
                                    {{ $type->name }} ({{ $type->category->name }})
                                </option>
                            @endforeach
                        </select>
                        @error('consumable_type_id') <span class="text-xs text-red-600 font-bold">{{ $message }}</span> @enderror
                    </div>

                    <div id="color_group" style="display: none;">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Color</label>
                        <select name="color_id" id="color_select" class="w-full border-gray-300 focus:border-red-500 focus:ring-red-500 rounded-md shadow-sm">
                            <option value="">-- Select Color --</option>
                            @foreach($colors as $color)
                                <option value="{{ $color->id }}">{{ $color->name }}</option>
                            @endforeach
                        </select>
                        @error('color_id') <span class="text-xs text-red-600 font-bold">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Stock Level</label>
                        <x-text-input name="stock_level" type="number" min="0" value="0" class="w-full" required />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Low Threshold</label>
                        <x-text-input name="threshold" type="number" min="0" value="5" class="w-full" required />
                    </div>

                    <div class="md:col-span-5 flex justify-end">
                        <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded font-bold hover:bg-red-700 transition">
                            Save Inventory
                        </button>
                    </div>
                </form>
            </div>
            @endcan

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-t-4 border-red-600">
                <div class="p-6 text-gray-900 overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type / Category</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Color</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Stock</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($inventories as $inv)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-bold">
                                    {{ $inv->consumableType->name }}
                                    <span class="block text-xs text-gray-500 font-normal">{{ $inv->consumableType->category->name }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $inv->color ? $inv->color->name : 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center font-bold text-lg">
                                    {{ $inv->stock_level }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if($inv->stock_level <= $inv->threshold)
                                        <span class="px-2 py-1 bg-red-100 text-red-800 text-xs rounded-full font-bold">Low Stock</span>
                                    @else
                                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full font-bold">In Stock</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    @can('manage-inventory')
                                    <form action="{{ route('inventory.destroy', $inv) }}" method="POST" class="inline">
                                        @csrf @method('DELETE')
                                        <button class="text-red-600 hover:text-red-900 font-bold" onclick="return confirm('Delete this inventory item?')">Delete</button>
                                    </form>
                                    @endcan
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const typeSelect = document.getElementById('type_select');
            const colorGroup = document.getElementById('color_group');
            const colorSelect = document.getElementById('color_select');

            typeSelect.addEventListener('change', function() {
                // Get the currently selected option
                const selectedOption = this.options[this.selectedIndex];

                // Read the data-requires-color attribute
                const requiresColor = selectedOption.getAttribute('data-requires-color');

                if (requiresColor === 'true') {
                    // Show color dropdown and make it required
                    colorGroup.style.display = 'block';
                    colorSelect.setAttribute('required', 'required');
                } else {
                    // Hide color dropdown, remove required status, and clear value
                    colorGroup.style.display = 'none';
                    colorSelect.removeAttribute('required');
                    colorSelect.value = '';
                }
            });
        });
    </script>
</x-app-layout>
