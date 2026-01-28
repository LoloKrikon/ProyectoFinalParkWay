// =============================================
// EJEMPLOS DE USO - CÓMO INTEGRAR EN TU WEB
// =============================================

// =============================================
// EJEMPLO 1: USO BÁSICO EN TU CÓDIGO
// =============================================

// Importar las funciones
const { encriptarContraseña, verificarContraseña } = require('./encriptacion');

// --- CUANDO UN USUARIO SE REGISTRA ---
async function registrarUsuario(email, contraseña) {
  // 1. Encriptar la contraseña
  const contraseñaEncriptada = await encriptarContraseña(contraseña);
  
  // 2. Guardar en BD (ejemplo con MongoDB)
  // const usuario = new Usuario({
  //   email: email,
  //   contraseña: contraseñaEncriptada  // ← Guardas LA ENCRIPTADA
  // });
  // await usuario.save();
  
  console.log(`Usuario ${email} registrado con contraseña encriptada`);
}

// --- CUANDO UN USUARIO INTENTA LOGIN ---
async function loginUsuario(emailIngresado, contraseñaIngresada, contraseñaBD) {
  // 1. Verificar que la contraseña coincide
  const esValida = await verificarContraseña(contraseñaIngresada, contraseñaBD);
  
  if (esValida) {
    console.log('✅ Login correcto');
    // Crear sesión, JWT token, etc.
  } else {
    console.log('❌ Contraseña incorrecta');
  }
}

// =============================================
// EJEMPLO 2: CON TU BASE DE DATOS
// =============================================

// Si usas MongoDB con Mongoose:
/*
const usuarioSchema = new Schema({
  email: {
    type: String,
    required: true,
    unique: true
  },
  contraseña: {
    type: String,
    required: true
    // ← Aquí guardas la contraseña ENCRIPTADA
  },
  fechaRegistro: {
    type: Date,
    default: Date.now
  }
});

// Cuando creas un usuario:
const usuario = new Usuario({
  email: 'usuario@example.com',
  contraseña: await encriptarContraseña('MiContraseña123')
});
await usuario.save();

// Cuando el usuario hace login:
const usuario = await Usuario.findOne({ email: 'usuario@example.com' });
const esValida = await verificarContraseña('MiContraseña123', usuario.contraseña);
*/

// =============================================
// EJEMPLO 3: CON JWT (token de sesión)
// =============================================

const jwt = require('jsonwebtoken');
const SECRET_KEY = process.env.SECRET_KEY || 'tu_clave_secreta_aqui';

function generarToken(usuarioId) {
  return jwt.sign(
    { id: usuarioId },
    SECRET_KEY,
    { expiresIn: '24h' }  // Token válido por 24 horas
  );
}

async function loginCompleto(email, contraseña) {
  // 1. Buscar usuario
  // const usuario = await Usuario.findOne({ email });
  
  // 2. Verificar contraseña
  // const esValida = await verificarContraseña(contraseña, usuario.contraseña);
  
  // 3. Si es válida, generar token
  // if (esValida) {
  //   const token = generarToken(usuario._id);
  //   return { token, usuario };
  // }
}

// =============================================
// EJEMPLO 4: MIDDLEWARE DE PROTECCIÓN
// =============================================

function verificarToken(req, res, next) {
  const token = req.headers.authorization?.split(' ')[1];
  
  if (!token) {
    return res.status(401).json({ error: 'Token requerido' });
  }
  
  try {
    const datos = jwt.verify(token, SECRET_KEY);
    req.usuarioId = datos.id;
    next();
  } catch (error) {
    res.status(401).json({ error: 'Token inválido' });
  }
}

// Usar en una ruta protegida:
// app.get('/perfil', verificarToken, (req, res) => {
//   res.json({ usuarioId: req.usuarioId });
// });

// =============================================
// EJEMPLO 5: PRUEBAS CON CURL (terminal)
// =============================================

/*
// 1. REGISTRAR USUARIO
curl -X POST http://localhost:3000/api/auth/registro \
  -H "Content-Type: application/json" \
  -d '{"email":"usuario@example.com","contraseña":"Password123"}'

// 2. LOGIN
curl -X POST http://localhost:3000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"usuario@example.com","contraseña":"Password123"}'

// 3. CAMBIAR CONTRASEÑA
curl -X PUT http://localhost:3000/api/auth/cambiar-contraseña \
  -H "Content-Type: application/json" \
  -d '{"usuarioId":"ID_DEL_USUARIO","contraseñaActual":"Password123","contraseñaNueva":"NuevaPassword456"}'
*/

module.exports = {
  registrarUsuario,
  loginUsuario,
  loginCompleto,
  generarToken,
  verificarToken
};
