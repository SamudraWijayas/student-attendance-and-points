@props([
    'variant' => 'primary',
    'size' => 'md',
    'icon' => null,
    'type' => 'button',
])

@php
$baseClasses = 'inline-flex items-center justify-center font-medium rounded-xl transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-60 disabled:cursor-not-allowed cursor-pointer';

$sizeClasses = match($size) {
    'sm' => 'px-3 py-1.5 text-xs gap-1.5',
    'lg' => 'px-5 py-3 text-base gap-2.5',
    default => 'px-4 py-2.2 text-sm gap-2',
};

$variantClasses = match($variant) {
    'primary' => 'bg-teal-700 hover:bg-teal-800 text-white shadow-sm shadow-teal-700/20 focus:ring-teal-600',
    'secondary' => 'bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/50 shadow-xs focus:ring-slate-400',
    'danger' => 'bg-rose-600 hover:bg-rose-700 text-white shadow-sm shadow-rose-600/20 focus:ring-rose-500',
    'warning' => 'bg-amber-500 hover:bg-amber-600 text-white shadow-sm shadow-amber-500/20 focus:ring-amber-400',
    'ghost' => 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 focus:ring-slate-400',
    default => 'bg-teal-700 hover:bg-teal-800 text-white shadow-sm focus:ring-teal-600',
};
@endphp

<button type="{{ $type }}" {{ $attributes->merge(['class' => "{$baseClasses} {$sizeClasses} {$variantClasses}"]) }}>
    @if($icon)
        <x-icon :name="$icon" class="{{ $size === 'sm' ? 'w-4 h-4' : 'w-4.5 h-4.5' }}" />
    @endif
    {{ $slot }}
</button>

