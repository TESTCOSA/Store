<!-- resources/views/filament/resources/attendance-resource/pages/monthly-attendance-sheet.blade.php -->
<x-filament-panels::page>
    <div class="space-y-6">
        <form wire:submit="saveAttendance" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{ $this->form }}
            </div>

            <div class="overflow-x-auto w-full">
                <table class="w-full bg-white rounded-lg shadow-md table-fixed">
                    <thead>
                    <tr class="bg-gray-100 text-gray-700">
                        <th class="sticky left-0 bg-gray-100 px-4 py-2 w-12">Code</th>
                        <th class="sticky left-12 bg-gray-100 px-4 py-2 w-48">Name</th>

                        @foreach ($this->daysInPeriod as $day)
                            <th class="px-2 py-2 text-center text-black w-20 text-xs">
                                <div>{{ $day['month'] }}</div>
                                <div>{{ $day['dayOfMonth'] }}</div>
                                <div>{{ $day['dayOfWeek'] }}</div>
                            </th>
                        @endforeach

                    </tr>
                    </thead>
                    <tbody>

                    @foreach ($attendanceData as $employeeId => $data)
                        <tr class="border-t hover:bg-gray-50">
                            <td class="sticky left-0 bg-white px-2 py-2 border-r">
                                {{ $data['employee']['user_details']['emp_code'] ?? 'N/A' }}
                            </td>
                            <td class="sticky left-12 bg-white px-4 py-2 border-r">
                                {{ $data['employee']['user_details']['full_name_en'] ?? 'N/A' }}
                            </td>
                            @foreach ($this->daysInPeriod as $day)
                                <td class="px-1 py-1 text-center border">
                                    <select
                                        wire:model="attendanceData.{{ $employeeId }}.attendance.{{ $day['day'] }}"
                                        class="block w-full h-8 text-center text-sm font-medium text-black bg-white border border-gray-300 rounded focus:border-blue-300 focus:outline-none"
                                        style="min-width: 3.5rem; padding: 0 1.5rem 0 0.25rem;"
                                    >
                                        <option value="X">X</option>
                                        <option value="A">A</option>
                                        <option value="CK">CK</option>
                                        <option value="AL">AL</option>
                                        <option value="DO">DO</option>
                                        <option value="SL">SL</option>
                                        <option value="PH">PH</option>
                                        <option value="UL">UL</option>
                                    </select>
                                </td>
                            @endforeach

                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="flex justify-end">
                <x-filament::button type="submit">
                    Save Attendance
                </x-filament::button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add any JavaScript enhancements here
        });
    </script>
</x-filament-panels::page>

