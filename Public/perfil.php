<!DOCTYPE html>
<html lang="es">

<head>
    <!-- 
        Configuración de la página y título.
        Se define $pageTitle antes de incluir head.php para personalizar el título de la pestaña.
    -->
    <?php $pageTitle = 'ParkWay - Mi Perfil'; ?>
    
    <!-- Inclusión del encabezado común (Meta tags, CSS, Fuentes) -->
    <?php include 'includes/head.php'; ?>

    <style>
        /* ESTILOS DEL PERFIL */
        
        .profile-section {
            background: rgba(255, 255, 255, 0.05); /* Fondo semitransparente estilo tarjeta */
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 24px;
            border: 1px solid rgba(255, 255, 255, 0.1); /* Borde sutil */
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .profile-section h3 {
            margin-bottom: 20px;
            color: var(--accent-main); /* Color de acento de la marca */
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding-bottom: 8px;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* Contenedores de inputs */
        .input-group {
            margin-bottom: 18px;
        }

        .input-group label {
            display: block;
            margin-bottom: 8px;
            color: rgba(255, 255, 255, 0.7); /* Texto grisáceo para etiquetas */
            font-size: 0.9rem;
            font-weight: 500;
        }

        /* Inputs de texto modernizados */
        .input-group input {
            width: 100%;
            padding: 14px;
            border-radius: 10px;
            background: rgba(0, 0, 0, 0.3); /* Fondo oscuro */
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
            font-family: 'Outfit', sans-serif;
            transition: all 0.3s ease;
        }

        /* Estilo para inputs cuando están desactivados (Modo Lectura) */
        .input-group input:disabled {
            background: rgba(255, 255, 255, 0.02);
            border-color: transparent;
            color: rgba(255, 255, 255, 0.6);
            cursor: default; /* Cursor normal, no 'not-allowed' que es agresivo */
        }

        /* Efecto Focus (Modo Edición) */
        .input-group input:focus {
            outline: none;
            border-color: var(--accent-main);
            background: rgba(0, 0, 0, 0.5);
            box-shadow: 0 0 0 2px rgba(124, 58, 237, 0.2);
        }

        /* Barra de botones inferior */
        .action-bar {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }

        .hidden {
            display: none !important;
        }
    </style>
</head>

<body>
    <section class="view">
        <!-- 
            Inclusión de la Barra de Navegación.
            Configuramos variables para mostrar el botón de "Volver" hacia plataformas.php.
        -->
        <?php 
            $backLink = 'plataformas.php';
            $backText = 'Volver';
            $showProfile = false; // Ya estamos en el perfil, no mostramos el icono duplicado
            include 'includes/navbar.php'; 
        ?>

        <div class="landing-content" style="padding-top: 2rem; max-width: 600px; margin: 0 auto; width: 100%;">
            <!-- Cabecera de la sección -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                <h2 style="font-size: 2.2rem; margin: 0; font-weight: 700;">Mi Perfil</h2>
                <!-- Indicador de carga -->
                <div id="loading" style="color: var(--accent-main); display: none; font-weight: 500;">
                    <i class="fa-solid fa-spinner fa-spin"></i> Cargando datos...
                </div>
            </div>

            <form id="profileForm">
                
                <!--SECCIÓN: DATOS PERSONALES -->
                <div class="profile-section">
                    <h3><i class="fa-solid fa-user"></i> Datos Personales</h3>
                    
                    <div class="input-group">
                        <label>Correo Electrónico</label>
                        <!-- El email es el ID único, no permitimos editarlo fácilmente -->
                        <input type="email" id="email" disabled title="El correo no se puede cambiar">
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div class="input-group">
                            <label>Nombre</label>
                            <input type="text" id="nombre" disabled placeholder="Tu nombre">
                        </div>
                        <div class="input-group">
                            <label>Edad</label>
                            <input type="number" id="edad" disabled placeholder="Ej: 25">
                        </div>
                    </div>

                    <div class="input-group">
                        <label>Teléfono de Contacto</label>
                        <input type="tel" id="telefono" disabled placeholder="+34 ...">
                    </div>
                </div>

                <!-- ============================
                     SECCIÓN: DATOS DEL VEHÍCULO 
                     ============================ -->
                <div class="profile-section">
                    <h3><i class="fa-solid fa-car"></i> Mi Vehículo</h3>
                    
                    <div class="input-group">
                        <label>Matrícula</label>
                        <input type="text" id="matricula" disabled style="text-transform: uppercase; letter-spacing: 2px; font-weight: bold;" placeholder="0000 XXX">
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div class="input-group">
                            <label>Marca</label>
                            <input type="text" id="marca" disabled placeholder="Ej: Toyota">
                        </div>
                        <div class="input-group">
                            <label>Modelo</label>
                            <input type="text" id="modelo" disabled placeholder="Ej: Corolla">
                        </div>
                    </div>

                    <div class="input-group">
                        <label>Color</label>
                        <input type="text" id="color" disabled placeholder="Ej: Blanco Perla">
                    </div>
                </div>

                <!-- ============================
                     BARRA DE ACCIONES 
                     ============================ -->
                <div class="action-bar">
                    <!-- Botón para activar modo edición -->
                    <button type="button" id="editBtn" class="primary-btn" style="flex: 1;">
                        <i class="fa-solid fa-pen-to-square"></i> Editar Datos
                    </button>
                    
                    <!-- Botón para guardar (oculto por defecto) -->
                    <button type="submit" id="saveBtn" class="primary-btn hidden" style="flex: 1; background: #059669;">
                        <i class="fa-solid fa-save"></i> Guardar Cambios
                    </button>
                    
                    <!-- Botón para cancelar (oculto por defecto) -->
                    <button type="button" id="cancelBtn" class="secondary-btn hidden" style="flex: 1;">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Fondo ambiental animado -->
        <div class="background-ambient"></div>
    </section>

    <!-- ==========================================
         LÓGICA JAVASCRIPT
         ========================================== -->
    <script type="module">
        // Importación de módulos de Firebase necesarios
        import { auth, db } from './js/firebase-config.js';
        import { onAuthStateChanged } from "https://www.gstatic.com/firebasejs/10.8.0/firebase-auth.js";
        // IMPORTANTE: Usamos setDoc en lugar de updateDoc para evitar fallos si el documento no existe
        import { doc, getDoc, setDoc } from "https://www.gstatic.com/firebasejs/10.8.0/firebase-firestore.js";

        // Referencias a elementos del DOM
        const form = document.getElementById('profileForm');
        const editBtn = document.getElementById('editBtn');
        const saveBtn = document.getElementById('saveBtn');
        const cancelBtn = document.getElementById('cancelBtn');
        const loading = document.getElementById('loading');
        
        // Objeto que agrupa todos los inputs para facilitar el manejo
        const inputs = {
            email: document.getElementById('email'),
            nombre: document.getElementById('nombre'),
            edad: document.getElementById('edad'),
            telefono: document.getElementById('telefono'),
            matricula: document.getElementById('matricula'),
            marca: document.getElementById('marca'),
            modelo: document.getElementById('modelo'),
            color: document.getElementById('color')
        };

        // Estado local
        let currentUser = null; // Usuario autenticado actual
        let originalData = {};  // Copia de respaldo para cancelar cambios

        // -------------------------------------------------------------
        // 1. INICIALIZACIÓN: Verificar Autenticación
        // -------------------------------------------------------------
        onAuthStateChanged(auth, async (user) => {
            if (user) {
                currentUser = user;
                loadUserData(user.uid); // Si hay usuario, cargamos sus datos
            } else {
                // Si no hay sesión, mandamos al login
                window.location.href = 'logeo.php'; 
            }
        });

        // -------------------------------------------------------------
        // 2. CARGAR DATOS DESDE FIRESTORE
        // -------------------------------------------------------------
        async function loadUserData(uid) {
            loading.style.display = 'block'; // Mostrar spinner
            try {
                const docRef = doc(db, "users", uid);
                const docSnap = await getDoc(docRef);

                if (docSnap.exists()) {
                    originalData = docSnap.data(); // Guardar copia
                    populateForm(originalData);    // Rellenar formulario
                } else {
                    console.log("No se encontró el documento del usuario en Firestore.");
                }
            } catch (error) {
                console.error("Error al obtener documento:", error);
                alert("Hubo un problema al cargar tus datos. Por favor recarga la página.");
            } finally {
                loading.style.display = 'none'; // Ocultar spinner
            }
        }

        // Rellena los campos del formulario con el objeto de datos
        function populateForm(data) {
            inputs.email.value = data.email || '';
            inputs.nombre.value = data.nombre || '';
            inputs.edad.value = data.edad || '';
            inputs.telefono.value = data.telefono || '';
            inputs.matricula.value = data.matricula || '';
            
            // Validamos que exista el objeto vehiculo antes de acceder a sus propiedades
            if (data.vehiculo) {
                inputs.marca.value = data.vehiculo.marca || '';
                inputs.modelo.value = data.vehiculo.modelo || '';
                inputs.color.value = data.vehiculo.color || '';
            }
        }

        // -------------------------------------------------------------
        // 3. GESTIÓN DEL MODO EDICIÓN
        // -------------------------------------------------------------
        editBtn.addEventListener('click', () => {
            toggleEditMode(true);
        });

        cancelBtn.addEventListener('click', () => {
            toggleEditMode(false);
            populateForm(originalData); // Revertir cambios a lo que había en BD
        });

        // Alterna entre estado de solo lectura y editable
        function toggleEditMode(enable) {
            Object.keys(inputs).forEach(key => {
                if (key !== 'email') { // El email es inmutable por seguridad/diseño
                    inputs[key].disabled = !enable;
                }
            });

            // Manejo de la visibilidad de botones
            if (enable) {
                editBtn.classList.add('hidden');
                saveBtn.classList.remove('hidden');
                cancelBtn.classList.remove('hidden');
                inputs.nombre.focus(); // Poner foco en el primer campo editable
            } else {
                editBtn.classList.remove('hidden');
                saveBtn.classList.add('hidden');
                cancelBtn.classList.add('hidden');
            }
        }

        // -------------------------------------------------------------
        // 4. GUARDADO DE DATOS
        // -------------------------------------------------------------
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            if (!currentUser) return;

            // UI Feedback
            loading.style.display = 'block';
            saveBtn.disabled = true;
            saveBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Guardando...';

            // Preparar el objeto con los datos limpios
            const updatedData = {
                nombre: inputs.nombre.value,
                edad: parseInt(inputs.edad.value) || 0,
                telefono: inputs.telefono.value,
                matricula: inputs.matricula.value.toUpperCase(), // Forzar mayúsculas en matrícula
                vehiculo: {
                    marca: inputs.marca.value,
                    modelo: inputs.modelo.value,
                    color: inputs.color.value
                }
            };

            try {
                // Actualización en Firestore usando setDoc con merge: true
                // Esto crea el documento si no existe, solucionando el error "No document to update"
                const docRef = doc(db, "users", currentUser.uid);
                await setDoc(docRef, updatedData, { merge: true });
                
                // Éxito
                originalData = { ...originalData, ...updatedData }; // Actualizar caché local
                alert("¡Datos actualizados correctamente!");
                toggleEditMode(false);
            } catch (error) {
                console.error("Error actualizando documento:", error);
                alert("Error al guardar los cambios: " + error.message);
            } finally {
                // Restaurar UI
                loading.style.display = 'none';
                saveBtn.disabled = false;
                saveBtn.innerHTML = '<i class="fa-solid fa-save"></i> Guardar Cambios';
            }
        });
    </script>
</body>
</html>
