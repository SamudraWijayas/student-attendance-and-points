@props([
    'title',
    'value',
    'icon' => 'activity',
    'iconBg' => 'bg-teal-50 text-teal-700 dark:bg-teal-950/50 dark:text-teal-300',
    'badgeText' => null,
    'badgeClass' => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300',
    'trend' => null,
])

<div {{ $attributes->merge(['class' => 'bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 p-5.5 shadow-sm hover:shadow-md transition-all duration-200 relative overflow-hidden group']) }}>
    <div class="flex items-start justify-between gap-4">
        <div>
            <p class="text-xs font-medium uppercase tracking-wider text-slate-500 dark:text-slate-400">{{ $title }}</p>
            <h4 class="text-2xl font-bold text-slate-900 dark:text-white mt-1.5 tracking-tight group-hover:text-teal-700 dark:group-hover:text-teal-400 transition-colors">
                {{ $value }}
            </h4>
            @if($badgeText || $trend)
                <div class="mt-3 flex items-center gap-2">
                    @if($badgeText)
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $badgeClass }}">
                            {{ $badgeText }}
                        </span>
                    @endif
                    @if($trend)
                        <span class="text-xs text-slate-500 dark:text-slate-400">{{ $trend }}</span>
                    @endif
                </div>
            @endif
        </div>
        <div class="w-12 h-12 rounded-xl {{ $iconBg }} flex items-center justify-center shrink-0 shadow-sm">
            <x-icon :name="$icon" class="w-6 h-6" />
        </div>
    </div>
</div>

