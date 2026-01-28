# GUÍA RÁPIDA DE INTEGRACIÓN CON BD

## ¿CÓMO USAR ESTE CÓDIGO EN TU PROYECTO WEB?

---

## OPCIÓN 1: MongoDB + Mongoose

### Paso 1: Descomenta en `rutas-autenticacion.js`

Busca estas líneas y descomenta:
```javascript
const Usuario = require('./models/Usuario');

// En registro:
const usuarioExistente = await Usuario.findOne({ email });
if (usuarioExistente) {
  return res.status(400).json({ error: 'El email ya está registrado' });
}

const nuevoUsuario = new Usuario({
  email,
  contraseña: contraseñaEncriptada
});
await nuevoUsuario.save();
```

### Paso 2: Tu modelo Usuario debe tener:

```javascript
// models/Usuario.js
const Schema = require('mongoose').Schema;
const usuarioSchema = new Schema({
  email: String,
  contraseña: String,  // ← Aquí va la encriptada
  nombre: String,
  fechaRegistro: { type: Date, default: Date.now }
});

module.exports = require('mongoose').model('Usuario', usuarioSchema);
```

---

## OPCIÓN 2: MySQL + Sequelize

### Paso 1: Modelo Usuario en Sequelize

```javascript
// models/Usuario.js
module.exports = (sequelize, DataTypes) => {
  return sequelize.define('Usuario', {
    email: {
      type: DataTypes.STRING,
      unique: true,
      allowNull: false
    },
    contraseña: {
      type: DataTypes.STRING,
      allowNull: false
      // ← Aquí va la encriptada de bcrypt
    }
  });
};
```

### Paso 2: En tus rutas (ejemplo con Sequelize)

```javascript
const { Usuario } = require('./models');
const { encriptarContraseña, verificarContraseña } = require('./encriptacion');

// REGISTRO
app.post('/registro', async (req, res) => {
  const { email, contraseña } = req.body;
  
  const contraseñaEncriptada = await encriptarContraseña(contraseña);
  
  const usuario = await Usuario.create({
    email,
    contraseña: contraseñaEncriptada
  });
  
  res.json({ mensaje: 'Registrado', usuario });
});

// LOGIN
app.post('/login', async (req, res) => {
  const { email, contraseña } = req.body;
  
  const usuario = await Usuario.findOne({ where: { email } });
  if (!usuario) return res.status(401).json({ error: 'No existe' });
  
  const esValida = await verificarContraseña(contraseña, usuario.contraseña);
  if (!esValida) return res.status(401).json({ error: 'Contraseña incorrecta' });
  
  res.json({ mensaje: 'Login exitoso', usuario });
});
```

---

## OPCIÓN 3: PostgreSQL + Knex.js

```javascript
const { encriptarContraseña, verificarContraseña } = require('./encriptacion');
const knex = require('./db'); // Tu conexión a BD

// REGISTRO
app.post('/registro', async (req, res) => {
  const { email, contraseña } = req.body;
  
  const contraseñaEncriptada = await encriptarContraseña(contraseña);
  
  await knex('usuarios').insert({
    email,
    contraseña: contraseñaEncriptada
  });
  
  res.json({ mensaje: 'Registrado' });
});

// LOGIN
app.post('/login', async (req, res) => {
  const { email, contraseña } = req.body;
  
  const usuario = await knex('usuarios').where({ email }).first();
  if (!usuario) return res.status(401).json({ error: 'No existe' });
  
  const esValida = await verificarContraseña(contraseña, usuario.contraseña);
  if (!esValida) return res.status(401).json({ error: 'Contraseña incorrecta' });
  
  res.json({ mensaje: 'Login exitoso' });
});
```

---

## LO MÁS IMPORTANTE:

### ✅ SIEMPRE:
1. Encriptar contraseña ANTES de guardar en BD
2. Usar `await` con las funciones de bcrypt
3. Guardar la contraseña ENCRIPTADA (nunca texto plano)
4. Comparar con `verificarContraseña()` en login

### ❌ NUNCA:
1. Guardar contraseña sin encriptar
2. Desencriptar la contraseña de BD
3. Comparar strings directamente sin bcrypt
4. Guardar la contraseña en logs/archivos

---

## TABLA DE REFERENCIA:

| Situación | Función | Guarda |
|-----------|---------|--------|
| Usuario se registra | `encriptarContraseña()` | Versión encriptada en BD |
| Usuario hace login | `verificarContraseña()` | No guardas nada, solo verificas |
| Usuario cambia contraseña | `encriptarContraseña()` | Nueva versión encriptada en BD |

---

## EJEMPLO COMPLETO (MongoDB):

```javascript
// En tu ruta de login
app.post('/login', async (req, res) => {
  try {
    const { email, contraseña } = req.body;
    
    // 1. Buscar usuario en BD
    const usuario = await Usuario.findOne({ email });
    if (!usuario) {
      return res.status(401).json({ error: 'Credenciales inválidas' });
    }
    
    // 2. Verificar contraseña con bcrypt
    const esValida = await verificarContraseña(contraseña, usuario.contraseña);
    if (!esValida) {
      return res.status(401).json({ error: 'Credenciales inválidas' });
    }
    
    // 3. ¡Login exitoso!
    res.json({ 
      mensaje: 'Login exitoso',
      usuarioId: usuario._id,
      email: usuario.email
    });
    
  } catch (error) {
    res.status(500).json({ error: 'Error en servidor' });
  }
});
```

---

**¡Listo! Ahora tienes todo integrado con tu BD real.** 🚀
