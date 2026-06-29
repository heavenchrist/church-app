<x-filament-panels::page>
    <div class="grid gap-6">
        <h2 class="text-xl font-bold tracking-tight">Member Report</h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <x-filament::section class="bg-white rounded-lg shadow">
                <div class="text-center">
                    <div class="text-3xl font-bold text-success-600">{{ $reportData['total_active'] ?? 0 }}</div>
                    <div class="text-sm text-gray-500">Active Members</div>
                </div>
            </x-filament::section>

            <x-filament::section class="bg-white rounded-lg shadow">
                <div class="text-center">
                    <div class="text-3xl font-bold text-gray-400">{{ $reportData['total_inactive'] ?? 0 }}</div>
                    <div class="text-sm text-gray-500">Inactive Members</div>
                </div>
            </x-filament::section>

            <x-filament::section class="bg-white rounded-lg shadow">
                <div class="text-center">
                    <div class="text-3xl font-bold text-primary-600">{{ $reportData['total'] ?? 0 }}</div>
                    <div class="text-sm text-gray-500">Total Members</div>
                </div>
            </x-filament::section>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <x-filament::section>
                <x-slot name="heading">Gender Distribution</x-slot>
                <div class="space-y-2">
                    @forelse($reportData['gender_distribution'] ?? [] as $gender => $count)
                        <div class="flex justify-between items-center">
                            <span class="capitalize">{{ $gender }}</span>
                            <span class="font-bold">{{ $count }}</span>
                        </div>
                    @empty
                        <p class="text-gray-500">No data available</p>
                    @endforelse
                </div>
            </x-filament::section>

            <x-filament::section>
                <x-slot name="heading">Classification Distribution</x-slot>
                <div class="space-y-2">
                    @forelse($reportData['classification_distribution'] ?? [] as $classification => $count)
                        <div class="flex justify-between items-center">
                            <span class="capitalize">{{ str_replace('_', ' ', $classification) }}</span>
                            <span class="font-bold">{{ $count }}</span>
                        </div>
                    @empty
                        <p class="text-gray-500">No data available</p>
                    @endforelse
                </div>
            </x-filament::section>
        </div>

        <x-filament::section>
            <x-slot name="heading">Upcoming Birthdays</x-slot>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="text-left py-3 px-4 font-medium">Name</th>
                            <th class="text-left py-3 px-4 font-medium">Birthday</th>
                            <th class="text-center py-3 px-4 font-medium">Age</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reportData['birthday_list'] ?? [] as $birthday)
                            <tr class="border-b border-gray-100">
                                <td class="py-3 px-4">{{ $birthday['name'] }}</td>
                                <td class="py-3 px-4">{{ $birthday['birthday'] }}</td>
                                <td class="py-3 px-4 text-center">{{ $birthday['age'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-4 text-center text-gray-500">No upcoming birthdays</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
