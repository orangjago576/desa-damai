<div x-data="{
    lat: @entangle($getStatePath() . '.latitude'),
    lng: @entangle($getStatePath() . '.longitude'),
    map: null,
    marker: null,
    searchQuery: '',
    isSearching: false,
    geoLoading: false,
    searchError: null,

    initMap() {
        if (!window.L) {
            if (!document.getElementById('leaflet-css')) {
                const link = document.createElement('link');
                link.id = 'leaflet-css';
                link.rel = 'stylesheet';
                link.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
                document.head.appendChild(link);
            }

            if (!document.getElementById('leaflet-js')) {
                const script = document.createElement('script');
                script.id = 'leaflet-js';
                script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
                script.onload = () => {
                    this.$nextTick(() => this.setupLeaflet());
                };
                document.head.appendChild(script);
            } else {
                const checkL = setInterval(() => {
                    if (window.L) {
                        clearInterval(checkL);
                        this.$nextTick(() => this.setupLeaflet());
                    }
                }, 100);
            }
        } else {
            this.$nextTick(() => this.setupLeaflet());
        }
    },

    setupLeaflet() {
        if (this.map || !this.$refs.mapContainer) return;

        const defaultLat = this.lat ? parseFloat(this.lat) : -5.064099;
        const defaultLng = this.lng ? parseFloat(this.lng) : 119.608050;

        this.map = L.map(this.$refs.mapContainer).setView([defaultLat, defaultLng], 15);

        // Tile Google Maps Hybrid & Jalan (Sangat kencang, CDN Google, tanpa API Key)
        const streetLayer = L.tileLayer('https://mt1.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
            maxZoom: 20,
            attribution: '© Google Maps'
        }).addTo(this.map);

        // Foto Satelit Asli Google Maps
        const satelliteLayer = L.tileLayer('https://mt1.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
            maxZoom: 20,
            attribution: '© Google Maps Satellite'
        });

        // Kontrol ganti peta Jalan vs Satelit di pojok kanan atas
        L.control.layers({
            'Google Maps (Jalan)': streetLayer,
            'Google Maps (Satelit)': satelliteLayer
        }, null, { position: 'topright' }).addTo(this.map);

        this.marker = L.marker([defaultLat, defaultLng], { draggable: true }).addTo(this.map);

        this.marker.on('dragend', (e) => {
            const pos = e.target.getLatLng();
            this.updateCoords(pos.lat, pos.lng);
        });

        this.map.on('click', (e) => {
            this.marker.setLatLng(e.latlng);
            this.updateCoords(e.latlng.lat, e.latlng.lng);
        });

        this.$watch('lat', value => {
            if (value && this.lng && this.marker && this.map) {
                const newPos = [parseFloat(value), parseFloat(this.lng)];
                this.marker.setLatLng(newPos);
                this.map.panTo(newPos);
            }
        });

        this.$watch('lng', value => {
            if (value && this.lat && this.marker && this.map) {
                const newPos = [parseFloat(this.lat), parseFloat(value)];
                this.marker.setLatLng(newPos);
                this.map.panTo(newPos);
            }
        });

        setTimeout(() => {
            if (this.map) {
                this.map.invalidateSize();
            }
        }, 400);
    },

    updateCoords(lat, lng) {
        const fixedLat = parseFloat(lat).toFixed(6);
        const fixedLng = parseFloat(lng).toFixed(6);
        this.lat = fixedLat;
        this.lng = fixedLng;
        $wire.set('data.latitude', fixedLat);
        $wire.set('data.longitude', fixedLng);
    },

    searchLocation() {
        if (!this.searchQuery.trim()) return;
        this.isSearching = true;
        this.searchError = null;

        const url = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(this.searchQuery + ', Tanralili, Maros')}&limit=1`;

        fetch(url)
            .then(res => res.json())
            .then(data => {
                this.isSearching = false;
                if (data && data.length > 0) {
                    const foundLat = parseFloat(data[0].lat);
                    const foundLng = parseFloat(data[0].lon);
                    const newPos = [foundLat, foundLng];
                    if (this.marker) this.marker.setLatLng(newPos);
                    if (this.map) this.map.setView(newPos, 16);
                    this.updateCoords(foundLat, foundLng);
                } else {
                    return fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(this.searchQuery)}&limit=1`)
                        .then(r => r.json())
                        .then(fallbackData => {
                            if (fallbackData && fallbackData.length > 0) {
                                const fLat = parseFloat(fallbackData[0].lat);
                                const fLng = parseFloat(fallbackData[0].lon);
                                const nPos = [fLat, fLng];
                                if (this.marker) this.marker.setLatLng(nPos);
                                if (this.map) this.map.setView(nPos, 16);
                                this.updateCoords(fLat, fLng);
                            } else {
                                this.searchError = 'Lokasi tidak ditemukan. Silakan geser pin manual ke titik yang diinginkan.';
                            }
                        });
                }
            })
            .catch(() => {
                this.isSearching = false;
                this.searchError = 'Gagal mencari lokasi. Pastikan terhubung ke internet.';
            });
    },

    getCurrentLocation() {
        if (!navigator.geolocation) {
            alert('Browser tidak mendukung deteksi lokasi otomatis.');
            return;
        }

        this.geoLoading = true;
        this.searchError = null;

        navigator.geolocation.getCurrentPosition(
            (position) => {
                this.geoLoading = false;
                const userLat = position.coords.latitude;
                const userLng = position.coords.longitude;
                const pos = [userLat, userLng];
                if (this.marker) this.marker.setLatLng(pos);
                if (this.map) this.map.setView(pos, 17);
                this.updateCoords(userLat, userLng);
            },
            (err) => {
                this.geoLoading = false;
                alert('Gagal mengambil GPS. Pastikan izin lokasi aktif pada peramban/HP.');
            },
            { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
        );
    }
}" x-init="initMap()" class="space-y-3">

    <!-- Bar Pencarian Lokasi & Tombol GPS -->
    <div style="display: flex; gap: 8px; flex-wrap: wrap;">
        <div style="position: relative; flex: 1; min-width: 240px;">
            <input 
                type="text" 
                x-model="searchQuery" 
                @keydown.enter.prevent="searchLocation()"
                placeholder="Ketik nama dusun, jalan, atau tempat (contoh: Dusun Billa)..."
                style="width: 100%; font-size: 13px; border-radius: 8px; border: 1px solid #d1d5db; padding: 8px 65px 8px 34px; box-sizing: border-box;"
            >
            <div style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); pointer-events: none; color: #9ca3af;">
                <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <button 
                type="button" 
                @click="searchLocation()"
                :disabled="isSearching"
                style="position: absolute; right: 5px; top: 5px; bottom: 5px; background-color: #15803d; color: white; border: none; border-radius: 6px; padding: 0 12px; font-size: 12px; font-weight: 600; cursor: pointer;">
                <span x-show="!isSearching">Cari</span>
                <span x-show="isSearching">Mencari...</span>
            </button>
        </div>

        <button 
            type="button" 
            @click="getCurrentLocation()"
            :disabled="geoLoading"
            style="display: inline-flex; align-items: center; gap: 6px; background-color: #059669; color: white; border: none; border-radius: 8px; padding: 8px 14px; font-size: 13px; font-weight: 600; cursor: pointer; white-space: nowrap;">
            <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <span x-show="!geoLoading">📍 Lokasi Saya Saat Ini</span>
            <span x-show="geoLoading">Mencari GPS...</span>
        </button>
    </div>

    <template x-if="searchError">
        <p style="font-size: 12px; color: #d97706; margin: 0;" x-text="searchError"></p>
    </template>

    <!-- Info Petunjuk & Koordinat Terpilih -->
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; font-size: 12px; background-color: #f9fafb; padding: 8px 12px; border-radius: 8px; border: 1px solid #e5e7eb; gap: 8px;">
        <span style="color: #4b5563; display: flex; align-items: center; gap: 6px;">
            <span style="width: 8px; height: 8px; border-radius: 50%; background-color: #10b981; display: inline-block;"></span>
            <strong>Petunjuk:</strong> Klik peta atau geser pin biru ke titik bangunan yang dituju.
        </span>
        <span style="background-color: white; padding: 3px 8px; border-radius: 4px; border: 1px solid #e5e7eb; font-family: monospace; font-size: 11px; color: #166534;">
            📍 Terpilih: <strong x-text="lat && lng ? lat + ', ' + lng : 'Belum dipilih'"></strong>
        </span>
    </div>

    <!-- Peta Leaflet Utama (Dengan style inline eksplisit agar tidak tertelan) -->
    <div x-ref="mapContainer" style="width: 100%; height: 380px; min-height: 380px; border-radius: 8px; border: 1px solid #d1d5db; position: relative; z-index: 1; background-color: #f3f4f6;"></div>
</div>
