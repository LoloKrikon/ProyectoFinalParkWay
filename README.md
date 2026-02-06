# ParkWay 🚗💨

**ParkWay** es aplicaciones web colaborativa diseñada para facilitar la búsqueda de aparcamiento en zonas urbanas. Conecta a usuarios que dejan su plaza libre con aquellos que la están buscando, todo en tiempo real.

![ParkWay Banner](https://via.placeholder.com/1200x400?text=ParkWay+App+Preview)

## 🌐 Demo en Vivo
Puedes probar la aplicación desplegada aquí:
👉 **[https://parkway-c842c.web.app](https://parkway-c842c.web.app)**

---

## ✨ Características Principales

### 🗺️ Mapa Interactivo
- **Visualización en tiempo real** de plazas libres (marcadores verdes pulsantes).
- **Estilo Dark Mode** (CartoDB Dark Matter) para una visualización cómoda de noche.
- **Geolocalización** del usuario para mostrar su posición exacta.

### 📍 Navegación y Rutas
- Cálculo de rutas desde tu posición hasta la plaza libre elegida.
- Indicaciones paso a paso (giros, distancias) integradas en el panel lateral.
- Modo "Moverme" para simular conducción (útil para pruebas sin GPS real).

### 🤝 Colaboración (Crowdsourcing)
- **Liberar Plaza**: Los usuarios pueden marcar su ubicación actual como "libre" cuando se van.
- **Base de Datos en Vivo**: Las plazas aparecen instantáneamente en los mapas de otros usuarios gracias a Firebase Firestore.

### 👤 Perfil de Usuario
- Registro e inicio de sesión seguro con **Google** o Email.
- Gestión de perfil: foto, nombre, vehículo y teléfono.
- **Historial de búsquedas**: Guarda tus destinos frecuentes automáticamente.

### 📱 Diseño Responsive
- Interfaz adaptada tanto para **Escritorio** como para **Móviles**.
- En versión móvil, los controles se reorganizan para un uso fácil con una mano.

---

## 🛠️ Tecnologías Utilizadas

- **Frontend**: HTML5, CSS3 (Variables, Flexbox, Grid), JavaScript (ES6 Modules).
- **Mapas**: [Leaflet.js](https://leafletjs.com/) + [Leaflet Routing Machine](https://www.liedman.net/leaflet-routing-machine/).
- **Tiles**: CartoDB Dark Matter (OpenStreetMap).
- **Backend (Serverless)**: 
  - **Firebase Authentication**: Gestión de usuarios.
  - **Firebase Firestore**: Base de datos NoSQL en tiempo real.
  - **Firebase Hosting**: Alojamiento web estático y rápido.

---

## 🚀 Instalación y Despliegue

### Requisitos Previos
- Tener instalado [Node.js](https://nodejs.org/).
- Tener una cuenta de Google/Firebase.

### 1. Clonar y Configurar
```bash
git clone https://github.com/tu-usuario/proyecto-parkway.git
cd ProyectoFinalParkWay
```

### 2. Configuración de Firebase
El proyecto ya incluye la configuración básica, pero si lo despliegas en tu propia cuenta:
1. Instala las tools de Firebase:
   ```bash
   npm install -g firebase-tools
   ```
2. Inicia sesión:
   ```bash
   firebase login
   ```
3. Inicializa el proyecto (si es nuevo):
   ```bash
   firebase init
   ```
   *Selecciona: Hosting y Firestore.*

### 3. Ejecutar Localmente
Para ver la web en tu ordenador antes de subirla:
```bash
firebase serve
```
La web estará disponible en `http://localhost:5000`.

### 4. Desplegar a Producción
Para subir los cambios a internet:
```bash
firebase deploy
```

---

## 📂 Estructura del Proyecto

```
ProyectoFinalParkWay/
├── Public/                 # Carpeta raíz del servidor web
│   ├── css/                # Estilos (styles.css, map-styles.css)
│   ├── js/                 # Lógica (buscar-map.js, firebase-config.js...)
│   ├── includes/           # (Legacy) Archivos PHP antiguos
│   ├── index.html          # Redirección inicial
│   ├── buscar.html         # Vista principal del mapa
│   └── ... (otras vistas HTML)
├── firebase.json           # Configuración de despliegue
└── MARKET_RESEARCH.md      # Análisis inicial de competidores y negocio
```

---

## 📄 Licencia
Este proyecto es un prototipo educativo. El uso de los mapas está sujeto a las licencias de OpenStreetMap y CartoDB.
