@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'p-3.5 rounded-xl bg-[#004AAD]/15 border border-[#004AAD]/30 text-emerald-700 text-xs font-semibold flex items-center gap-2.5']) }}>
        <svg class="w-4 h-4 text-[#004AAD] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
        </svg>
        <span>{{ $status }}</span>
    </div>
@endif





