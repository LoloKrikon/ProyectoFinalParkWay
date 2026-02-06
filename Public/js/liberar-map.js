import { db, auth } from './firebase-config.js';
import { collection, addDoc, serverTimestamp, GeoPoint } from "https://www.gstatic.com/firebasejs/10.8.0/firebase-firestore.js";

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

// -------------------------------------------------------------
//  LÓGICA: GESTIÓN DE PLAZAS (Liberar y Ocupar)
// -------------------------------------------------------------

/**
 * Acción 1: LIBERAR PLAZA (Dejar libre para otros)
 */
window.liberarPlaza = async function () {
    if (!myMarker) {
        alert("Necesitamos tu ubicación GPS para liberar la plaza.");
        return;
    }

    const btn = document.getElementById('btn-liberar');
    const statusDiv = document.getElementById('statusMessage');
    const user = auth.currentUser;

    if (!user) {
        alert("Debes iniciar sesión para colaborar.");
        return;
    }

    // UI: Cargando
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Guardando ubicación...';
    btn.disabled = true;

    try {
        // Guardar en Firestore
        const coords = myMarker.getLatLng();
        await addDoc(collection(db, "plazas_libres"), {
            location: new GeoPoint(coords.lat, coords.lng),
            timestamp: serverTimestamp(),
            userId: user.uid,
            userName: user.displayName || "Usuario Anónimo",
            status: 'free', // Estado: disponible
            type: 'user_report'
        });

        // UI: Éxito
        btn.innerHTML = '<i class="fa-solid fa-check"></i> ¡Sitio Liberado!';
        statusDiv.innerHTML = `
            <div style="background: rgba(16, 185, 129, 0.2); color: #10b981; padding: 10px; border-radius: 8px;">
                <strong>¡Gracias!</strong><br>Tu plaza es visible en el mapa en tiempo real.
            </div>
        `;
        statusDiv.classList.add('visible');

        // Reset
        setTimeout(() => {
            btn.innerHTML = '<i class="fa-solid fa-share-from-square"></i> Liberar mi sitio aquí';
            btn.disabled = false;
            statusDiv.classList.remove('visible');
        }, 5000);

    } catch (error) {
        console.error("Error al liberar plaza:", error);
        alert("Error al guardar: " + error.message);
        btn.innerHTML = '<i class="fa-solid fa-triangle-exclamation"></i> Reintentar';
        btn.disabled = false;
    }
};
