<!DOCTYPE html>
<html lang="id" class="h-full scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0">
    <meta name="robots" content="index, follow">
    <meta name="theme-color" content="#166534"> <!-- Tailwind green-800 -->
    <title>@yield('title', 'Website Resmi Desa ' . env('VILLAGE_NAME', 'Damai'))</title>
    <meta name="description" content="@yield('meta_description', 'Website Resmi Desa ' . env('VILLAGE_NAME', 'Damai') . ', Kecamatan ' . env('VILLAGE_DISTRICT', 'Tanralili') . ', Kabupaten ' . env('VILLAGE_REGENCY', 'Maros') . '. Pusat informasi dan layanan digital desa.')">
    
    <!-- Icons & Favicon -->
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('manifest.json') }}">

    <!-- Open Graph / Medsos -->
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="@yield('title', 'Website Resmi Desa ' . env('VILLAGE_NAME', 'Damai'))">
    <meta property="og:description" content="@yield('meta_description', 'Website Resmi Desa ' . env('VILLAGE_NAME', 'Damai') . '. Pusat informasi resmi desa.')">
    <meta property="og:image" content="@yield('og_image', asset('images/default-og.jpg'))">
    <meta property="og:site_name" content="Desa {{ env('VILLAGE_NAME', 'Damai') }}">
    <!-- JSON-LD GovernmentOrganization Schema -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "GovernmentOrganization",
      "name": "Pemerintah Desa {{ env('VILLAGE_NAME', 'Damai') }}",
      "address": {
        "@type": "PostalAddress",
        "addressLocality": "Desa {{ env('VILLAGE_NAME', 'Damai') }}",
        "addressRegion": "Kecamatan {{ env('VILLAGE_DISTRICT', 'Tanralili') }}, Kabupaten {{ env('VILLAGE_REGENCY', 'Maros') }}",
        "addressCountry": "ID"
      },
      "url": "{{ url('/') }}"
    }
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="flex flex-col min-h-full bg-surface-50 text-surface-800 antialiased font-sans">
    <!-- Header Navigasi Utama -->
    <header class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-surface-200/80 shadow-xs" x-data="{ mobileMenuOpen: false }">
        <!-- Banner Pengayoman Resmi -->
        <div class="bg-primary-900 text-primary-100 text-[11px] sm:text-xs py-1.5 px-4">
            <div class="max-w-7xl mx-auto flex justify-between items-center">
                <span class="truncate">Portal Resmi Pemerintah Desa {{ env('VILLAGE_NAME', 'Damai') }}</span>
                <span class="hidden sm:inline text-primary-200">Kec. {{ env('VILLAGE_DISTRICT', 'Tanralili') }}, Kab. {{ env('VILLAGE_REGENCY', 'Maros') }}</span>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16 sm:h-20">
                <!-- Logo & Identitas Desa -->
                <a href="{{ url('/') }}" class="flex items-center gap-3 group">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-lg bg-primary-700 text-white flex items-center justify-center font-bold text-lg sm:text-xl shadow-xs group-hover:bg-primary-800 transition-colors flex-shrink-0">
                        🏛️
                    </div>
                    <div class="flex flex-col">
                        <span class="text-base sm:text-lg font-extrabold text-surface-900 leading-tight group-hover:text-primary-700 transition-colors">
                            Desa {{ env('VILLAGE_NAME', 'Damai') }}
                        </span>
                        <span class="text-[11px] sm:text-xs text-surface-500 font-medium">
                            Kec. {{ env('VILLAGE_DISTRICT', 'Tanralili') }}
                        </span>
                    </div>
                </a>

                <!-- Navigasi Desktop -->
                <nav class="hidden md:flex items-center gap-1 lg:gap-2">
                    <a href="{{ url('/') }}" class="px-3 py-2 rounded-lg text-sm font-semibold transition-colors {{ request()->is('/') ? 'text-primary-700 bg-primary-50' : 'text-surface-700 hover:text-primary-700 hover:bg-surface-100' }}">
                        Beranda
                    </a>
                    <a href="{{ url('/kegiatan') }}" class="px-3 py-2 rounded-lg text-sm font-semibold transition-colors {{ request()->is('kegiatan*') ? 'text-primary-700 bg-primary-50' : 'text-surface-700 hover:text-primary-700 hover:bg-surface-100' }}">
                        Kegiatan
                    </a>
                    <a href="{{ url('/lembaga') }}" class="px-3 py-2 rounded-lg text-sm font-semibold transition-colors {{ request()->is('lembaga*') ? 'text-primary-700 bg-primary-50' : 'text-surface-700 hover:text-primary-700 hover:bg-surface-100' }}">
                        Lembaga
                    </a>
                    <a href="{{ url('/peta') }}" class="px-3 py-2 rounded-lg text-sm font-semibold transition-colors {{ request()->is('peta*') ? 'text-primary-700 bg-primary-50' : 'text-surface-700 hover:text-primary-700 hover:bg-surface-100' }}">
                        Peta Lokasi
                    </a>
                    <a href="{{ url('/kontak') }}" class="px-3 py-2 rounded-lg text-sm font-semibold transition-colors {{ request()->is('kontak*') ? 'text-primary-700 bg-primary-50' : 'text-surface-700 hover:text-primary-700 hover:bg-surface-100' }}">
                        Kontak Penting
                    </a>
                </nav>

                <!-- Tombol Admin & Hamburger Mobile -->
                <div class="flex items-center gap-2">
                    <a href="{{ url('/admin') }}" class="hidden xs:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-surface-100 text-surface-700 hover:bg-surface-200 transition-colors">
                        <svg class="w-3.5 h-3.5 text-surface-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        <span>Portal Admin</span>
                    </a>

                    <!-- Hamburger Button -->
                    <button type="button" 
                            @click="mobileMenuOpen = !mobileMenuOpen"
                            class="md:hidden p-2 rounded-lg text-surface-600 hover:text-surface-900 hover:bg-surface-100 focus:outline-none focus:ring-2 focus:ring-primary-600"
                            aria-label="Buka menu navigasi"
                            :aria-expanded="mobileMenuOpen">
                        <svg class="w-6 h-6" x-show="!mobileMenuOpen" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                        <svg class="w-6 h-6" x-show="mobileMenuOpen" x-cloak fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Menu Mobile Overlay -->
        <div x-show="mobileMenuOpen" 
             x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             class="md:hidden border-b border-surface-200 bg-white px-4 pt-2 pb-4 shadow-lg space-y-1">
            <a href="{{ url('/') }}" class="block px-3 py-2.5 rounded-lg text-sm font-semibold {{ request()->is('/') ? 'text-primary-700 bg-primary-50' : 'text-surface-700 hover:bg-surface-100' }}">
                Beranda
            </a>
            <a href="{{ url('/kegiatan') }}" class="block px-3 py-2.5 rounded-lg text-sm font-semibold {{ request()->is('kegiatan*') ? 'text-primary-700 bg-primary-50' : 'text-surface-700 hover:bg-surface-100' }}">
                Kegiatan Desa
            </a>
            <a href="{{ url('/lembaga') }}" class="block px-3 py-2.5 rounded-lg text-sm font-semibold {{ request()->is('lembaga*') ? 'text-primary-700 bg-primary-50' : 'text-surface-700 hover:bg-surface-100' }}">
                Lembaga Desa
            </a>
            <a href="{{ url('/peta') }}" class="block px-3 py-2.5 rounded-lg text-sm font-semibold {{ request()->is('peta*') ? 'text-primary-700 bg-primary-50' : 'text-surface-700 hover:bg-surface-100' }}">
                Peta Lokasi Penting
            </a>
            <a href="{{ url('/kontak') }}" class="block px-3 py-2.5 rounded-lg text-sm font-semibold {{ request()->is('kontak*') ? 'text-primary-700 bg-primary-50' : 'text-surface-700 hover:bg-surface-100' }}">
                Direktori Kontak Penting
            </a>
            <div class="pt-2 border-t border-surface-100">
                <a href="{{ url('/admin') }}" class="flex items-center justify-center gap-2 w-full px-3 py-2.5 rounded-lg text-sm font-semibold bg-surface-100 text-surface-800 hover:bg-surface-200">
                    <span>Login Pengelola / Admin Desa</span>
                </a>
            </div>
        </div>
    </header>

    <!-- Konten Utama -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- Footer Utama -->
    <footer class="bg-surface-900 text-surface-300 mt-16 border-t border-surface-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 sm:gap-12">
                <!-- Kolom 1: Identitas Resmi -->
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-primary-700 text-white flex items-center justify-center font-bold text-lg">
                            🏛️
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-white leading-tight">Pemerintah Desa {{ env('VILLAGE_NAME', 'Damai') }}</h3>
                            <p class="text-xs text-surface-400">Kec. {{ env('VILLAGE_DISTRICT', 'Tanralili') }}, Kab. {{ env('VILLAGE_REGENCY', 'Maros') }}</p>
                        </div>
                    </div>
                    <p class="text-xs sm:text-sm text-surface-400 leading-relaxed">
                        Portal resmi sistem informasi digital desa. Menyajikan data profil, transparansi kegiatan, lokasi penting, dan layanan informasi kontak desa.
                    </p>
                </div>

                <!-- Kolom 2: Tautan Pintar Navigasi -->
                <div class="space-y-3">
                    <h4 class="text-sm font-bold text-white uppercase tracking-wider">Navigasi Utama</h4>
                    <ul class="space-y-2 text-xs sm:text-sm">
                        <li><a href="{{ url('/') }}" class="hover:text-primary-400 transition-colors">Beranda Utama</a></li>
                        <li><a href="{{ url('/kegiatan') }}" class="hover:text-primary-400 transition-colors">Kegiatan & Berita Desa</a></li>
                        <li><a href="{{ url('/lembaga') }}" class="hover:text-primary-400 transition-colors">Lembaga Kemasyarakatan Desa</a></li>
                        <li><a href="{{ url('/peta') }}" class="hover:text-primary-400 transition-colors">Peta Lokasi & Fasilitas Publik</a></li>
                        <li><a href="{{ url('/kontak') }}" class="hover:text-primary-400 transition-colors">Direktori Kontak Pejabat Desa</a></li>
                    </ul>
                </div>

                <!-- Kolom 3: Kontak & Medsos Resmi -->
                <div class="space-y-3">
                    <h4 class="text-sm font-bold text-white uppercase tracking-wider">Kontak & Sekretariat</h4>
                    <ul class="space-y-2.5 text-xs sm:text-sm text-surface-400">
                        <li class="flex items-start gap-2.5">
                            <svg class="w-4 h-4 text-primary-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span>Kantor Kepala Desa {{ env('VILLAGE_NAME', 'Damai') }}, Jl. Raya Balai Desa No. 01</span>
                        </li>
                        <li class="flex items-center gap-2.5">
                            <svg class="w-4 h-4 text-primary-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <span>{{ 'pemdes@' . Str::slug(env('VILLAGE_NAME', 'Damai')) . '.go.id' }}</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Hak Cipta & KKN Footnote -->
            <div class="mt-12 pt-6 border-t border-surface-800 flex flex-col sm:flex-row justify-between items-center gap-4 text-xs text-surface-500">
                <p>© {{ date('Y') }} Pemerintah Desa {{ env('VILLAGE_NAME', 'Damai') }}. Seluruh hak cipta dilindungi.</p>
                <p class="text-surface-400">Dikembangkan melalui Program KKN Mahasiswa</p>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
