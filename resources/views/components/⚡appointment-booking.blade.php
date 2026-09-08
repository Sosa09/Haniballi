<?php

use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Book Appointment')] class extends Component
{
    public string $selectedDate = '';

    /** @var array<int, string> */
    public array $availableSlots = [];

    public string $selectedSlot = '';

    public int $step = 1;

    public string $appointmentType = 'video';

    public string $notes = '';

    public bool $bookingConfirmed = false;

    public function selectDate(string $date): void
    {
        $this->selectedDate = $date;
        $this->availableSlots = $this->generateAvailableSlots($date);
        $this->selectedSlot = '';
        $this->step = 2;
    }

    public function selectSlot(string $slot): void
    {
        $this->selectedSlot = $slot;
        $this->step = 3;
    }

    public function confirmBooking(): void
    {
        $this->validate([
            'selectedDate' => 'required|date',
            'selectedSlot' => 'required|string',
            'appointmentType' => 'required|string',
        ]);

        $scheduledAt = Carbon::parse("{$this->selectedDate} {$this->selectedSlot}");

        Appointment::create([
            'patient_id' => auth()->user()->patient?->id,
            'user_id' => 1,
            'scheduled_at' => $scheduledAt,
            'duration_minutes' => 30,
            'type' => $this->appointmentType,
            'status' => 'pending',
            'notes' => $this->notes,
        ]);

        $this->bookingConfirmed = true;
        $this->step = 1;
        $this->selectedDate = '';
        $this->selectedSlot = '';
        $this->notes = '';
    }

    public function goToStep(int $step): void
    {
        $this->step = $step;
    }

    /** @return array<int, string> */
    private function generateAvailableSlots(string $date): array
    {
        $slots = [];
        $start = Carbon::parse("{$date} 09:00");
        $end = Carbon::parse("{$date} 17:00");

        while ($start->lt($end)) {
            $slots[] = $start->format('H:i');
            $start->addMinutes(30);
        }

        return $slots;
    }
};
?>

