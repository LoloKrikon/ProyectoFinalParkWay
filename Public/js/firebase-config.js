// firebase-config.js
import { initializeApp } from "https://www.gstatic.com/firebasejs/10.8.0/firebase-app.js";
import { getAuth, GoogleAuthProvider } from "https://www.gstatic.com/firebasejs/10.8.0/firebase-auth.js";
import { getFirestore } from "https://www.gstatic.com/firebasejs/10.8.0/firebase-firestore.js";

// Tu configuración de Firebase (copiada de tu app.js original)
const firebaseConfig = {
    apiKey: "AIzaSyCEa7pxlPILJDTPF2qrKFoFUL5edOmeqW0",
    authDomain: "filmseen-b7f04.firebaseapp.com",
    projectId: "filmseen-b7f04",
    storageBucket: "filmseen-b7f04.firebasestorage.app",
    messagingSenderId: "455110464640",
    appId: "1:455110464640:web:4cf237d30d71b6cbd88265",
    measurementId: "G-FF9RZYWVLQ"
};

// Inicializar la aplicación
const app = initializeApp(firebaseConfig);

// Exportar las herramientas de autenticación para usarlas en otros archivos
export const auth = getAuth(app);
export const googleProvider = new GoogleAuthProvider();
export const db = getFirestore(app);