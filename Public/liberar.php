<!DOCTYPE html>
<html lang="es">

<head>
    <!-- Configuración para la página de liberar plaza -->
    <?php 
        $pageTitle = 'ParkWay - Dejar Sitio Libre';
        
        // Leaflet CSS (Mapas)
        $extraCss = '<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
                     <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.css" />';
    ?>
    <?php include 'includes/head.php'; ?>

    <style>
        /* Ajuste de margen para el feedbak */
        .feedback-message {
            margin-top: 15px;
            padding: 10px;
            border-radius: 8px;
            font-size: 0.95rem;
            text-align: center;
            opacity: 0;
            transform: translateY(10px);
            transition: all 0.5s ease;
        }

        .feedback-message.visible {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
</head>

<body>
    <section class="view">
        <!-- Barra de navegación común -->
        <?php 
            $showProfile = true; 
            $backLink = 'plataformas.php'; // Volver al menú principal
            include 'includes/navbar.php'; 
        ?>

        <div class="map-layout">
            <!-- PANEL LATERAL: Confirmación de acción -->
            <div class="map-panel">
                <h2>🅿️ Liberar Plaza</h2>
                
                <div class="map-input-group">
                    <p style="color: var(--text-secondary); font-size: 0.95rem; margin-bottom: 20px; line-height: 1.5;">
                        ¿Vas a salir? Confirma tu ubicación para avisar a otros conductores y ayudar a la comunidad ParkWay.
                    </p>
                    
                    <!-- Botón de Acción Principal -->
                    <button onclick="liberarPlaza()" class="primary-btn"
                        style="width: 100%; padding: 14px; background: linear-gradient(135deg, #10b981, #059669); font-weight: bold; font-size: 1.1rem; box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4);">
                        <i class="fa-solid fa-check-circle"></i> Liberar mi sitio aquí
                    </button>
                    
                    <!-- Área de mensajes de estado (éxito/error) -->
                    <div id="statusMessage" class="feedback-message"></div>
                </div>

                <!-- Info Box -->
                <div style="margin-top: 30px; padding: 15px; background: rgba(255,255,255,0.05); border-radius: 12px; font-size: 0.85rem; color: #aaa;">
                    <i class="fa-solid fa-circle-info"></i> 
                    Al liberar tu plaza ganas puntos de comunidad. ¡Gracias por colaborar!
                </div>
            </div>

            <!-- CONTENEDOR DEL MAPA -->
            <div class="map-container">
                <div id="map"></div>
            </div>
        </div>
    </section>

    <!-- SCRIPTS LEAFLET -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.min.js"></script>

    <!-- LÓGICA JAVASCRIPT -->
    <script>
        // 1. Inicialización del mapa
        const map = L.map('map').setView([40.4168, -3.7038], 15);

        // Capa oscura (Dark Mode)
        L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; OpenStreetMap &copy; CARTO',
            subdomains: 'abcd',
            maxZoom: 20
        }).addTo(map);

        let myMarker = null;

        // 2. Icono Personalizado: Mi Ubicación (Punto verde pulsante)
        const myIcon = L.divIcon({
            className: 'custom-car-icon',
            html: `
            <svg width="60" height="60" viewBox="0 0 24 24" style="filter: drop-shadow(0 0 15px #10b981);">
                <circle cx="12" cy="12" r="10" fill="#10b981" fill-opacity="0.3">
                    <animate attributeName="r" values="8;11;8" dur="1.5s" repeatCount="indefinite" />
                    <animate attributeName="fill-opacity" values="0.3;0.1;0.3" dur="1.5s" repeatCount="indefinite" />
                </circle>
                <circle cx="12" cy="12" r="5" fill="#10b981"/>
                <circle cx="12" cy="12" r="2" fill="white"/>
            </svg>`,
            iconSize: [60, 60],
            iconAnchor: [30, 30]
        });

        // 3. Geolocalización automática al entrar
        map.locate({ setView: true, maxZoom: 18 });

        // Callback de éxito: Centrar en usuario
        function onLocationFound(e) {
            if (myMarker) map.removeLayer(myMarker);
            myMarker = L.marker(e.latlng, { icon: myIcon }).addTo(map)
                .bindPopup("<b>Estás aquí</b>").openPopup();
        }

        // Callback de error
        function onLocationError(e) {
            console.warn("Error GPS:", e.message);
            // No bloqueamos la UI, pero el botón avisará si se pulsa sin GPS
        }

        map.on('locationfound', onLocationFound);
        map.on('locationerror', onLocationError);

        /**
         * Simula la acción de liberar una plaza.
         * Envía feedback visual al usuario tras "conectar" con el servidor (simulado).
         */
        function liberarPlaza() {
            if (!myMarker) {
                alert("Necesitamos tu ubicación GPS para liberar la plaza.");
                return;
            }
            
            const btn = document.querySelector('button[onclick="liberarPlaza()"]');
            const statusDiv = document.getElementById('statusMessage');
            
            // Estado Cargando
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Procesando...';
            btn.disabled = true;

            // Simulación de petición al servidor (1.5 segundos)
            setTimeout(() => {
                // Estado: Completado
                btn.innerHTML = '<i class="fa-solid fa-check"></i> ¡Sitio Liberado!';
                
                // Mostrar mensaje de éxito
                statusDiv.innerHTML = `
                    <div style="background: rgba(16, 185, 129, 0.2); color: #10b981; padding: 10px; border-radius: 8px;">
                        <strong>¡Gracias!</strong><br>Tu plaza ahora es visible para otros usuarios.
                    </div>
                `;
                statusDiv.classList.add('visible');
                
                // Aquí iría la lógica real:
                // await addDoc(collection(db, "plazas"), { ...coords, timestamp: serverTimestamp() });

                // Resetear botón después de 3 segundos
                setTimeout(() => {
                    btn.innerHTML = '<i class="fa-solid fa-check-circle"></i> Liberar mi sitio aquí';
                    btn.disabled = false;
                }, 4000);

            }, 1500);
        }
    </script>
</body>
</html>
