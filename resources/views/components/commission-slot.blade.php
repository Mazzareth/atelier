{{--
/**
 * x-commission-slot.blade.php
 * Visual commission slot display.
 * @prop open: int     Number of open slots
 * @prop max: int      Maximum slots
 * @prop status: string  open | closed | waitlist
 */
--}}
@props([
    'open' => 0,
    'max' => 0,
    'status' => 'closed',
])

@php
    $isOpen = $open > 0;
@endphp

<div class="comm-slots card card-pad-md">
    <div class="flex items-center justify-between">
        <div>
            <div class="comm-slots-label">Commission Slots</div>
            <div class="comm-slots-count {{ $isOpen ? 'open' : 'closed' }}">{{ $open }}</div>
            <div class="text-xs font-mono text-muted" style="margin-top:var(--space-1);">
                @if($isOpen)
                    {{ $open === 1 ? 'slot' : 'slots' }} available
                @else
                    currently closed
                @endif
            </div>
        </div>

        @if($max > 0)
            <div class="text-right">
                <div class="text-xs font-mono text-muted uppercase tracking-wider">Capacity</div>
                <div class="text-2xl font-bold" style="color:var(--color-text);">{{ $max }}</div>
            </div>
        @endif
    </div>

    @if($max > 0)
        <div class="comm-slots-dots" style="margin-top:var(--space-3);">
            @for($i = 0; $i < $max; $i++)
                <div class="comm-slot-dot {{ $i < $open ? 'open' : 'closed' }}"
                     title="{{ $i < $open ? 'Available' : 'Taken' }}">
                </div>
            @endfor
        </div>
    @endif
</div>
