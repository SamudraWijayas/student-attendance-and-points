@props([
    'icon' => 'inbox',
    'title' => 'Tidak Ada Data',
    'description' => 'Belum ada data yang tersedia untuk ditampilkan pada kategori ini.',
    'action' => null,
])

<div {{ $attributes->merge(['class' => 'text-center py-12 px-4 rounded-2xl bg-slate-50/50 dark:bg-slate-900/40 border border-dashed border-slate-200 dark:border-slate-800']) }}>
    <div class="w-14 h-14 mx-auto rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400 dark:text-slate-500 mb-3.5">
        <x-icon :name="$icon" class="w-7 h-7" />
    </div>
    <h4 class="text-sm font-semibold text-slate-800 dark:text-slate-200">{{ $title }}</h4>
    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-sm mx-auto leading-relaxed">{{ $description }}</p>
    @if($action)
        <div class="mt-4">
            {{ $action }}
        </div>
    @endif
</div>

