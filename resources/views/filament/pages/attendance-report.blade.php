<x-filament-panels::page>
    <div class="grid gap-6">
        {{ $this->form }}

        @if(!empty($reportData) && ($reportData['empty'] ?? false))
            <x-filament::section>
                <p class="text-center text-gray-500 py-4">No services found for the selected period and filters.</p>
            </x-filament::section>
        @elseif(!empty($reportData) && !($reportData['empty'] ?? true))
            {{-- Summary Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <x-filament::section class="bg-white rounded-lg shadow">
                    <div class="text-center">
                        <div class="text-3xl font-bold text-primary-600">{{ $reportData['total_services'] }}</div>
                        <div class="text-sm text-gray-500">Total Services</div>
                    </div>
                </x-filament::section>

                <x-filament::section class="bg-white rounded-lg shadow">
                    <div class="text-center">
                        <div class="text-3xl font-bold text-success-600">{{ $reportData['total_present'] }}</div>
                        <div class="text-sm text-gray-500">Total Present</div>
                    </div>
                </x-filament::section>

                <x-filament::section class="bg-white rounded-lg shadow">
                    <div class="text-center">
                        <div class="text-3xl font-bold text-danger-600">{{ $reportData['total_absent'] }}</div>
                        <div class="text-sm text-gray-500">Total Absent</div>
                    </div>
                </x-filament::section>

                <x-filament::section class="bg-white rounded-lg shadow">
                    <div class="text-center">
                        <div class="text-3xl font-bold text-warning-600">{{ $reportData['average_attendance'] }}</div>
                        <div class="text-sm text-gray-500">Avg Attendance / Service</div>
                    </div>
                </x-filament::section>
            </div>

            {{-- Attendance by Type --}}
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <x-filament::section class="bg-white rounded-lg shadow">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-success-600">{{ $reportData['total_present'] }}</div>
                        <div class="text-sm text-gray-500">Present</div>
                    </div>
                </x-filament::section>

                <x-filament::section class="bg-white rounded-lg shadow">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-warning-600">{{ $reportData['total_late'] }}</div>
                        <div class="text-sm text-gray-500">Late</div>
                    </div>
                </x-filament::section>

                <x-filament::section class="bg-white rounded-lg shadow">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-danger-600">{{ $reportData['total_absent'] }}</div>
                        <div class="text-sm text-gray-500">Absent</div>
                    </div>
                </x-filament::section>

                <x-filament::section class="bg-white rounded-lg shadow">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-gray-500">{{ $reportData['total_excused'] }}</div>
                        <div class="text-sm text-gray-500">Excused</div>
                    </div>
                </x-filament::section>

                <x-filament::section class="bg-white rounded-lg shadow">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-primary-600">{{ $reportData['total_unique_attendees'] }}</div>
                        <div class="text-sm text-gray-500">Unique Members</div>
                    </div>
                </x-filament::section>
            </div>

            {{-- Per-Service Breakdown --}}
            <x-filament::section>
                <x-slot name="heading">Attendance Per Service</x-slot>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="text-left py-3 px-4 font-medium">Service</th>
                                <th class="text-left py-3 px-4 font-medium">Date</th>
                                <th class="text-left py-3 px-4 font-medium">Type</th>
                                <th class="text-center py-3 px-4 font-medium text-success-600">Present</th>
                                <th class="text-center py-3 px-4 font-medium text-warning-600">Late</th>
                                <th class="text-center py-3 px-4 font-medium text-danger-600">Absent</th>
                                <th class="text-center py-3 px-4 font-medium text-gray-500">Excused</th>
                                <th class="text-center py-3 px-4 font-medium">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reportData['services'] as $service)
                                <tr class="border-b border-gray-100 hover:bg-gray-50">
                                    <td class="py-3 px-4">{{ $service['title'] }}</td>
                                    <td class="py-3 px-4">{{ $service['date'] }}</td>
                                    <td class="py-3 px-4">
                                        <x-filament::badge>{{ $service['type'] }}</x-filament::badge>
                                    </td>
                                    <td class="py-3 px-4 text-center text-success-600 font-medium">{{ $service['present'] }}</td>
                                    <td class="py-3 px-4 text-center text-warning-600 font-medium">{{ $service['late'] }}</td>
                                    <td class="py-3 px-4 text-center text-danger-600 font-medium">{{ $service['absent'] }}</td>
                                    <td class="py-3 px-4 text-center text-gray-500 font-medium">{{ $service['excused'] }}</td>
                                    <td class="py-3 px-4 text-center font-medium">{{ $service['total'] }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="py-4 text-center text-gray-500">No services found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-filament::section>

            {{-- Age Ranges + Gender + Marital --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Age Ranges --}}
                <x-filament::section>
                    <x-slot name="heading">Age Ranges (Attendees)</x-slot>
                    <div class="space-y-3">
                        @php
                            $ageLabels = ['children' => 'Children (0–12)', 'teens' => 'Teens (13–19)', 'young_adult' => 'Young Adults (20–35)', 'adult' => 'Adults (36+)'];
                        @endphp
                        @foreach($ageLabels as $key => $label)
                            <div class="flex justify-between items-center">
                                <span class="text-sm">{{ $label }}</span>
                                <span class="font-bold text-primary-600">{{ $reportData['age_ranges'][$key] ?? 0 }}</span>
                            </div>
                        @endforeach
                    </div>
                </x-filament::section>

                {{-- Gender Distribution --}}
                <x-filament::section>
                    <x-slot name="heading">Gender Distribution</x-slot>
                    <div class="space-y-3">
                        @forelse($reportData['gender_distribution'] as $gender => $count)
                            <div class="flex justify-between items-center">
                                <span class="text-sm">{{ $gender }}</span>
                                <span class="font-bold text-primary-600">{{ $count }}</span>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500">No data</p>
                        @endforelse
                    </div>
                </x-filament::section>

                {{-- Marital Status --}}
                <x-filament::section>
                    <x-slot name="heading">Marital Status</x-slot>
                    <div class="space-y-3">
                        @forelse($reportData['marital_distribution'] as $status => $count)
                            <div class="flex justify-between items-center">
                                <span class="text-sm">{{ $status }}</span>
                                <span class="font-bold text-primary-600">{{ $count }}</span>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500">No data</p>
                        @endforelse
                    </div>
                </x-filament::section>
            </div>

            {{-- Services by Type --}}
            <x-filament::section>
                <x-slot name="heading">Services by Type</x-slot>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @forelse($reportData['services_by_type'] as $type => $count)
                        <div class="text-center p-3 bg-gray-50 rounded-lg">
                            <div class="text-2xl font-bold text-primary-600">{{ $count }}</div>
                            <div class="text-sm text-gray-500">{{ $type }}</div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 col-span-full text-center">No data</p>
                    @endforelse
                </div>
            </x-filament::section>
        @else
            <x-filament::section>
                <div class="text-center py-8">
                    <p class="text-gray-500">Select a date range and click <strong>Generate Report</strong> to view attendance statistics.</p>
                </div>
            </x-filament::section>
        @endif
    </div>
</x-filament-panels::page>
