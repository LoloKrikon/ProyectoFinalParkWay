<!DOCTYPE html>
<html lang="es">

<head>
    <!-- Configuración para la página de búsqueda con mapa -->
    <?php 
        $pageTitle = 'ParkWay - Buscar Aparcamiento';
        
        // CSS extra para Leaflet y estilos específicos del nuevo diseño
        // CSS extra para Leaflet y estilos específicos del nuevo diseño
        $extraCss = <<<EOT
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.css" />
<style>
    /* Local Override */
    .search-input-alt {
        background: rgba(0, 0, 0, 0.3);
        border: 1px solid rgba(255, 255, 255, 0.1);
        padding: 12px;
        border-radius: 8px;
        color: white;
        width: 100%;
        outline: none;
    }

    .search-input-alt:focus {
        border-color: var(--accent-main);
    }

    /* Hide Leaflet Default Control Container if needed or style it */
    .leaflet-control-container .leaflet-routing-container-hide {
        display: none;
    }

    /* Crosshair (Cruceta) */
    .center-crosshair {
        position: absolute;
        top: 50%;
        left: 50%;
        width: 20px;
        height: 20px;
        transform: translate(-50%, -50%);
        pointer-events: none;
        z-index: 1000;
        display: none; /* Hidden by default */
    }
    .center-crosshair::before, .center-crosshair::after {
        content: '';
        position: absolute;
        background: rgba(255, 255, 255, 0.8);
        box-shadow: 0 0 4px rgba(0,0,0,0.5);
    }
    .center-crosshair::before {
        top: 9px; left: 0; width: 20px; height: 2px; /* Horizontal */
    }
    .center-crosshair::after {
        top: 0; left: 9px; width: 2px; height: 20px; /* Vertical */
    }

    /* Floating Controls */
    .floating-controls {
        position: absolute;
        bottom: 30px;
        left: 50%;
        transform: translateX(-50%);
        display: flex;
        gap: 15px;
        z-index: 1000;
    }
    .float-btn {
        background: rgba(30, 30, 30, 0.9);
        color: white;
        border: 1px solid rgba(255, 255, 255, 0.2);
        padding: 10px 20px;
        border-radius: 50px;
        font-size: 0.9rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.5);
        transition: all 0.3s;
    }
    .float-btn:hover {
        background: var(--accent-main);
        transform: translateY(-2px);
    }
    .float-btn.active {
        background: var(--accent-main);
        border-color: var(--accent-main);
    }
</style>
EOT;
    ?>
    <!-- Inclusión del head común -->
    <?php include 'includes/head.php'; ?>
</head>

