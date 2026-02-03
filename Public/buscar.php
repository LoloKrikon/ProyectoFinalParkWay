<!DOCTYPE html>
<html lang="es">

<head>
    <!-- Configuración para la página de búsqueda con mapa -->
    <?php 
        $pageTitle = 'ParkWay - Buscar Aparcamiento';
        
        // CSS extra para Leaflet (Mapas) y Routing Machine
        // Importante: Estos se cargan dinámicamente en el header común
        $extraCss = '<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
                     <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.css" />';
    ?>
    <!-- Inclusión del head común -->
    <?php include 'includes/head.php'; ?>
    
    <style>
        /*
           ESTILOS ESPECÍFICOS DEL MAPA
           ========================================= */

        /* Input de búsqueda flotante mejorado */
        .search-input-alt {
            background: rgba(0, 0, 0, 0.4); /* Fondo más oscuro para legibilidad */
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 14px;
            border-radius: 12px;
            color: white;
            width: 100%;
            outline: none;
            transition: all 0.3s ease;
            backdrop-filter: blur(5px); /* Efecto vidrio */
            font-size: 1rem;
        }

        .search-input-alt:focus {
            border-color: var(--accent-main);
            background: rgba(0, 0, 0, 0.6);
            box-shadow: 0 0 0 2px rgba(124, 58, 237, 0.2);
        }

        /* Ocultar el contenedor de rutas por defecto de Leaflet para personalizar la UI nosotros mismos */
        .leaflet-control-container .leaflet-routing-container-hide {
            display: none;
        }

        /* Panel de instrucciones personalizado */
        .instructions-panel {
            margin-top: 20px;
            max-height: 400px;
            overflow-y: auto;
            border-top: 1px solid rgba(255,255,255,0.05);
            padding-top: 10px;
        }

        /* Scrollbar personalizado para las instrucciones */
        .instructions-panel::-webkit-scrollbar {
            width: 6px;
        }
        .instructions-panel::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.02);
        }
        .instructions-panel::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 3px;
        }
    </style>
</head>

