// =============================================
// SERVIDOR EXPRESS FUNCIONAL CON BCRYPT
// =============================================
// Este es un servidor completo y listo para usar

const express = require('express');
const app = express();
const rutasAutenticacion = require('./rutas-autenticacion');

// =============================================
// CONFIGURACIÓN
// =============================================
const PORT = process.env.PORT || 3000;

// Middleware
app.use(express.json());
app.use(express.urlencoded({ extended: true }));

// =============================================
// RUTAS
// =============================================
// Todas las rutas de autenticación
app.use('/api/auth', rutasAutenticacion);

// Ruta de prueba
app.get('/', (req, res) => {
  res.json({ 
    mensaje: 'Servidor funcionando correctamente',
    rutas: {
      registro: 'POST /api/auth/registro',
      login: 'POST /api/auth/login',
      cambiarContraseña: 'PUT /api/auth/cambiar-contraseña'
    }
  });
});

// =============================================
// MANEJO DE ERRORES
// =============================================
app.use((err, req, res, next) => {
  console.error('Error:', err);
  res.status(500).json({ error: 'Error interno del servidor' });
});

// =============================================
// INICIAR SERVIDOR
// =============================================
app.listen(PORT, () => {
  console.log(`✅ Servidor corriendo en http://localhost:${PORT}`);
  console.log(`📝 Usa estas rutas para autenticación con bcrypt:`);
  console.log(`   - POST /api/auth/registro (crear usuario)`);
  console.log(`   - POST /api/auth/login (iniciar sesión)`);
  console.log(`   - PUT /api/auth/cambiar-contraseña (cambiar contraseña)`);
});

module.exports = app;
