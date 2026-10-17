<div x-data="{
    lat: @entangle($getStatePath() . '.latitude'),
    lng: @entangle($getStatePath() . '.longitude'),
    map: null,
    marker: null,
    initMap() {
        if (typeof L === 'undefined') {
            const link = document.createElement('link');
            link.rel = 'stylesheet';
            link.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
            document.head.appendChild(link);

            const script = document.createElement('script');
            script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
            script.onload = () => this.setupLeaflet();
            document.head.appendChild(script);
        } else {
            this.setupLeaflet();
        }
    },
    setupLeaflet() {
        const defaultLat = this.lat ? parseFloat(this.lat) : -5.064099;
        const defaultLng = this.lng ? parseFloat(this.lng) : 119.608050;

        this.map = L.map(this.$refs.mapContainer).setView([defaultLat, defaultLng], 14);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(this.map);

        this.marker = L.marker([defaultLat, defaultLng], { draggable: true }).addTo(this.map);

        this.marker.on('dragend', (e) => {
            const position = e.target.getLatLng();
            this.lat = position.lat.toFixed(6);
            this.lng = position.lng.toFixed(6);
            $wire.set('data.latitude', this.lat);
            $wire.set('data.longitude', this.lng);
        });

        this.map.on('click', (e) => {
            const clickLat = e.latlng.lat.toFixed(6);
            const clickLng = e.latlng.lng.toFixed(6);
            this.marker.setLatLng(e.latlng);
            this.lat = clickLat;
            this.lng = clickLng;
            $wire.set('data.latitude', clickLat);
            $wire.set('data.longitude', clickLng);
        });

        this.$watch('lat', value => {
            if (value && this.lng && this.marker) {
                const newPos = [parseFloat(value), parseFloat(this.lng)];
                this.marker.setLatLng(newPos);
                this.map.panTo(newPos);
            }
        });

        this.$watch('lng', value => {
            if (value && this.lat && this.marker) {
                const newPos = [parseFloat(this.lat), parseFloat(value)];
                this.marker.setLatLng(newPos);
                this.map.panTo(newPos);
            }
        });

        setTimeout(() => {
            this.map.invalidateSize();
        }, 300);
    }
}" x-init="initMap()" class="space-y-2">
    <div class="flex items-center justify-between text-xs font-medium text-gray-700 dark:text-gray-300">
        <span>Klik pada peta atau geser penanda untuk memilih koordinat otomatis:</span>
        <span class="text-primary-600 font-semibold" x-text="lat && lng ? lat + ', ' + lng : 'Belum dipilih'"></span>
    </div>
    <div x-ref="mapContainer" class="w-full h-72 rounded-lg border border-gray-300 dark:border-gray-700 shadow-inner z-0" style="min-height: 280px;"></div>
</div>
