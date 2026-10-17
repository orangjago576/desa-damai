<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex items-center space-x-4">
            <div class="p-3 bg-primary-100 dark:bg-primary-900/40 rounded-full text-primary-600 dark:text-primary-400">
                <x-heroicon-o-home-modern class="w-8 h-8" />
            </div>
            <div>
                <h2 class="text-xl font-bold tracking-tight text-gray-950 dark:text-white">
                    Selamat Datang di Panel Pengelolaan Website Resmi Desa {{ env('VILLAGE_NAME', 'Damai') }}
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Halo, <span class="font-semibold text-gray-700 dark:text-gray-200">{{ auth()->user()->name }}</span> ({{ auth()->user()->role === 'superadmin' ? 'Super Admin' : 'Admin Pengelola' }}). Gunakan panel navigasi di sebelah kiri untuk memperbarui data kegiatan, struktur lembaga desa, peta lokasi, dan kontak penting.
                </p>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
