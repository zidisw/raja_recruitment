<div>
    <flux:dropdown position="bottom" align="end">
        <button class="px-4 py-2 bg-linear-to-r from-brand-500 to-brand-600 text-white rounded-xl shadow-[0_8px_30px_rgba(245,166,35,0.3)] hover:shadow-[0_8px_30px_rgba(245,166,35,0.5)] transition-all font-semibold flex items-center gap-2 cursor-pointer border-none outline-none">
            <flux:icon.calendar class="size-4" />
            {{ $application->interview ? __('Reschedule Interview') : __('Schedule Interview') }}
        </button>

        <flux:menu class="w-80" style="display: none;">
            <div class="px-4 py-3 w-full max-h-[80vh] overflow-y-auto">
                <div class="mb-4">
                    <h3 class="text-lg font-bold text-zinc-900 dark:text-zinc-100">{{ __('Set Interview') }}</h3>
                </div>

                <form wire:submit.prevent="saveSchedule" class="space-y-3 pb-2 w-full">
                    <div class="flex flex-col gap-1 w-full">
                        <flux:label>{{ __('Interviewer') }}</flux:label>
                        <x-custom-select
                            wire:model="interviewer_id"
                            placeholder="{{ __('Select Interviewer') }}"
                            :options="['' => __('Select Interviewer')] + $interviewers->mapWithKeys(fn($i) => [$i->id => $i->name . ' (' . $i->role->label() . ')'])->toArray()"
                        />
                        @error('interviewer_id') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-2 w-full">
                        <div class="flex flex-col gap-1">
                            <flux:label>{{ __('Date') }}</flux:label>
                            <x-date-picker wire:model="scheduled_date" mode="date" placeholder="{{ __('Select date...') }}" />
                            @error('scheduled_date') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div class="flex flex-col gap-1">
                            <flux:label>{{ __('Time') }}</flux:label>
                            <x-date-picker wire:model="scheduled_time" mode="time" placeholder="{{ __('Select time...') }}" />
                            @error('scheduled_time') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="flex flex-col gap-1 w-full">
                        <flux:label>{{ __('Meeting Link (Optional)') }}</flux:label>
                        <flux:input type="url" wire:model="meeting_link" placeholder="https://zoom.us/j/..." class="w-full" />
                        @error('meeting_link') <span class="text-sm text-red-500">{{ $message }}</span> @enderror
                    </div>

                    <div class="pt-2 w-full">
                        <button type="submit" class="w-full py-2 bg-brand-500 hover:bg-brand-600 text-white font-semibold rounded-xl shadow-lg transition-colors cursor-pointer">
                            {{ __('Save & Send Invite') }}
                        </button>
                    </div>
                </form>

                @if (session()->has('success'))
                    <div class="mt-3 p-2 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 rounded-lg text-sm w-full">
                        {{ session('success') }}
                    </div>
                @endif
                @if (session()->has('error'))
                    <div class="mt-3 p-2 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300 rounded-lg text-sm w-full">
                        {{ session('error') }}
                    </div>
                @endif
            </div>
        </flux:menu>
    </flux:dropdown>
</div>