<div class="max-w-3xl mx-auto">
    @if ($bookingConfirmed)
        <div class="bg-white border border-emerald-200 rounded-2xl p-8 text-center shadow-sm space-y-4">
            <div class="w-14 h-14 bg-emerald-50 text-emerald-600 rounded-full flex items-center justify-center mx-auto border border-emerald-200">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </div>
            <div>
                <h3 class="text-xl font-bold text-slate-900">Consultation Confirmed</h3>
                <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                    Your appointment with Dr. Mehdi Haniballi has been registered on the clinical calendar.
                </p>
            </div>
            <div class="pt-2">
                <button wire:click="$set('bookingConfirmed', false)" class="bg-slate-900 hover:bg-emerald-700 text-white text-xs font-bold px-6 py-2.5 rounded-xl transition shadow-sm">
                    Book Another Session
                </button>
            </div>
        </div>
    @else
        {{-- Refined Step Indicator --}}
        <div class="flex items-center justify-center mb-8 gap-2 sm:gap-4">
            @foreach ([1 => 'Date', 2 => 'Time Slot', 3 => 'Confirmation'] as $stepNum => $label)
                <div class="flex items-center gap-2">
                    <div class="flex items-center justify-center w-7 h-7 rounded-lg text-xs font-bold transition
                        {{ $step >= $stepNum ? 'bg-emerald-700 text-white shadow-sm' : 'bg-slate-100 text-slate-400 border border-slate-200' }}">
                        {{ $stepNum }}
                    </div>
                    <span class="text-xs font-semibold {{ $step >= $stepNum ? 'text-slate-900' : 'text-slate-400' }}">
                        {{ $label }}
                    </span>
                    @if ($stepNum < 3)
                        <div class="w-6 sm:w-10 h-px {{ $step > $stepNum ? 'bg-emerald-600' : 'bg-slate-200' }}"></div>
                    @endif
                </div>
            @endforeach
        </div>

        {{-- Step 1: Pick Date --}}
        @if ($step === 1)
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 md:p-8 space-y-6">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Select Consultation Date</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Clinical consultation availability over the next two weeks.</p>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    @for ($i = 1; $i <= 14; $i++)
                        @php $date = now()->addDays($i); @endphp
                        @if ($date->isWeekday())
                            <button wire:click="selectDate('{{ $date->format('Y-m-d') }}')"
                                class="p-4 border rounded-xl text-center hover:border-emerald-500 hover:bg-emerald-50/50 transition
                                    {{ $selectedDate === $date->format('Y-m-d') ? 'border-emerald-600 bg-emerald-50 ring-1 ring-emerald-600' : 'border-slate-200 bg-slate-50/50' }}">
                                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">{{ $date->format('D') }}</div>
                                <div class="text-xl font-bold text-slate-900 my-0.5 font-mono">{{ $date->format('j') }}</div>
                                <div class="text-[11px] text-slate-500">{{ $date->format('M') }}</div>
                            </button>
                        @endif
                    @endfor
                </div>
            </div>
        @endif

        {{-- Step 2: Pick Time --}}
        @if ($step === 2)
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 md:p-8 space-y-6">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Select Time Slot</h3>
                        <p class="text-xs text-slate-500 mt-0.5 font-mono">
                            {{ \Carbon\Carbon::parse($selectedDate)->format('l, F j, Y') }}
                        </p>
                    </div>
                    <button wire:click="goToStep(1)" class="text-xs font-bold text-emerald-700 hover:underline flex items-center gap-1">
                        <span>← Change Date</span>
                    </button>
                </div>
                <div class="grid grid-cols-3 sm:grid-cols-4 gap-2.5">
                    @foreach ($availableSlots as $slot)
                        <button wire:click="selectSlot('{{ $slot }}')"
                            class="py-2.5 px-3 border rounded-xl text-xs font-mono font-bold text-center hover:border-emerald-500 hover:bg-emerald-50/50 transition
                                {{ $selectedSlot === $slot ? 'border-emerald-600 bg-emerald-50 text-emerald-900 ring-1 ring-emerald-600' : 'border-slate-200 text-slate-700 bg-slate-50/50' }}">
                            {{ $slot }}
                        </button>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Step 3: Confirm --}}
        @if ($step === 3)
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 md:p-8 space-y-6">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Confirm Reservation</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Verify session parameters prior to booking.</p>
                    </div>
                    <button wire:click="goToStep(2)" class="text-xs font-bold text-emerald-700 hover:underline flex items-center gap-1">
                        <span>← Change Time</span>
                    </button>
                </div>

                <div class="bg-slate-50 rounded-xl p-4 border border-slate-200 divide-y divide-slate-200 text-xs">
                    <div class="py-2 flex justify-between">
                        <span class="text-slate-500">Date:</span>
                        <span class="font-bold text-slate-900">{{ \Carbon\Carbon::parse($selectedDate)->format('l, F j, Y') }}</span>
                    </div>
                    <div class="py-2 flex justify-between">
                        <span class="text-slate-500">Time:</span>
                        <span class="font-bold text-slate-900 font-mono">{{ $selectedSlot }}</span>
                    </div>
                    <div class="py-2 flex justify-between">
                        <span class="text-slate-500">Doctor:</span>
                        <span class="font-bold text-emerald-800">Dr. Mehdi Haniballi</span>
                    </div>
                    <div class="py-2 flex justify-between">
                        <span class="text-slate-500">Duration:</span>
                        <span class="font-semibold text-slate-700">30 minutes</span>
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Consultation Modality</label>
                        <select wire:model="appointmentType" class="w-full text-xs font-medium border border-slate-200 rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-emerald-500 focus:border-transparent bg-slate-50/50">
                            <option value="video">Encrypted Telehealth Video</option>
                            <option value="in_person">In-Clinic Assessment</option>
                            <option value="phone">Clinical Telephone Follow-up</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Pre-Session Symptoms & Notes (Optional)</label>
                        <textarea wire:model="notes" rows="3" placeholder="Specify any dietary changes, fatigue patterns, or questions for Dr. Haniballi..."
                            class="w-full text-xs border border-slate-200 rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-emerald-500 focus:border-transparent resize-none bg-slate-50/50"></textarea>
                    </div>
                </div>

                <button wire:click="confirmBooking" wire:loading.attr="disabled"
                    class="w-full bg-emerald-700 hover:bg-emerald-800 text-white py-3 rounded-xl font-bold text-xs transition shadow-md shadow-emerald-700/20 disabled:opacity-50">
                    <span wire:loading.remove>Finalize Appointment Booking</span>
                    <span wire:loading>Processing Reservation...</span>
                </button>
            </div>
        @endif
    @endif
</div>