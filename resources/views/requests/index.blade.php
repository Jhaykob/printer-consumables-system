<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-red-600 leading-tight">My Requests</h2>
            <a href="{{ route('requests.create') }}" class="px-4 py-2 bg-red-600 text-white rounded font-bold hover:bg-red-700 transition">
                + New Request
            </a>
        </div>
    </x-slot>

    <div class="py-12 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        @if(session('status'))
            <div class="p-4 bg-green-100 text-green-800 font-bold rounded">{{ session('status') }}</div>
        @endif

        @forelse($requests as $req)
            <div class="bg-white p-6 shadow-sm sm:rounded-lg border-l-4 {{ $req->status == 'Pending' ? 'border-yellow-400' : ($req->status == 'Fulfilled' ? 'border-green-500' : 'border-red-600') }}">
                <div class="flex justify-between items-center border-b pb-4 mb-4">
                    <div>
                        <span class="text-sm text-gray-500">Request #REQ-{{ str_pad($req->id, 4, '0', STR_PAD_LEFT) }}</span>
                        <span class="ml-4 text-sm text-gray-500">{{ $req->created_at->format('d M Y, h:i A') }}</span>
                    </div>
                    <span class="px-3 py-1 text-sm font-bold rounded-full {{ $req->status == 'Pending' ? 'bg-yellow-100 text-yellow-800' : ($req->status == 'Fulfilled' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') }}">
                        {{ $req->status }}
                    </span>
                </div>

                <table class="w-full text-sm text-left text-gray-600">
                    <thead>
                        <tr class="text-gray-900 bg-gray-50">
                            <th class="py-2 px-4">Item Requested</th>
                            <th class="py-2 px-4">Color</th>
                            <th class="py-2 px-4 text-center">Quantity</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($req->items as $item)
                        <tr class="border-b">
                            <td class="py-2 px-4 font-medium">{{ $item->inventory->consumableType->name }}</td>
                            <td class="py-2 px-4">{{ $item->inventory->color ? $item->inventory->color->name : 'N/A' }}</td>
                            <td class="py-2 px-4 text-center font-bold">{{ $item->quantity }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @empty
            <div class="bg-white p-6 shadow-sm sm:rounded-lg text-center text-gray-500">
                You haven't made any requests yet.
            </div>
        @endforelse
    </div>
</x-app-layout>