<body>
    <section class="view">
        <!-- 
            Barra de navegación.
            'showProfile' habilita el avatar del usuario.
            'backLink' define a dónde regresa la flecha de "volver".
        -->
        <?php 
            $showProfile = true; 
            $backLink = 'plataformas.php';
            include 'includes/navbar.php'; 
        ?>

        <div class="map-layout">
            <!-- PANEL LATERAL: Entrada de búsqueda e instrucciones -->
            <div class="map-panel">
                <h2 style="display: flex; align-items: center; gap: 10px;">
                    <i class="fa-solid fa-map-location-dot" style="color: var(--accent-main);"></i> 
                    Buscar Plaza
                </h2>
                
                <!-- Input de búsqueda -->
                <div class="map-input-group">
                    <input id="searchInput" class="search-input-alt" placeholder="¿A dónde quieres ir hoy?" />
                    
                    <button onclick="iniciarNavegacion()" class="primary-btn" style="width: 100%; margin-top: 15px;">
                        <i class="fa-solid fa-location-arrow"></i> Navegar
                    </button>
                </div>

                <!-- Contenedor de instrucciones de ruta (Se llena dinámicamente con JS) -->
                <div id="routeInstructions" class="instructions-panel">
                    <div style="text-align: center; padding: 40px 20px; opacity: 0.6; display: flex; flex-direction: column; align-items: center; gap: 10px;">
                        <i class="fa-solid fa-route" style="font-size: 2rem;"></i>
                        <p>Ingresa un destino para ver la ruta y plazas disponibles.</p>
                    </div>
                </div>
            </div>

            <!-- CONTENEDOR DEL MAPA LEAFLET -->
            <div class="map-container">
                <div id="map"></div>
            </div>
        </div>
    </section>

    <!-- LIBRERÍAS DE MAPAS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.min.js"></script>


    <!-- LÓGICA JAVASCRIPT DEL MAPA -->
    <script>
        // 1. Inicialización del mapa (centrado en Madrid por defecto)
        const map = L.map('map').setView([40.4168, -3.7038], 15);

        // 2. Capa de mapa: Usamos estilo 'Dark Matter' para coincidir con el tema oscuro de la App
        L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; OpenStreetMap contributors &copy; CARTO',
            subdomains: 'abcd',
            maxZoom: 20
        }).addTo(map);

        // --- Estado Global del Mapa ---
        let routingControl = null;  // Controlador de ruta
        let watchId = null;         // ID del watcher de GPS
        let destinoLatLng = null;   // Coordenadas del destino
        let carMarker = null;       // Marcador del coche del usuario
        let hasAddedOriginSpots = false; // Flag para cargar plazas solo al inicio

        // --- Iconos Personalizados ---

        // Icono de Coche (Usuario)
        const carIcon = L.divIcon({
            className: 'custom-car-icon',
            html: `
            <svg width="40" height="40" viewBox="0 0 512 512" style="filter: drop-shadow(0 0 10px #7c3aed);">
                <path fill="#7c3aed" d="M437 166L382 56H130L75 166c-28 6-49 31-49 61v155c0 18 14 32 32 32h21c6 0 11-4 13-9l15-45h298l15 45c2 5 7 9 13 9h21c18 0 32-14 32-32V227c0-30-21-55-49-61zM152 96h208l33 64H119l33-64zm-32 208c-18 0-32-14-32-32s14-32 32-32 32 14 32 32-14 32-32 32zm272 0c-18 0-32-14-32-32s14-32 32-32 32 14 32 32-14 32-32 32z"/>
            </svg>`,
            iconSize: [40, 40],
            iconAnchor: [20, 20] // Centro del icono
        });

        // Icono de Plaza Libre (Verde)
        const parkingIcon = L.divIcon({
            className: 'parking-icon',
            html: `
            <div style="background: #10b981; width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 2px solid white; box-shadow: 0 0 10px rgba(16, 185, 129, 0.5);">
                <i class="fa-solid fa-square-parking" style="color: white; font-size: 16px;"></i>
            </div>`,
            iconSize: [30, 30],
            iconAnchor: [15, 15]
        });

        /**
         * Genera plazas de aparcamiento aleatorias alrededor de un punto.
         * En producción, esto haría una llamada a Firestore con geo-queries.
         */
        function addRandomParkingSpots(center) {
            for (let i = 0; i < 8; i++) {
                // Generar offset aleatorio pequeño (~100-500m)
                const latOffset = (Math.random() - 0.5) * 0.01;
                const lngOffset = (Math.random() - 0.5) * 0.01;

                L.marker([center.lat + latOffset, center.lng + lngOffset], { icon: parkingIcon })
                    .addTo(map)
                    .bindPopup(`
                        <div style="text-align: center;">
                            <strong style="color: #10b981; font-size: 1.1rem;">🅿️ Plaza Libre</strong><br>
                            <span style="color: #666;">Detectado hace ${Math.floor(Math.random() * 10) + 1} min</span><br>
                            <button style="background: #7c3aed; color: white; border: none; padding: 4px 8px; border-radius: 4px; margin-top: 5px; cursor: pointer;">Ir aquí</button>
                        </div>
                    `);
            }
        }

        // 3. Geolocalización Inicial
        map.locate({ setView: true, maxZoom: 16 });

        // Evento: Usuario encontrado
        map.on('locationfound', (e) => {
            const actual = e.latlng;
            if (!carMarker) {
                carMarker = L.marker(actual, { icon: carIcon }).addTo(map)
                    .bindPopup("<b>Tu ubicación actual</b>").openPopup();

                // Cargar plazas cercanas simuladas
                if (!hasAddedOriginSpots) {
                    addRandomParkingSpots(actual);
                    hasAddedOriginSpots = true;
                }
            }
        });

        map.on('locationerror', (e) => {
            console.warn("No se pudo geolocalizar:", e.message);
            // No mostramos alerta intrusiva, solo log, el usuario puede buscar manualmente
        });

        /**
         * Lógica principal: Busca destino y traza ruta
         */
        async function iniciarNavegacion() {
            const destino = document.getElementById('searchInput').value;
            if (!destino) return alert('Por favor, escribe un destino primero.');

            // Feedback visual en el botón
            const btn = document.querySelector('button[onclick="iniciarNavegacion()"]');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Calculando ruta...';
            btn.disabled = true;

            try {
                // Geocodificación (Texto -> Coordenadas) usando Nominatim
                const url = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(destino)}`;
                const res = await fetch(url);
                const data = await res.json();

                if (!data.length) {
                    throw new Error('Destino no encontrado');
                }

                // Guardar destino
                destinoLatLng = L.latLng(data[0].lat, data[0].lon);

                // Activar seguimiento GPS en tiempo real
                if (watchId) navigator.geolocation.clearWatch(watchId);

                // WatchPosition se ejecutará cada vez que el GPS cambie
                watchId = navigator.geolocation.watchPosition(
                    actualizarPosicion, // Éxito
                    err => {           // Error
                        console.error(err);
                        alert('Error de GPS: ' + err.message);
                        btn.innerHTML = originalText;
                        btn.disabled = false;
                    },
                    { enableHighAccuracy: true, maximumAge: 0, timeout: 10000 }
                );

            } catch (e) {
                console.error(e);
                alert(e.message === 'Destino no encontrado' ? 'No encontramos esa dirección.' : "Error al conectar con el servicio de mapas.");
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        }

        /**
         * Se ejecuta cada vez que el GPS reporta una nueva posición
         */
        function actualizarPosicion(pos) {
            const lat = pos.coords.latitude;
            const lng = pos.coords.longitude;
            const actual = L.latLng(lat, lng);

            // Actualizar marcador del coche
            if (!carMarker) {
                carMarker = L.marker(actual, { icon: carIcon }).addTo(map);
                // Si es la primera vez que tenemos posición, cargamos plazas ambiente
                if (!hasAddedOriginSpots) {
                    addRandomParkingSpots(actual);
                    hasAddedOriginSpots = true;
                }
            } else {
                carMarker.setLatLng(actual);
            }

            // Mover cámara suavemente
            map.setView(actual, 18, { animate: true });

            // Calcular ruta si hay destino
            if (destinoLatLng) {
                if (routingControl) map.removeControl(routingControl);

                routingControl = L.Routing.control({
                    waypoints: [actual, destinoLatLng],
                    show: false, // Usamos nuestro propio panel UI, ocultamos el nativo
                    routeWhileDragging: false,
                    language: 'es', // Idioma de las instrucciones
                    lineOptions: {
                        styles: [{ color: '#7c3aed', weight: 6, opacity: 0.9, shadowBlur: 10, shadowColor: '#7c3aed' }]
                    },
                    createMarker: function () { return null; } // No poner marcadores extraños de A->B de la librería
                }).addTo(map);

                // Cuando la ruta se calcula, mostramos las instrucciones
                routingControl.on('routesfound', e => mostrarIndicaciones(e));
            }
        }

        /**
         * Renderiza las instrucciones paso a paso en el panel lateral
         */
        function mostrarIndicaciones(e) {
            const div = document.getElementById('routeInstructions');
            div.innerHTML = ''; // Limpiar anterior

            const r = e.routes[0]; // Mejor ruta encontrada

            // Resumen (Tiempo y Distancia)
            div.innerHTML += `
                <div style="margin-bottom: 15px; padding: 15px; background: rgba(124, 58, 237, 0.1); border-radius: 12px; border: 1px solid rgba(124, 58, 237, 0.3);">
                    <div style="font-size: 1.4rem; color: #fff; font-weight: bold;">
                        <i class="fa-solid fa-clock"></i> ${(r.summary.totalTime / 60).toFixed(0)} min
                    </div>
                    <div style="color: #ccc; margin-top: 5px;">
                        <i class="fa-solid fa-road"></i> ${(r.summary.totalDistance / 1000).toFixed(1)} km
                    </div>
                </div>`;

            // Lista de pasos
            r.instructions.forEach(i => {
                // Iconos según el tipo de maniobra
                let icon = '<i class="fa-solid fa-arrow-up"></i>';
                if (i.type === 'TurnLeft') icon = '<i class="fa-solid fa-arrow-left"></i>';
                if (i.type === 'TurnRight') icon = '<i class="fa-solid fa-arrow-right"></i>';
                if (i.type === 'Roundabout') icon = '<i class="fa-solid fa-spin fa-rotate-right"></i>';
                if (i.type === 'DestinationReached') icon = '<i class="fa-solid fa-flag-checkered" style="color: var(--accent-main);"></i>';

                div.innerHTML += `
                    <div style="display: flex; gap: 15px; padding: 12px 0; border-bottom: 1px solid rgba(255,255,255,0.05); align-items: center;">
                        <span style="font-size: 1.2rem; width: 30px; text-align: center;">${icon}</span>
                        <span style="font-size: 0.95rem; line-height: 1.4;">${i.text}</span>
                    </div>`;
            });

            // Restaurar botón (ya estamos navegando)
            const btn = document.querySelector('button[onclick="iniciarNavegacion()"]');
            btn.innerHTML = '<i class="fa-solid fa-location-crosshairs"></i> Recalcular Ruta';
            btn.disabled = false;
        }
    </script>
</body>
</html>
