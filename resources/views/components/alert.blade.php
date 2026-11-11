@props(['type' => 'info'])

@php
$config = match($type) {
    'success' => [
        'bg' => 'bg-emerald-50 border-emerald-200 text-emerald-800 dark:bg-emerald-950/40 dark:border-emerald-800/50 dark:text-emerald-200',
        'icon' => 'check-circle-2',
        'iconColor' => 'text-emerald-600 dark:text-emerald-400',
    ],
    'error', 'danger' => [
        'bg' => 'bg-rose-50 border-rose-200 text-rose-800 dark:bg-rose-950/40 dark:border-rose-800/50 dark:text-rose-200',
        'icon' => 'alert-circle',
        'iconColor' => 'text-rose-600 dark:text-rose-400',
    ],
    'warning' => [
        'bg' => 'bg-amber-50 border-amber-200 text-amber-800 dark:bg-amber-950/40 dark:border-amber-800/50 dark:text-amber-200',
        'icon' => 'alert-triangle',
        'iconColor' => 'text-amber-600 dark:text-amber-400',
    ],
    default => [
        'bg' => 'bg-sky-50 border-sky-200 text-sky-800 dark:bg-sky-950/40 dark:border-sky-800/50 dark:text-sky-200',
        'icon' => 'info',
        'iconColor' => 'text-sky-600 dark:text-sky-400',
    ],
};
@endphp

<div {{ $attributes->merge(['class' => 'p-4 rounded-xl border flex items-start gap-3 ' . $config['bg']]) }} role="alert">
    <x-icon :name="$config['icon']" class="w-5 h-5 shrink-0 mt-0.5 {{ $config['iconColor'] }}" />
    <div class="text-sm font-medium leading-relaxed">
        {{ $slot }}
    </div>
</div>

