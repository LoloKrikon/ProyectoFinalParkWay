// =============================================
// RUTAS CON AUTENTICACIÓN - LISTO PARA TU WEB
// =============================================
// Integra esto en tu servidor Express

const express = require('express');
const router = express.Router();
const { encriptarContraseña, verificarContraseña } = require('./encriptacion');

// IMPORTA TU MODELO DE USUARIO (ejemplo con tu BDD)
// const Usuario = require('./models/Usuario'); // Tu modelo de BD

// =============================================
// RUTA 1: REGISTRO DE USUARIO
// =============================================
// POST /api/auth/registro
// Body: { email, contraseña }

router.post('/registro', async (req, res) => {
  try {
    const { email, contraseña } = req.body;

    // Validaciones básicas
    if (!email || !contraseña) {
      return res.status(400).json({ 
        error: 'Email y contraseña son requeridos' 
      });
    }

    if (contraseña.length < 6) {
      return res.status(400).json({ 
        error: 'La contraseña debe tener mínimo 6 caracteres' 
      });
    }

    // Verificar si el usuario ya existe
    // const usuarioExistente = await Usuario.findOne({ email });
    // if (usuarioExistente) {
    //   return res.status(400).json({ error: 'El email ya está registrado' });
    // }

    // Encriptar la contraseña
    const contraseñaEncriptada = await encriptarContraseña(contraseña);

    // Crear nuevo usuario en BD
    // const nuevoUsuario = new Usuario({
    //   email,
    //   contraseña: contraseñaEncriptada
    // });
    // await nuevoUsuario.save();

    res.status(201).json({ 
      mensaje: 'Usuario registrado exitosamente',
      // usuario: { id: nuevoUsuario._id, email: nuevoUsuario.email }
    });

  } catch (error) {
    console.error('Error en registro:', error);
    res.status(500).json({ error: 'Error en el registro' });
  }
});

// =============================================
// RUTA 2: LOGIN DE USUARIO
// =============================================
// POST /api/auth/login
// Body: { email, contraseña }

router.post('/login', async (req, res) => {
  try {
    const { email, contraseña } = req.body;

    // Validaciones
    if (!email || !contraseña) {
      return res.status(400).json({ 
        error: 'Email y contraseña son requeridos' 
      });
    }

    // Buscar usuario en BD
    // const usuario = await Usuario.findOne({ email });
    // if (!usuario) {
    //   return res.status(401).json({ error: 'Email o contraseña inválidos' });
    // }

    // Verificar contraseña
    // const esValida = await verificarContraseña(contraseña, usuario.contraseña);
    // if (!esValida) {
    //   return res.status(401).json({ error: 'Email o contraseña inválidos' });
    // }

    // Aquí puedes generar un token JWT si lo necesitas
    // const token = generarToken(usuario._id);

    res.json({ 
      mensaje: 'Login exitoso',
      // token: token,
      // usuario: { id: usuario._id, email: usuario.email }
    });

  } catch (error) {
    console.error('Error en login:', error);
    res.status(500).json({ error: 'Error en el login' });
  }
});

// =============================================
// RUTA 3: CAMBIAR CONTRASEÑA
// =============================================
// PUT /api/auth/cambiar-contraseña
// Body: { usuarioId, contraseñaActual, contraseñaNueva }

router.put('/cambiar-contraseña', async (req, res) => {
  try {
    const { usuarioId, contraseñaActual, contraseñaNueva } = req.body;

    if (!contraseñaNueva || contraseñaNueva.length < 6) {
      return res.status(400).json({ 
        error: 'La nueva contraseña debe tener mínimo 6 caracteres' 
      });
    }

    // Buscar usuario
    // const usuario = await Usuario.findById(usuarioId);
    // if (!usuario) {
    //   return res.status(404).json({ error: 'Usuario no encontrado' });
    // }

    // Verificar contraseña actual
    // const esValida = await verificarContraseña(contraseñaActual, usuario.contraseña);
    // if (!esValida) {
    //   return res.status(401).json({ error: 'Contraseña actual inválida' });
    // }

    // Encriptar nueva contraseña
    const nuevaEncriptada = await encriptarContraseña(contraseñaNueva);

    // Actualizar en BD
    // usuario.contraseña = nuevaEncriptada;
    // await usuario.save();

    res.json({ mensaje: 'Contraseña actualizada exitosamente' });

  } catch (error) {
    console.error('Error al cambiar contraseña:', error);
    res.status(500).json({ error: 'Error al cambiar contraseña' });
  }
});

module.exports = router;
