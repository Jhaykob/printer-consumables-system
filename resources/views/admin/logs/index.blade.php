<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-red-600 leading-tight">System Audit Logs</h2>
    </x-slot>

    <div class="py-12 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow sm:rounded-lg border-t-4 border-red-600 overflow-hidden">
            <div class="overflow-x-auto p-6">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50 text-left text-xs font-bold text-gray-500 uppercase">
                        <tr>
                            <th class="px-4 py-3">Timestamp</th>
                            <th class="px-4 py-3">User</th>
                            <th class="px-4 py-3">Action</th>
                            <th class="px-4 py-3">Item Type</th>
                            <th class="px-4 py-3">Changes (Before &rarr; After)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 text-sm">
                        @forelse($logs as $log)
                        <tr>
                            <td class="px-4 py-4 whitespace-nowrap text-gray-500">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-4 font-bold">{{ $log->user->name ?? 'System' }}</td>
                            <td class="px-4 py-4 font-bold text-xs uppercase text-red-600">{{ $log->action }}</td>
                            <td class="px-4 py-4">{{ $log->model_type }}</td>
                            <td class="px-4 py-4">
                                @if($log->before)
                                    <div class="bg-red-50 p-1 text-red-800 text-xs mb-1 rounded">WAS: {{ $log->before }}</div>
                                @endif
                                @if($log->after)
                                    <div class="bg-green-50 p-1 text-green-800 text-xs font-bold rounded">NOW: {{ $log->after }}</div>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="px-4 py-4 text-center text-gray-500">No logs found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4 bg-gray-50 border-t">{{ $logs->links() }}</div>
        </div>
    </div>
</x-app-layout>
