/**
 * Configuración central de Firebase para ParkWay.
 * Aquí inicializamos la conexión y exportamos los servicios necesarios (Auth y Firestore).
 */
import { initializeApp } from "https://www.gstatic.com/firebasejs/10.8.0/firebase-app.js";
import { getAuth, GoogleAuthProvider } from "https://www.gstatic.com/firebasejs/10.8.0/firebase-auth.js";
import { getFirestore } from "https://www.gstatic.com/firebasejs/10.8.0/firebase-firestore.js";

// Credenciales del proyecto
const firebaseConfig = {
    apiKey: "AIzaSyB2HR19k8a8vsake0JJGRFGLDy3bnIqwKE",
    authDomain: "parkway-c842c.firebaseapp.com",
    projectId: "parkway-c842c",
    storageBucket: "parkway-c842c.firebasestorage.app",
    messagingSenderId: "491754873419",
    appId: "1:491754873419:web:790ec7f43c76a25e5323ce",
    measurementId: "G-X2SMP5VLPC"
};

// Inicialización
const app = initializeApp(firebaseConfig);

// Exportamos las instancias listas para usar en la app
export const auth = getAuth(app);
export const googleProvider = new GoogleAuthProvider();
export const db = getFirestore(app);