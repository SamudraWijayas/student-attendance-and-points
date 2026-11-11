{{-- <!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50 text-slate-900 antialiased">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Dashboard' }} - SMK Tri Sukses Natar</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
        }

        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="h-full flex overflow-hidden bg-slate-50 text-slate-900 dark:bg-slate-950 dark:text-slate-100"
    x-data="{
        sidebarOpen: false,
        sidebarCollapsed: false,
        theme: 'light',
    
        init() {
            const savedTheme = localStorage.getItem('smkts-theme') || 'light';
    
            this.theme = savedTheme;
            document.documentElement.classList.toggle('dark', savedTheme === 'dark');
        },
    
        toggleTheme() {
            this.theme = this.theme === 'light' ? 'dark' : 'light';
    
            document.documentElement.classList.toggle('dark', this.theme === 'dark');
    
            localStorage.setItem('smkts-theme', this.theme);
        }
    }" x-init="init()">

    <!-- Mobile Sidebar Backdrop -->
    <div x-show="sidebarOpen" x-transition:enter="transition-opacity ease-linear duration-300"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0" class="fixed inset-0 bg-slate-900/60 z-40 lg:hidden backdrop-blur-xs"
        x-on:click="sidebarOpen = false" x-cloak></div>

    <!-- SIDEBAR -->
    <aside
        :class="{
            'translate-x-0': sidebarOpen,
            '-translate-x-full': !sidebarOpen,
            'lg:w-68': !sidebarCollapsed,
            'lg:w-20': sidebarCollapsed
        }"
        class="app-sidebar fixed inset-y-0 left-0 z-50 flex flex-col bg-slate-900 text-slate-300 border-r border-slate-800 transition-all duration-300 ease-in-out lg:static lg:translate-x-0 shrink-0 w-68">
        <!-- Brand Header -->
        <div class="h-18 px-5 flex items-center justify-between border-b border-slate-800/80 bg-slate-950/40">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 overflow-hidden group">
                <div
                    class="w-10 h-10 rounded-xl bg-gradient-to-br from-teal-500 to-emerald-600 flex items-center justify-center text-white font-bold text-lg shadow-md shadow-teal-500/20 shrink-0">
                    <x-icon name="graduation-cap" class="w-6 h-6" />
                </div>
                <div x-show="!sidebarCollapsed" class="transition-opacity duration-200">
                    <h1
                        class="text-sm font-bold text-white tracking-tight leading-tight group-hover:text-teal-400 transition-colors">
                        SMK TRI SUKSES</h1>
                    <p class="text-[11px] font-medium text-teal-400 tracking-wider">NATAR LAMPUNG</p>
                </div>
            </a>
            <button type="button" x-on:click="sidebarCollapsed = !sidebarCollapsed"
                class="hidden lg:flex p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition-colors"
                title="Toggle Sidebar">
                <x-icon name="panel-left-close" class="w-4 h-4" />
            </button>
            <button type="button" x-on:click="sidebarOpen = false"
                class="lg:hidden p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800">
                <x-icon name="x" class="w-5 h-5" />
            </button>
        </div>

        <!-- Sidebar Navigation -->
        <nav class="flex-1 overflow-y-auto px-3.5 py-4 space-y-6 text-xs scrollbar-thin scrollbar-thumb-slate-800">

            <!-- DASHBOARD -->
            <div>
                <p x-show="!sidebarCollapsed"
                    class="px-3 mb-2 text-[10px] font-bold uppercase tracking-wider text-slate-500">Dashboard</p>
                <a href="{{ route('dashboard') }}"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition-all {{ request()->routeIs('dashboard') ? 'bg-teal-600 text-white shadow-sm shadow-teal-600/30' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                    <x-icon name="layout-dashboard" class="w-5 h-5 shrink-0" />
                    <span x-show="!sidebarCollapsed" class="truncate text-sm">Dashboard Utama</span>
                </a>
            </div>

            <!-- DATA MASTER -->
            <div>
                <p x-show="!sidebarCollapsed"
                    class="px-3 mb-2 text-[10px] font-bold uppercase tracking-wider text-slate-500">Data Master</p>
                <div class="space-y-1">
                    <a href="{{ route('students.index') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-xl font-medium transition-all {{ request()->routeIs('students.*') ? 'bg-teal-600/20 text-teal-400 font-semibold border border-teal-500/30' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                        <x-icon name="users" class="w-4.5 h-4.5 shrink-0" />
                        <span x-show="!sidebarCollapsed" class="truncate text-sm">Data Siswa</span>
                    </a>
                    <a href="{{ route('classes.index') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-xl font-medium transition-all {{ request()->routeIs('classes.*') ? 'bg-teal-600/20 text-teal-400 font-semibold border border-teal-500/30' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                        <x-icon name="layers" class="w-4.5 h-4.5 shrink-0" />
                        <span x-show="!sidebarCollapsed" class="truncate text-sm">Data Kelas</span>
                    </a>
                    <a href="{{ route('majors.index') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-xl font-medium transition-all {{ request()->routeIs('majors.*') ? 'bg-teal-600/20 text-teal-400 font-semibold border border-teal-500/30' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                        <x-icon name="book-open" class="w-4.5 h-4.5 shrink-0" />
                        <span x-show="!sidebarCollapsed" class="truncate text-sm">Jurusan</span>
                    </a>
                    <a href="{{ route('academic-years.index') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-xl font-medium transition-all {{ request()->routeIs('academic-years.*') ? 'bg-teal-600/20 text-teal-400 font-semibold border border-teal-500/30' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                        <x-icon name="calendar" class="w-4.5 h-4.5 shrink-0" />
                        <span x-show="!sidebarCollapsed" class="truncate text-sm">Tahun Ajaran</span>
                    </a>
                    <a href="{{ route('semesters.index') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-xl font-medium transition-all {{ request()->routeIs('semesters.*') ? 'bg-teal-600/20 text-teal-400 font-semibold border border-teal-500/30' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                        <x-icon name="clock" class="w-4.5 h-4.5 shrink-0" />
                        <span x-show="!sidebarCollapsed" class="truncate text-sm">Semester</span>
                    </a>
                </div>
            </div>

            <!-- ABSENSI -->
            <div>
                <p x-show="!sidebarCollapsed"
                    class="px-3 mb-2 text-[10px] font-bold uppercase tracking-wider text-slate-500">Absensi</p>
                <div class="space-y-1">
                    <a href="{{ route('attendance.scan') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-xl font-medium transition-all {{ request()->routeIs('attendance.scan') ? 'bg-emerald-600 text-white shadow-sm shadow-emerald-600/30 font-semibold' : 'text-emerald-400 hover:bg-emerald-950/40 hover:text-emerald-300' }}">
                        <x-icon name="qr-code" class="w-4.5 h-4.5 shrink-0" />
                        <span x-show="!sidebarCollapsed" class="truncate text-sm font-semibold">Scan QR Code</span>
                    </a>
                    <a href="{{ route('attendance.monitoring') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-xl font-medium transition-all {{ request()->routeIs('attendance.monitoring') ? 'bg-teal-600/20 text-teal-400 font-semibold border border-teal-500/30' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                        <x-icon name="activity" class="w-4.5 h-4.5 shrink-0" />
                        <span x-show="!sidebarCollapsed" class="truncate text-sm">Monitoring Absensi</span>
                    </a>
                    <a href="{{ route('attendance.history') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-xl font-medium transition-all {{ request()->routeIs('attendance.history') ? 'bg-teal-600/20 text-teal-400 font-semibold border border-teal-500/30' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                        <x-icon name="history" class="w-4.5 h-4.5 shrink-0" />
                        <span x-show="!sidebarCollapsed" class="truncate text-sm">Riwayat Absensi</span>
                    </a>
                    <a href="{{ route('attendance.settings') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-xl font-medium transition-all {{ request()->routeIs('attendance.settings') ? 'bg-teal-600/20 text-teal-400 font-semibold border border-teal-500/30' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                        <x-icon name="sliders" class="w-4.5 h-4.5 shrink-0" />
                        <span x-show="!sidebarCollapsed" class="truncate text-sm">Pengaturan Absensi</span>
                    </a>
                </div>
            </div>

            <!-- POIN KARAKTER -->
            <div>
                <p x-show="!sidebarCollapsed"
                    class="px-3 mb-2 text-[10px] font-bold uppercase tracking-wider text-slate-500">Poin Karakter</p>
                <div class="space-y-1">
                    <a href="{{ route('points.create') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-xl font-medium transition-all {{ request()->routeIs('points.create') ? 'bg-teal-600 text-white font-semibold shadow-sm' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                        <x-icon name="plus-circle" class="w-4.5 h-4.5 shrink-0" />
                        <span x-show="!sidebarCollapsed" class="truncate text-sm">Input Poin</span>
                    </a>
                    <a href="{{ route('points.prestasi') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-xl font-medium transition-all {{ request()->routeIs('points.prestasi') ? 'bg-teal-600/20 text-teal-400 font-semibold border border-teal-500/30' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                        <x-icon name="award" class="w-4.5 h-4.5 shrink-0" />
                        <span x-show="!sidebarCollapsed" class="truncate text-sm">Data Prestasi</span>
                    </a>
                    <a href="{{ route('points.pelanggaran') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-xl font-medium transition-all {{ request()->routeIs('points.pelanggaran') ? 'bg-teal-600/20 text-teal-400 font-semibold border border-teal-500/30' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                        <x-icon name="alert-triangle" class="w-4.5 h-4.5 shrink-0" />
                        <span x-show="!sidebarCollapsed" class="truncate text-sm">Data Pelanggaran</span>
                    </a>
                    <a href="{{ route('points.history') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-xl font-medium transition-all {{ request()->routeIs('points.history') ? 'bg-teal-600/20 text-teal-400 font-semibold border border-teal-500/30' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                        <x-icon name="list-checks" class="w-4.5 h-4.5 shrink-0" />
                        <span x-show="!sidebarCollapsed" class="truncate text-sm">Riwayat Transaksi Poin</span>
                    </a>
                    <a href="{{ route('follow-ups.index') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-xl font-medium transition-all {{ request()->routeIs('follow-ups.*') ? 'bg-teal-600/20 text-teal-400 font-semibold border border-teal-500/30' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                        <x-icon name="shield-alert" class="w-4.5 h-4.5 shrink-0" />
                        <span x-show="!sidebarCollapsed" class="truncate text-sm">Tindak Lanjut & SP</span>
                    </a>
                    <a href="{{ route('remedials.index') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-xl font-medium transition-all {{ request()->routeIs('remedials.*') ? 'bg-teal-600/20 text-teal-400 font-semibold border border-teal-500/30' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                        <x-icon name="rotate-ccw" class="w-4.5 h-4.5 shrink-0" />
                        <span x-show="!sidebarCollapsed" class="truncate text-sm">Remedial Poin</span>
                    </a>
                    <a href="{{ route('juara-karakter.index') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-xl font-medium transition-all {{ request()->routeIs('juara-karakter.*') ? 'bg-teal-600/20 text-amber-400 font-semibold border border-amber-500/30' : 'text-amber-300/90 hover:bg-slate-800/70 hover:text-amber-200' }}">
                        <x-icon name="trophy" class="w-4.5 h-4.5 shrink-0" />
                        <span x-show="!sidebarCollapsed" class="truncate text-sm font-semibold">Juara Karakter</span>
                    </a>
                    <a href="{{ route('disciplinary-book.index') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-xl font-medium transition-all {{ request()->routeIs('disciplinary-book.*') ? 'bg-teal-600/20 text-teal-400 font-semibold border border-teal-500/30' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                        <x-icon name="book-marked" class="w-4.5 h-4.5 shrink-0" />
                        <span x-show="!sidebarCollapsed" class="truncate text-sm">Buku Induk Disiplin</span>
                    </a>
                </div>
            </div>

            <!-- LAPORAN -->
            <div>
                <p x-show="!sidebarCollapsed"
                    class="px-3 mb-2 text-[10px] font-bold uppercase tracking-wider text-slate-500">Laporan & Rekap</p>
                <div class="space-y-1">
                    <a href="{{ route('reports.attendance') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-xl font-medium transition-all {{ request()->routeIs('reports.attendance*') ? 'bg-teal-600/20 text-teal-400 font-semibold border border-teal-500/30' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                        <x-icon name="file-spreadsheet" class="w-4.5 h-4.5 shrink-0" />
                        <span x-show="!sidebarCollapsed" class="truncate text-sm">Laporan Absensi</span>
                    </a>
                    <a href="{{ route('reports.points') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-xl font-medium transition-all {{ request()->routeIs('reports.points*') ? 'bg-teal-600/20 text-teal-400 font-semibold border border-teal-500/30' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                        <x-icon name="file-text" class="w-4.5 h-4.5 shrink-0" />
                        <span x-show="!sidebarCollapsed" class="truncate text-sm">Laporan Poin Karakter</span>
                    </a>
                </div>
            </div>

            <!-- PENGATURAN -->
            <div>
                <p x-show="!sidebarCollapsed"
                    class="px-3 mb-2 text-[10px] font-bold uppercase tracking-wider text-slate-500">Pengaturan</p>
                <div class="space-y-1">
                    <a href="{{ route('settings.school') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-xl font-medium transition-all {{ request()->routeIs('settings.school') ? 'bg-teal-600/20 text-teal-400 font-semibold border border-teal-500/30' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                        <x-icon name="school" class="w-4.5 h-4.5 shrink-0" />
                        <span x-show="!sidebarCollapsed" class="truncate text-sm">Profil Sekolah</span>
                    </a>
                    <a href="{{ route('point-categories.index') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-xl font-medium transition-all {{ request()->routeIs('point-categories.*') ? 'bg-teal-600/20 text-teal-400 font-semibold border border-teal-500/30' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                        <x-icon name="tags" class="w-4.5 h-4.5 shrink-0" />
                        <span x-show="!sidebarCollapsed" class="truncate text-sm">Kategori Poin</span>
                    </a>
                    <a href="{{ route('settings.account') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-xl font-medium transition-all {{ request()->routeIs('settings.account') ? 'bg-teal-600/20 text-teal-400 font-semibold border border-teal-500/30' : 'text-slate-300 hover:bg-slate-800/70 hover:text-white' }}">
                        <x-icon name="user-cog" class="w-4.5 h-4.5 shrink-0" />
                        <span x-show="!sidebarCollapsed" class="truncate text-sm">Akun Admin</span>
                    </a>
                </div>
            </div>

        </nav>

        <!-- User Profile & Logout Footer -->
        <div class="p-3.5 border-t border-slate-800/80 bg-slate-950/40">
            <div class="flex items-center justify-between gap-3">
                <div class="flex items-center gap-3 overflow-hidden">
                    <div
                        class="w-9 h-9 rounded-xl bg-teal-800 text-teal-200 flex items-center justify-center font-bold text-sm shrink-0 border border-teal-700">
                        {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                    </div>
                    <div x-show="!sidebarCollapsed" class="truncate">
                        <p class="text-xs font-semibold text-white truncate">{{ auth()->user()->name ?? 'Admin' }}</p>
                        <p class="text-[11px] text-slate-400 truncate">
                            {{ auth()->user()->email ?? 'admin@smkts.sch.id' }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="p-2 rounded-xl text-slate-400 hover:text-rose-400 hover:bg-rose-950/40 transition-colors cursor-pointer"
                        title="Keluar dari Sistem">
                        <x-icon name="log-out" class="w-4.5 h-4.5" />
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- MAIN CONTENT AREA -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

        <!-- Top Header Navbar -->
        <header
            class="h-18 bg-white dark:bg-slate-900 border-b border-slate-200/80 dark:border-slate-800 px-4 lg:px-8 flex items-center justify-between shrink-0 shadow-xs">
            <div class="flex items-center gap-3 lg:gap-4">
                <button type="button" x-on:click="sidebarOpen = true"
                    class="lg:hidden p-2 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition-colors">
                    <x-icon name="menu" class="w-5 h-5" />
                </button>
                <div>
                    <h2 class="text-lg lg:text-xl font-bold text-slate-900 dark:text-white tracking-tight">
                        {{ $headerTitle ?? 'Dashboard' }}
                    </h2>
                    @if (isset($headerSubtitle))
                        <p class="text-xs text-slate-500 dark:text-slate-400 hidden sm:block">{{ $headerSubtitle }}
                        </p>
                    @endif
                </div>
            </div>

            <!-- Header Quick Stats & Controls -->
            <div class="flex items-center gap-3 lg:gap-4">
                <!-- Semester Badge -->
                @php
                    $activeSem = \App\Models\Semester::active();
                @endphp
                @if ($activeSem)
                    <div
                        class="hidden md:flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-teal-50 border border-teal-200 text-teal-800 dark:bg-teal-950/40 dark:border-teal-800 dark:text-teal-300 text-xs font-semibold">
                        <x-icon name="calendar-check" class="w-4 h-4 text-teal-600 dark:text-teal-400" />
                        <span>TA {{ $activeSem->academicYear?->name }} •
                            {{ $activeSem->semester_type->label() }}</span>
                    </div>
                @endif

                <!-- Server Digital Clock -->
                <div x-data="{
                    time: '',
                    updateTime() {
                        const now = new Date();
                        this.time = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit', timeZone: 'Asia/Jakarta' }) + ' WIB';
                    }
                }" x-init="updateTime();
                setInterval(() => updateTime(), 1000)"
                    class="hidden sm:flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-mono text-xs font-bold">
                    <x-icon name="clock" class="w-3.5 h-3.5 text-teal-600" />
                    <span x-text="time">--:--:-- WIB</span>
                </div>
                <!-- Theme Toggle -->
                <button type="button" @click="toggleTheme()"
                    class="inline-flex items-center justify-center w-10 h-10 rounded-xl
           bg-slate-100 text-slate-600 hover:bg-slate-200
           dark:bg-slate-800 dark:text-amber-300 dark:hover:bg-slate-700
           transition-all duration-200"
                    :title="theme === 'light' ? 'Aktifkan Dark Mode' : 'Aktifkan Light Mode'"
                    aria-label="Ganti tema">
                    <template x-if="theme === 'light'">
                        <x-icon name="moon" class="w-5 h-5" />
                    </template>

                    <template x-if="theme === 'dark'">
                        <x-icon name="sun" class="w-5 h-5" />
                    </template>
                </button>
                <!-- Quick Action: Scan QR Button -->
                <a href="{{ route('attendance.scan') }}"
                    class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-teal-700 hover:bg-teal-800 text-white text-xs font-semibold shadow-sm shadow-teal-700/20 transition-all">
                    <x-icon name="scan-line" class="w-4 h-4" />
                    <span class="hidden sm:inline">Buka Scanner</span>
                </a>
            </div>
        </header>

        <!-- Flash Messages -->
        <div class="px-4 lg:px-8 pt-4">
            @if (session('success'))
                <x-alert type="success" class="mb-4">
                    {{ session('success') }}
                </x-alert>
            @endif

            @if (session('error'))
                <x-alert type="error" class="mb-4">
                    {{ session('error') }}
                </x-alert>
            @endif

            @if (session('info'))
                <x-alert type="info" class="mb-4">
                    {{ session('info') }}
                </x-alert>
            @endif
        </div>

        <!-- Main Page Scrollable Content -->
        <main class="flex-1 overflow-y-auto px-4 lg:px-8 pb-12">
            {{ $slot }}
        </main>
    </div>

    @livewireScripts
</body>

</html> --}}