<body>
    <section class="view">
        <!-- 
            Barra de navegación común.
            Se adapta el header del nuevo diseño usando el componente navbar.php.
        -->
        <?php 
            $showProfile = true; 
            $backLink = 'plataformas.php';
            include 'includes/navbar.php'; 
        ?>

        <div class="map-layout">
            <!-- Panel Lateral -->
            <div class="map-panel">
                <h2>Buscar Plaza</h2>
                <div class="map-input-group">
                    <input id="searchInput" class="search-input-alt" placeholder="¿Dónde quieres ir?" />
                    <button onclick="iniciarNavegacion()" class="primary-btn" style="width: 100%; padding: 10px;">
                        <i class="fa-solid fa-location-arrow"></i> Navegar
                    </button>
                </div>

                <!-- Historial de búsquedas recientes -->
                <div id="searchHistory" style="margin-top: 20px;">
                    <h3 style="font-size: 0.9rem; opacity: 0.6; margin-bottom: 10px;">Búsquedas recientes</h3>
                    <div id="historyList"></div>
                </div>

                <!-- Indicaciones de ruta (oculto por defecto) -->
                <div id="routeInstructions" style="display: none;">
                    <h3 style="font-size: 0.9rem; opacity: 0.6; margin-bottom: 10px;">Indicaciones</h3>
                    <div id="instructionsList"></div>
                </div>
            </div>

            <!-- Mapa -->
            <div class="map-container" style="position: relative;">
                <div id="map"></div>
                
                <!-- Crosshair Overlay -->
                <div id="crosshair" class="center-crosshair"></div>

                <!-- Floating Controls -->
                <div class="floating-controls">
                    <button class="float-btn" onclick="localizarUsuario()">
                        <i class="fa-solid fa-crosshairs"></i> Localizar
                    </button>
                    <button id="btn-moverme" class="float-btn" onclick="toggleMoverme()">
                        <i class="fa-solid fa-up-down-left-right"></i> Moverme
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Leaflet Scripts -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.min.js"></script>
    
    <!-- Lógica del Mapa -->
    <script type="module">
        const map = L.map('map').setView([40.4168, -3.7038], 15);

        // CartoDB Dark Matter for Dark Theme compatibility
        L.tileLayer('https://tiles.stadiamaps.com/tiles/alidade_smooth_dark/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; <a href="https://stadiamaps.com/">Stadia Maps</a>, &copy; <a href="https://openmaptiles.org/">OpenMapTiles</a> &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors',
            maxZoom: 20
        }).addTo(map);

        let routingControl = null;
        let watchId = null;
        let destinoLatLng = null;
        let carMarker = null;
        let hasAddedOriginSpots = false;

        // Gestión de historial de búsquedas
        function cargarHistorial() {
            const historial = JSON.parse(localStorage.getItem('parkway_history') || '[]');
            const historyList = document.getElementById('historyList');
            
            if (historial.length === 0) {
                historyList.innerHTML = '<p style="text-align: center; opacity: 0.5; font-size: 0.9rem;">No hay búsquedas recientes</p>';
                return;
            }
            
            historyList.innerHTML = '';
            historial.slice(0, 5).forEach(lugar => {
                const item = document.createElement('div');
                item.style.cssText = 'padding: 10px; background: rgba(124, 58, 237, 0.1); border-radius: 8px; margin-bottom: 8px; cursor: pointer; display: flex; align-items: center; gap: 10px; transition: all 0.3s;';
                item.innerHTML = `
                    <i class="fa-solid fa-clock-rotate-left" style="color: #7c3aed;"></i>
                    <span style="flex: 1;">${lugar}</span>
                `;
                item.addEventListener('mouseenter', () => item.style.background = 'rgba(124, 58, 237, 0.2)');
                item.addEventListener('mouseleave', () => item.style.background = 'rgba(124, 58, 237, 0.1)');
                item.addEventListener('click', () => {
                    document.getElementById('searchInput').value = lugar;
                    iniciarNavegacion();
                });
                historyList.appendChild(item);
            });
        }

        function guardarEnHistorial(lugar) {
            let historial = JSON.parse(localStorage.getItem('parkway_history') || '[]');
            // Eliminar duplicados
            historial = historial.filter(h => h !== lugar);
            // Añadir al principio
            historial.unshift(lugar);
            // Mantener solo los últimos 5
            historial = historial.slice(0, 5);
            localStorage.setItem('parkway_history', JSON.stringify(historial));
            
            // Actualizar visualmente la lista
            cargarHistorial();
        }

        // Cargar historial al inicio
        cargarHistorial();
        
        // Detectar cuando se borra el input para mostrar historial
        document.getElementById('searchInput').addEventListener('input', function() {
            if (this.value.trim() === '') {
                document.getElementById('searchHistory').style.display = 'block';
                document.getElementById('routeInstructions').style.display = 'none';
                
                // Opcional: detener la navegación actual
                if (watchId) {
                    navigator.geolocation.clearWatch(watchId);
                    watchId = null;
                }
                if (routingControl) {
                    map.removeControl(routingControl);
                    routingControl = null;
                }
            }
        });

        // Custom Car Icon
        const carIcon = L.divIcon({
            className: 'custom-car-icon',
            html: `
            <svg width="40" height="40" viewBox="0 0 512 512" style="filter: drop-shadow(0 0 10px #7c3aed);">
                <path fill="#7c3aed" d="M437 166L382 56H130L75 166c-28 6-49 31-49 61v155c0 18 14 32 32 32h21c6 0 11-4 13-9l15-45h298l15 45c2 5 7 9 13 9h21c18 0 32-14 32-32V227c0-30-21-55-49-61zM152 96h208l33 64H119l33-64zm-32 208c-18 0-32-14-32-32s14-32 32-32 32 14 32 32-14 32-32 32zm272 0c-18 0-32-14-32-32s14-32 32-32 32 14 32 32-14 32-32 32z"/>
            </svg>`,
            iconSize: [40, 40],
            iconAnchor: [20, 20]
        });

        // Parking Spot Icon
        const parkingIcon = L.divIcon({
            className: 'parking-icon',
            html: `
            <div style="background: #10b981; width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 2px solid white; box-shadow: 0 0 10px rgba(16, 185, 129, 0.5);">
                <i class="fa-solid fa-square-parking" style="color: white; font-size: 16px;"></i>
            </div>`,
            iconSize: [30, 30],
            iconAnchor: [15, 15]
        });

        // -------------------------------------------------------------
        //  LÓGICA REAL: ESCUCHAR PLAZAS DE LA COMUNIDAD (Firebase)
        // -------------------------------------------------------------
        import { db } from './js/firebase-config.js';
        import { collection, onSnapshot, query, where, orderBy, limit } from "https://www.gstatic.com/firebasejs/10.8.0/firebase-firestore.js";

        const markers = {}; // Almacén de marcadores activos para poder borrarlos si se ocupan

        function escucharPlazasLibres() {
            // Escuchar cambios en tiempo real en la colección 'plazas_libres'
            // Filtramos solo las que estén en estado 'free'
            const q = query(
                collection(db, "plazas_libres"), 
                where("status", "==", "free"),
                limit(50) 
            );

            onSnapshot(q, (snapshot) => {
                snapshot.docChanges().forEach((change) => {
                    const data = change.doc.data();
                    const docId = change.doc.id;
                    const { location, timestamp, userName } = data;

                    if (change.type === "added") {
                        // Añadir marcador al mapa
                        if (location) {
                            // Calcular hace cuánto se liberó
                            let timeAgo = "Recientemente";
                            if (timestamp) {
                                const diff = new Date() - timestamp.toDate();
                                const min = Math.floor(diff / 60000);
                                timeAgo = min < 1 ? "Ahora mismo" : `Hace ${min} min`;
                            }

                            const marker = L.marker([location.latitude, location.longitude], { icon: parkingIcon })
                                .addTo(map)
                                .bindPopup(`
                                    <div style="text-align: center;">
                                        <b>🅿️ Plaza Libre</b><br>
                                        <span style="font-size: 0.85rem; color: #aaa;">Por: ${userName || 'Anónimo'}</span><br>
                                        <span style="color: #10b981; font-weight: bold;">${timeAgo}</span><br>
                                        <button onclick="navegarA('${location.latitude}', '${location.longitude}')" 
                                            style="margin-top: 8px; background: #7c3aed; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer;">
                                            <i class="fa-solid fa-location-arrow"></i> Ir aquí
                                        </button>
                                    </div>
                                `);
                            
                            markers[docId] = marker;
                        }
                    }
                    if (change.type === "modified") {
                        // Si cambia de estado (ej. se ocupa), podríamos actualizar el icono o borrarlo
                        // Por ahora, si deja de ser 'free', el query filter lo trataría como removed en muchos casos, 
                        // pero si solo cambia otro dato, actualizamos el popup.
                    }
                    if (change.type === "removed") {
                        // Si se elimina de la base de datos o cambia de estado a 'ocupado'
                        if (markers[docId]) {
                            map.removeLayer(markers[docId]);
                            delete markers[docId];
                        }
                    }
                });
            }, (error) => {
                console.error("Error escuchando plazas:", error);
            });
        }

        // Exponer función global para el botón del popup (fuera del módulo)
        window.navegarA = function(lat, lng) {
            destinoLatLng = L.latLng(lat, lng);
            document.getElementById('searchInput').value = `Plaza detectada`;
            iniciarNavegacion(true); // true = modo directo
        };

        // map.locate ya no es necesario como principal fuente, usamos watchPosition abajo.
        // Pero mantenemos un listener d error simple por si acaso.
        map.on('locationerror', (e) => {
            console.log("Error de localización Leaflet:", e.message);
            escucharPlazasLibres(); // Cargar plazas de todas formas
        });
        
        // Iniciamos escucha de plazas inmediatamente
        escucharPlazasLibres();

        // Función principal de búsqueda y navegación
        window.iniciarNavegacion = async function(isDirectMode = false) {
            const destino = document.getElementById('searchInput').value;
            if (!destino && !isDirectMode) return alert('Escribe un destino');

            const btn = document.querySelector('button[onclick="iniciarNavegacion()"]');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Buscando...';

            try {
                if (!isDirectMode) {
                    // Solo buscar en API si NO estamos en modo directo (click en plaza)
                    const url = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(destino)}`;
                    const res = await fetch(url);
                    const data = await res.json();

                    if (!data.length) {
                        alert('Destino no encontrado');
                        btn.innerHTML = originalText;
                        return;
                    }
                    destinoLatLng = L.latLng(data[0].lat, data[0].lon);
                }
                
                // Si esDirectMode, destinoLatLng ya fue seteado por navegarA() antes de llamar a esta funcion

                // Guardar en historial y cambiar vista

                // Guardar en historial y cambiar vista
                guardarEnHistorial(destino);
                document.getElementById('searchHistory').style.display = 'none';
                document.getElementById('routeInstructions').style.display = 'block';

                // Forzar actualización de ruta con la posición actual si existe
                if (lastKnownPos) actualizarPosicion({ coords: { latitude: lastKnownPos.lat, longitude: lastKnownPos.lng } });
                
                // Ya tenemos un watchId global, no necesitamos otro aquí que sobreescriba
                // Solo nos aseguramos de que el panel de instrucciones se actualice
            } catch (e) {
                console.error(e);
                alert("Error al conectar con el servicio de mapas.");
                btn.innerHTML = originalText;
            }
        }

        let isManualMode = true; 
        let isFreeRoam = false;  // Modo 'Conducción Virtual' (Cruz visible + Coche sigue centro)
        let isVirtualLocation = false; // Nuevo: Si estamos en una ubicación virtual fija (Coche quieto donde lo dejamos)
        let lastKnownPos = null;

        // Seguimiento GPS continuo
        function actualizarPosicion(pos) {
            const lat = pos.coords.latitude;
            const lng = pos.coords.longitude;
            const actual = L.latLng(lat, lng);
            
            // Siempre guardamos la última posición real GPS
            lastKnownPos = actual;

            // Si estamos en modo moverme O en modo virtual fijo, NO tocamos el coche con el GPS
            if (isFreeRoam || isVirtualLocation) return;

            if (!carMarker) {
                carMarker = L.marker(actual, { icon: carIcon }).addTo(map);
                
                if (!hasAddedOriginSpots) {
                    map.setView(actual, 16);
                    addRandomParkingSpots(actual, true); 
                    hasAddedOriginSpots = true;
                }
            } else {
                carMarker.setLatLng(actual);
            }

            actualizarRuta(actual); 
        }

        function actualizarRuta(origen) {
            if (destinoLatLng) {
                if (routingControl) map.removeControl(routingControl);

                routingControl = L.Routing.control({
                    waypoints: [origen, destinoLatLng],
                    show: false,
                    routeWhileDragging: false,
                    language: 'es',
                    lineOptions: {
                        styles: [{ color: '#7c3aed', weight: 6, opacity: 0.9, shadowBlur: 10, shadowColor: '#7c3aed' }]
                    },
                    createMarker: function () { return null; }
                }).addTo(map);

                routingControl.on('routesfound', e => mostrarIndicaciones(e));
            }
        }

        // --- Evento: Mover el "Coche Fantasma" con el mapa ---
        map.on('move', () => {
             // Solo mover si estamos EDITANDO la posición (Cruz visible)
             if (isFreeRoam && carMarker) {
                 const center = map.getCenter();
                 carMarker.setLatLng(center);
             }
        });

        // --- Funciones de Control Manual ---

        if (navigator.geolocation) {
            watchId = navigator.geolocation.watchPosition(actualizarPosicion, 
                (err) => console.warn("Error GPS:", err),
                { enableHighAccuracy: true, maximumAge: 0, timeout: 10000 }
            );
        } else {
            alert("Tu navegador no soporta geolocalización.");
        }

        window.localizarUsuario = function() {
            // AL PULSAR LOCALIZAR:
            // 1. Quitamos modo FreeRoam si estuviera (Cruz fuera)
            if (isFreeRoam) window.toggleMoverme();
            
            // 2. Quitamos modo Virtual Fijo (volvemos a GPS real)
            isVirtualLocation = false;

            // 3. Forzamos la vuelta al GPS
            if (lastKnownPos) {
                map.flyTo(lastKnownPos, 18, { animate: true, duration: 0.8 });
                if (carMarker) {
                    carMarker.setLatLng(lastKnownPos);
                    carMarker.setOpacity(1);
                    actualizarRuta(lastKnownPos);
                }
            } else {
                alert("Buscando tu ubicación...");
                map.locate({ setView: true, maxZoom: 18 });
            }
        };

        window.toggleMoverme = function() {
            const crosshair = document.getElementById('crosshair');
            const btn = document.getElementById('btn-moverme');
            
            if (isFreeRoam) {
                // --- AL PULSAR "LISTO" (Desactivar cruceta) ---
                isFreeRoam = false; 

                // UI
                crosshair.style.display = 'none';
                btn.classList.remove('active');
                btn.innerHTML = '<i class="fa-solid fa-up-down-left-right"></i> Moverme';
                
                // LÓGICA CRÍTICA:
                // NO volvemos al GPS. Nos quedamos en "Modo Virtual Fijo".
                isVirtualLocation = true;
                
                // El coche SE QUEDA donde estaba la cruz (centro del mapa en ese momento)
                // No llamamos a panTo ni setLatLng a lastKnownPos.
                
                // Opcional: Recalcular ruta desde esta nueva ubicación virtual
                if (carMarker) actualizarRuta(carMarker.getLatLng());

            } else {
                // --- AL PULSAR "MOVERME" (Activar cruceta) ---
                isFreeRoam = true;
                // isVirtualLocation da igual aquí, porque isFreeRoam tiene prioridad en move handler

                // UI
                crosshair.style.display = 'block';
                btn.classList.add('active');
                btn.innerHTML = '<i class="fa-solid fa-check"></i> Listo';
                
                // Traemos el coche al centro para empezar a moverlo
                if (carMarker) {
                    carMarker.setLatLng(map.getCenter());
                }
            }
        };



        // Renderizado de instrucciones de ruta
        function mostrarIndicaciones(e) {
            const div = document.getElementById('instructionsList');
            div.innerHTML = '';

            const r = e.routes[0];
            div.innerHTML += `<div style="margin-bottom: 10px; padding: 10px; background: rgba(124, 58, 237, 0.1); border-radius: 8px;">
                                <div style="font-size: 1.2rem; color: #fff;">${(r.summary.totalTime / 60).toFixed(0)} min</div>
                                <div style="color: #ccc;">${(r.summary.totalDistance / 1000).toFixed(1)} km</div>
                              </div>`;

            r.instructions.forEach(i => {
                let icon = '➡️';
                if (i.type === 'TurnLeft') icon = '⬅️';
                if (i.type === 'TurnRight') icon = '➡️';
                if (i.type === 'Roundabout') icon = '🔄';

                div.innerHTML += `<div style="display: flex; gap: 10px; padding: 8px 0; border-bottom: 1px solid rgba(255,255,255,0.05);">
                                    <span>${icon}</span>
                                    <span>${i.text}</span>
                                  </div>`;
            });

            // Restore btn
            const btn = document.querySelector('button[onclick="iniciarNavegacion()"]');
            btn.innerHTML = '<i class="fa-solid fa-location-arrow"></i> Actualizando...';
        }
    </script>
</body>
</html>
