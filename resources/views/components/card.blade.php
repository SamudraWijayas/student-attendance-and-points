@props(['title' => null, 'subtitle' => null, 'action' => null])

<div {{ $attributes->merge(['class' => 'bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm overflow-hidden transition-all duration-200']) }}>
    @if($title || $action)
        <div class="px-6 py-4.5 border-b border-slate-100 dark:border-slate-800/80 flex flex-wrap items-center justify-between gap-4">
            <div>
                @if($title)
                    <h3 class="text-base font-semibold text-slate-900 dark:text-slate-100 tracking-tight">{{ $title }}</h3>
                @endif
                @if($subtitle)
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ $subtitle }}</p>
                @endif
            </div>
            @if($action)
                <div class="flex items-center gap-2">
                    {{ $action }}
                </div>
            @endif
        </div>
    @endif
    <div class="p-6">
        {{ $slot }}
    </div>
</div>

