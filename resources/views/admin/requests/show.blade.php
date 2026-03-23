<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.requests.index') }}" class="text-gray-400 hover:text-red-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <h2 class="font-semibold text-xl text-red-600 leading-tight uppercase tracking-wide">
                    Request #REQ-{{ str_pad($request->id, 4, '0', STR_PAD_LEFT) }}
                </h2>
            </div>

            <div>
                @if($request->status === 'Pending')
                    <span class="px-4 py-2 bg-orange-100 text-orange-800 text-xs font-black uppercase tracking-widest rounded border border-orange-200 shadow-sm">Pending</span>
                @elseif($request->status === 'Processing')
                    <span class="px-4 py-2 bg-blue-100 text-blue-800 text-xs font-black uppercase tracking-widest rounded border border-blue-200 shadow-sm">Processing...</span>
                @elseif($request->status === 'Fulfilled')
                    <span class="px-4 py-2 bg-green-100 text-green-800 text-xs font-black uppercase tracking-widest rounded border border-green-200 shadow-sm">Fulfilled</span>
                @elseif($request->status === 'Partially Fulfilled')
                    <span class="px-4 py-2 bg-yellow-100 text-yellow-800 text-xs font-black uppercase tracking-widest rounded border border-yellow-200 shadow-sm">Partially Fulfilled</span>
                @else
                    <span class="px-4 py-2 bg-red-100 text-red-800 text-xs font-black uppercase tracking-widest rounded border border-red-200 shadow-sm">{{ $request->status }}</span>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12 max-w-7xl mx-auto sm:px-6 lg:px-8">

        @if (session('success'))
            <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-600 rounded shadow-sm">
                <div class="font-bold text-green-800">{{ session('success') }}</div>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-600 rounded shadow-sm">
                <div class="font-bold text-red-800">{{ session('error') }}</div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <div class="lg:col-span-1 space-y-8">
                <div class="bg-white p-6 rounded-lg shadow-sm border-t-4 border-gray-800">
                    <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest mb-4 border-b pb-2">Requester Details</h3>
                    <div class="space-y-4">
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Employee Name</p>
                            <p class="text-sm font-bold text-gray-900">
                                @if($request->guest_name)
                                    {{ $request->guest_name }} <span class="text-[10px] text-gray-500 uppercase tracking-widest ml-1">(Guest)</span>
                                @else
                                    {{ $request->user->name ?? 'Unknown' }}
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Department</p>
                            <p class="text-sm font-bold text-gray-900">{{ $request->department->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Date Submitted</p>
                            <p class="text-sm font-bold text-gray-900">{{ $request->created_at->format('d M Y, h:i A') }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-sm border-t-4 border-gray-800">
                    <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest mb-4 border-b pb-2">Destination Info</h3>
                    <div class="space-y-4">
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Location</p>
                            <p class="text-sm font-bold text-gray-900">{{ $request->location->name ?? 'N/A' }}</p>
                        </div>
                        @if($request->printer)
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Target Printer</p>
                            <p class="text-sm font-bold text-gray-900">{{ $request->printer->name ?? $request->printer->model }}</p>
                            <p class="text-xs text-gray-500">IP: {{ $request->printer->ip_address ?? 'N/A' }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="lg:col-span-2">

                <form action="{{ route('admin.requests.update', $request->id) }}" method="POST" class="bg-white rounded-lg shadow-sm border-t-4 border-red-600 overflow-hidden mb-8">
                    @csrf
                    @method('PUT')

                    <div class="p-6 bg-gray-50 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="text-lg font-black text-gray-800 uppercase tracking-wide">Requested Items</h3>
                        @if($request->items->where('status', 'Pending')->count() > 0)
                            <p class="text-xs text-gray-500 font-bold uppercase tracking-widest">Verify Stock & Fulfill</p>
                        @else
                            <p class="text-xs text-gray-500 font-bold uppercase tracking-widest">Processing Complete</p>
                        @endif
                    </div>

                    <div class="p-6">
                        <div class="space-y-6">
                            @foreach($request->items as $item)
                            <div class="flex flex-col p-4 border border-gray-200 rounded-lg bg-white relative">

                                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
                                    <div class="flex items-center gap-4 mb-4 sm:mb-0">
                                        <div class="w-12 h-12 rounded bg-gray-100 flex items-center justify-center border border-gray-200">
                                            <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                        </div>
                                        <div class="w-40">
                                            <h4 class="font-bold text-gray-900 truncate">{{ $item->inventory->consumableType->name ?? 'Unknown Item' }}</h4>
                                            <p class="text-[10px] text-gray-500 uppercase tracking-widest font-bold mt-1 truncate">
                                                Color: {{ $item->inventory->color->name ?? 'N/A' }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-4 sm:gap-6 flex-wrap">
                                        <div class="text-center">
                                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Requested</p>
                                            <p class="text-lg font-black text-gray-900">{{ $item->quantity }}</p>
                                        </div>

                                        <div class="text-center border-l border-gray-200 pl-4 sm:pl-6">
                                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Stock</p>
                                            <p class="text-lg font-black {{ ($item->inventory->stock_level ?? 0) < $item->quantity ? 'text-red-600' : 'text-green-600' }}">
                                                {{ $item->inventory->stock_level ?? 0 }}
                                            </p>
                                        </div>

                                        @if($item->status === 'Pending')
                                            <div class="text-center border-l border-gray-200 pl-4 sm:pl-6">
                                                <p class="text-[10px] font-bold text-gray-600 uppercase tracking-wider mb-1">Item Action</p>
                                                <select name="items[{{ $item->id }}][status]" class="w-28 text-xs font-bold border-gray-300 rounded focus:ring-red-500 focus:border-red-500 shadow-sm text-center">
                                                    <option value="Pending">Skip (Decide Later)</option>
                                                    <option value="Fulfilled" selected>Fulfill</option>
                                                    <option value="Denied">Deny</option>
                                                </select>
                                            </div>

                                            <div class="text-center border-l border-gray-200 pl-4 sm:pl-6">
                                                <p class="text-[10px] font-bold text-red-600 uppercase tracking-wider mb-1">Fulfill Qty</p>
                                                <input type="number" name="items[{{ $item->id }}][fulfilled_quantity]" value="{{ min($item->quantity, $item->inventory->stock_level ?? 0) }}" max="{{ $item->inventory->stock_level ?? 0 }}" min="0" class="w-16 text-center border-gray-300 rounded focus:ring-red-500 focus:border-red-500 shadow-sm text-sm font-bold p-1">
                                            </div>
                                        @else
                                            <div class="text-center border-l border-gray-200 pl-4 sm:pl-6">
                                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Status</p>
                                                <p class="text-xs font-black {{ $item->status === 'Denied' || $item->status === 'Recalled' ? 'text-red-600' : 'text-green-600' }} uppercase mt-2">{{ $item->status }}</p>
                                            </div>
                                            <div class="text-center border-l border-gray-200 pl-4 sm:pl-6">
                                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Given</p>
                                                <p class="text-lg font-black text-gray-900">{{ $item->fulfilled_quantity ?? 0 }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                @if($item->status === 'Pending')
                                    <div class="mt-4 pt-4 border-t border-gray-100">
                                        <input type="text" name="items[{{ $item->id }}][reason]" value="{{ $item->reason }}" placeholder="Add a note or reason... (Required if Denied)" class="w-full text-xs border-gray-300 rounded focus:ring-red-500 focus:border-red-500 shadow-sm bg-gray-50 p-2">
                                    </div>
                                @elseif($item->reason)
                                    <div class="mt-4 pt-4 border-t border-gray-100 bg-gray-50 p-3 rounded">
                                        <p class="text-xs text-gray-700 font-bold"><span class="text-red-600 uppercase tracking-widest text-[10px] mr-1">Notes:</span> {{ $item->reason }}</p>
                                    </div>
                                @endif

                                @if($item->status === 'Fulfilled')
                                    <div x-data="{ openRecall: false }" class="mt-3 border-t border-gray-100 pt-3">
                                        <button type="button" @click="openRecall = !openRecall" class="text-[10px] font-black uppercase tracking-widest text-red-600 hover:text-red-800 transition-colors flex items-center gap-1 group">
                                            <svg class="w-4 h-4 text-red-400 group-hover:text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
                                            Recall Item
                                        </button>

                                        <div x-show="openRecall" x-cloak class="mt-3 p-4 bg-red-50 border border-red-200 rounded-lg">
                                            <p class="text-xs text-red-800 font-bold mb-3 uppercase tracking-wider">Process Item Recall</p>
                                            <div class="flex flex-col xl:flex-row gap-3">
                                                <select form="recall-form-{{ $item->id }}" name="recall_action" required class="text-xs border-red-300 rounded focus:ring-red-500 shadow-sm">
                                                    <option value="return">Return to Stock (+{{ $item->fulfilled_quantity }})</option>
                                                    <option value="defective">Mark Defective (Do NOT return stock)</option>
                                                </select>
                                                <input form="recall-form-{{ $item->id }}" type="text" name="recall_reason" placeholder="Reason for recall..." required class="flex-grow text-xs border-red-300 rounded focus:ring-red-500 shadow-sm">
                                                <button form="recall-form-{{ $item->id }}" type="submit" class="px-4 py-2 bg-red-600 text-white font-black uppercase tracking-widest text-[10px] rounded shadow-sm hover:bg-red-700 transition-colors whitespace-nowrap">
                                                    Confirm Recall
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                            </div>
                            @endforeach
                        </div>
                    </div>

                    @if($request->items->where('status', 'Pending')->count() > 0)
                    <div class="p-6 bg-gray-50 border-t border-gray-100 flex flex-col sm:flex-row justify-between items-center gap-4">
                        <div class="flex items-center gap-2">
                            <input type="checkbox" required id="confirm_stock" class="rounded border-gray-300 text-red-600 shadow-sm focus:ring-red-500 h-4 w-4">
                            <label for="confirm_stock" class="text-xs text-gray-600 font-bold uppercase tracking-wider cursor-pointer">I confirm inventory adjustments</label>
                        </div>
                        <button type="submit" class="w-full sm:w-auto px-8 py-3 bg-red-600 text-white font-black uppercase tracking-widest text-xs rounded shadow-md hover:bg-red-700 hover:-translate-y-0.5 transition-all">
                            Complete Processing
                        </button>
                    </div>
                    @endif
                </form>

            </div>
        </div>
    </div>

    @foreach($request->items as $item)
        @if($item->status === 'Fulfilled')
            <form id="recall-form-{{ $item->id }}" action="{{ route('admin.requests.recall', [$request->id, $item->id]) }}" method="POST" class="hidden">
                @csrf
            </form>
        @endif
    @endforeach

</x-app-layout>
