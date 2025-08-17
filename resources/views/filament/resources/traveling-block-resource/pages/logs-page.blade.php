<x-filament-panels::page>
    <div class="space-y-6">
        <table class="w-full bg-white rounded-lg shadow-md">
            <thead>
                <tr class="bg-gray-100 text-gray-700">
                    <th class="px-4 py-2">Date</th>
                    <th class="px-4 py-2">User</th>
                    <th class="px-4 py-2">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr class="border-t">
                        <td class="px-4 py-2">{{ $log->added_date }}</td>
                        <td class="px-4 py-2">{{ $log->added_by }}</td>
                        <td class="px-4 py-2">{{ $log->action_name }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="text-center p-4">No logs found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-filament-panels::page>
