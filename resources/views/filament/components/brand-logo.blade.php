<div class="flex items-center gap-3">
    <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-emerald-600 text-white font-bold shadow-sm">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
        </svg>
    </div>
    <div class="flex flex-col text-left">
        <span class="text-base font-bold leading-tight text-gray-900 dark:text-white">
            Desa {{ env('VILLAGE_NAME', 'Damai') }}
        </span>
        <span class="text-xs text-gray-500 dark:text-gray-400">
            Kecamatan {{ env('VILLAGE_DISTRICT', 'Tanralili') }}, Kabupaten {{ env('VILLAGE_REGENCY', 'Maros') }}
        </span>
    </div>
</div>
