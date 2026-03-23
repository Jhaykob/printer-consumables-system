<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-red-600 leading-tight uppercase tracking-wide">
                Fulfillment Dashboard
            </h2>
            <div class="text-sm font-bold text-gray-500 uppercase tracking-widest bg-white px-4 py-2 rounded shadow-sm border border-gray-100">
                Pending Requests: <span class="text-red-600 ml-1">{{ $requests->where('status', 'Pending')->count() ?? 0 }}</span>
            </div>
        </div>
    </x-slot>

    <div class="py-12 max-w-7xl mx-auto sm:px-6 lg:px-8">

        @if (session('success'))
            <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-600 rounded shadow-sm">
                <div class="font-bold text-green-800">{{ session('success') }}</div>
            </div>
        @endif

        <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden border-t-4 border-t-red-600">
            <div class="p-6 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                <h3 class="text-lg font-black text-gray-800 uppercase tracking-wide">Supply Queue</h3>
                <p class="text-xs text-gray-500 font-bold uppercase tracking-widest">Review & Process</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-white border-b border-gray-200">
                            <th class="py-4 px-6 text-xs font-black text-gray-400 uppercase tracking-widest">Req ID / Date</th>
                            <th class="py-4 px-6 text-xs font-black text-gray-400 uppercase tracking-widest">Requested By</th>
                            <th class="py-4 px-6 text-xs font-black text-gray-400 uppercase tracking-widest">Items</th>
                            <th class="py-4 px-6 text-xs font-black text-gray-400 uppercase tracking-widest">Status</th>
                            <th class="py-4 px-6 text-xs font-black text-gray-400 uppercase tracking-widest text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($requests as $req)
                        <tr class="hover:bg-gray-50 transition-colors {{ $req->status === 'Pending' ? 'bg-orange-50/30' : '' }}">

                            <td class="py-4 px-6">
                                <div class="font-black text-gray-800 tracking-wider">#REQ-{{ str_pad($req->id, 4, '0', STR_PAD_LEFT) }}</div>
                                <div class="text-[10px] text-gray-500 font-bold uppercase mt-1">{{ $req->created_at->format('d M Y, h:i A') }}</div>
                            </td>

                            <td class="py-4 px-6">
                                <div class="font-bold text-gray-900">{{ $req->user->name ?? 'Unknown User' }}</div>
                                <div class="text-xs text-gray-500">{{ $req->department->name ?? 'No Dept' }}</div>
                            </td>

                            <td class="py-4 px-6">
                                <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold text-gray-700 bg-gray-100 rounded border border-gray-200">
                                    {{ $req->items->count() ?? 0 }} Items
                                </span>
                            </td>

                            <td class="py-4 px-6">
                                @if($req->status === 'Pending')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded bg-orange-100 text-orange-800 text-[10px] font-black uppercase tracking-widest border border-orange-200">
                                        Pending
                                    </span>
                                @elseif($req->status === 'Fulfilled')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded bg-green-100 text-green-800 text-[10px] font-black uppercase tracking-widest border border-green-200">
                                        Fulfilled
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded bg-red-100 text-red-800 text-[10px] font-black uppercase tracking-widest border border-red-200">
                                        {{ $req->status }}
                                    </span>
                                @endif
                            </td>

                            <td class="py-4 px-6 text-right">
                                @if($req->status === 'Pending')
                                    <a href="{{ route('admin.requests.show', $req->id) }}" class="inline-block px-4 py-2 bg-red-600 text-white font-black uppercase tracking-widest text-[10px] rounded shadow-sm hover:bg-red-700 hover:-translate-y-0.5 transition-all">
                                        Process
                                    </a>
                                @else
                                    <a href="{{ route('admin.requests.show', $req->id) }}" class="inline-block px-4 py-2 bg-white border border-gray-300 text-gray-700 font-black uppercase tracking-widest text-[10px] rounded hover:bg-gray-50 transition-colors">
                                        View Details
                                    </a>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center">
                                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 text-gray-400 mb-4">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                                </div>
                                <h3 class="text-sm font-black text-gray-500 uppercase tracking-widest">No Requests Found</h3>
                                <p class="text-xs text-gray-400 mt-1">The supply queue is currently empty.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(method_exists($requests, 'links') && $requests->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                    {{ $requests->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
