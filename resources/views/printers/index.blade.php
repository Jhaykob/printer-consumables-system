<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-red-600 leading-tight">
            {{ __('Manage Printers') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @can('manage-assets')
                <div class="p-6 bg-white shadow sm:rounded-lg border-l-4 border-red-600">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Register New Printer</h3>

                    @if ($locations->isEmpty())
                        <div class="text-red-500 mb-4">Please add a Printer Location before registering a printer.</div>
                    @else
                        <form action="{{ route('printers.store') }}" method="POST"
                            class="grid grid-cols-1 md:grid-cols-5 gap-4">
                            @csrf

                            <div>
                                <select name="department_id"
                                    class="w-full border-gray-300 focus:border-red-500 focus:ring-red-500 rounded-md shadow-sm"
                                    required>
                                    <option value="" disabled selected>Select Department...</option>
                                    @foreach ($departments as $dept)
                                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <select name="printer_location_id"
                                    class="w-full border-gray-300 focus:border-red-500 focus:ring-red-500 rounded-md shadow-sm"
                                    required>
                                    <option value="" disabled selected>Select Location...</option>
                                    @foreach ($locations as $location)
                                        <option value="{{ $location->id }}">{{ $location->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <x-text-input name="name" placeholder="Printer Model (e.g. HP M404n)" class="w-full"
                                    required />
                            </div>

                            <div>
                                <x-text-input name="serial_number" placeholder="Serial Number" class="w-full" required />
                            </div>

                            <div>
                                <x-text-input name="ip_address" placeholder="IP Address (Optional)" class="w-full" />
                            </div>

                            <div class="md:col-span-4 mt-2 border-t pt-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Compatible Consumables (Check
                                    all that apply)</label>
                                @if ($consumableTypes->isEmpty())
                                    <p class="text-sm text-red-500">Please add Consumable Types first.</p>
                                @else
                                    <div
                                        class="grid grid-cols-1 md:grid-cols-3 gap-2 max-h-48 overflow-y-auto p-2 border rounded bg-gray-50">
                                        @foreach ($consumableTypes as $type)
                                            <div class="flex items-center space-x-2">
                                                <input type="checkbox" name="consumable_types[]" value="{{ $type->id }}"
                                                    id="type_{{ $type->id }}"
                                                    class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                                                <label for="type_{{ $type->id }}"
                                                    class="text-sm text-gray-700 cursor-pointer">
                                                    {{ $type->name }} <span
                                                        class="text-xs text-gray-500">({{ $type->category->name }})</span>
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            <div class="md:col-span-4 flex justify-end">
                                <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 transition">
                                    Save Printer
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            @endcan

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-t-4 border-red-600">
                <div class="p-6 text-gray-900 overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Department
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Location
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Model / Name
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Serial
                                    Number</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">IP Address
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Compatible Consumables</th>
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($printers as $printer)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                                        {{ $printer->department ? $printer->department->name : 'Unassigned' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                                        {{ $printer->location->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $printer->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ $printer->serial_number }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ $printer->ip_address ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        @if ($printer->consumableTypes->count() > 0)
                                            <ul class="list-disc list-inside">
                                                @foreach ($printer->consumableTypes as $ctype)
                                                    <li>{{ $ctype->name }}</li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <span class="text-gray-400 italic">None assigned</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                        @can('manage-assets')
                                            <form action="{{ route('printers.destroy', $printer) }}" method="POST"
                                                class="inline">
                                                @csrf @method('DELETE')
                                                <button class="text-red-600 hover:text-red-900 font-bold"
                                                    onclick="return confirm('Delete this printer?')">Delete</button>
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
</x-app-layout>
