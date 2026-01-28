# 🔧 GUÍA PASO A PASO: INTEGRACIÓN SEGURA EN TU WEB

## ⚠️ ANTES DE EMPEZAR

**NO hagas cambios directos todavía.** Sigue estos pasos para NO romper nada:

1. Haz BACKUP de tu proyecto actual
2. Prueba en LOCAL primero (tu computadora)
3. Solo luego subes a PRODUCCIÓN

---

## PASO 1: PREPARAR TU PROYECTO

### Si tu proyecto actual es así:

```
tu-web/
├── server.js
├── routes/
│   └── users.js (aquí está tu registro/login)
├── models/
│   └── Usuario.js
└── package.json
```

### Copia estos archivos aquí:

```
tu-web/
├── server.js
├── routes/
│   ├── users.js (TUS rutas actuales)
│   └── auth.js (NUEVA ruta con bcrypt)
├── models/
│   └── Usuario.js
├── utils/
│   └── encriptacion.js (COPIAR AQUÍ)
└── package.json (actualizar)
```

---

## PASO 2: INSTALAR BCRYPTJS

En `package.json`, asegúrate que tengas:

```json
{
  "dependencies": {
    "bcryptjs": "^2.4.3",
    "express": "^4.18.2",
    "mongoose": "^7.0.0"  // o la BD que uses
  }
}
```

Luego ejecuta:
```bash
npm install
```

---

## PASO 3: COPIAR EL ARCHIVO DE ENCRIPTACIÓN

Copia `encriptacion.js` a tu carpeta `utils/`:

**utils/encriptacion.js** (es el que ya hicimos)

```javascript
const bcrypt = require('bcryptjs');

async function encriptarContraseña(contraseña) {
  const salt = await bcrypt.genSalt(10);
  const contraseñaEncriptada = await bcrypt.hash(contraseña, salt);
  return contraseñaEncriptada;
}

async function verificarContraseña(contraseña, contraseñaEncriptada) {
  const esValida = await bcrypt.compare(contraseña, contraseñaEncriptada);
  return esValida;
}

module.exports = { encriptarContraseña, verificarContraseña };
```

---

## PASO 4: ACTUALIZAR TU MODELO USUARIO

Si usas **MongoDB + Mongoose**, tu modelo debe ser así:

**models/Usuario.js**

```javascript
const Schema = require('mongoose').Schema;

const usuarioSchema = new Schema({
  email: {
    type: String,
    required: true,
    unique: true,
    lowercase: true
  },
  contraseña: {
    type: String,
    required: true
    // ← La contraseña ENCRIPTADA va aquí
  },
  nombre: String,
  fechaRegistro: {
    type: Date,
    default: Date.now
  }
});

module.exports = require('mongoose').model('Usuario', usuarioSchema);
```

Si usas **MySQL + Sequelize**:

```javascript
module.exports = (sequelize, DataTypes) => {
  return sequelize.define('Usuario', {
    email: {
      type: DataTypes.STRING,
      allowNull: false,
      unique: true
    },
    contraseña: {
      type: DataTypes.STRING,
      allowNull: false
      // ← La contraseña ENCRIPTADA va aquí
    },
    nombre: DataTypes.STRING,
    fechaRegistro: {
      type: DataTypes.DATE,
      defaultValue: DataTypes.NOW
    }
  });
};
```

---

## PASO 5: REEMPLAZAR TUS RUTAS DE REGISTRO Y LOGIN

### Tu código ANTIGUO (sin bcrypt):

```javascript
// ❌ MAL - Sin encriptación
app.post('/registro', async (req, res) => {
  const { email, contraseña } = req.body;
  
  // ❌ Guarda la contraseña en TEXTO PLANO (INSEGURO)
  const usuario = new Usuario({
    email,
    contraseña: contraseña  // ← ¡NUNCA hagas esto!
  });
  await usuario.save();
  res.json({ mensaje: 'Registrado' });
});
```

### Tu código NUEVO (con bcrypt):

```javascript
const { encriptarContraseña, verificarContraseña } = require('../utils/encriptacion');
const Usuario = require('../models/Usuario');

// ✅ BIEN - Con encriptación
app.post('/registro', async (req, res) => {
  try {
    const { email, contraseña } = req.body;
    
    // Validaciones
    if (!email || !contraseña) {
      return res.status(400).json({ error: 'Email y contraseña requeridos' });
    }
    
    if (contraseña.length < 6) {
      return res.status(400).json({ 
        error: 'Contraseña mínimo 6 caracteres' 
      });
    }
    
    // Verificar que el email NO exista ya
    const usuarioExistente = await Usuario.findOne({ email });
    if (usuarioExistente) {
      return res.status(400).json({ error: 'Email ya registrado' });
    }
    
    // ✅ ENCRIPTAR contraseña ANTES de guardar
    const contraseñaEncriptada = await encriptarContraseña(contraseña);
    
    // Guardar usuario con contraseña ENCRIPTADA
    const usuario = new Usuario({
      email,
      contraseña: contraseñaEncriptada  // ✅ ENCRIPTADA
    });
    
    await usuario.save();
    
    res.status(201).json({ 
      mensaje: 'Usuario registrado exitosamente',
      usuario: { id: usuario._id, email: usuario.email }
    });
    
  } catch (error) {
    console.error('Error en registro:', error);
    res.status(500).json({ error: 'Error en el registro' });
  }
});
```

---

## PASO 6: REEMPLAZAR LOGIN

### Código ANTIGUO (sin bcrypt):

```javascript
// ❌ MAL - Comparación directa
app.post('/login', async (req, res) => {
  const { email, contraseña } = req.body;
  const usuario = await Usuario.findOne({ email });
  
  // ❌ Esto NO funciona con bcrypt
  if (usuario.contraseña === contraseña) {
    res.json({ mensaje: 'Login ok' });
  }
});
```

### Código NUEVO (con bcrypt):

```javascript
// ✅ BIEN - Con bcrypt
app.post('/login', async (req, res) => {
  try {
    const { email, contraseña } = req.body;
    
    // Validaciones
    if (!email || !contraseña) {
      return res.status(400).json({ error: 'Email y contraseña requeridos' });
    }
    
    // Buscar usuario
    const usuario = await Usuario.findOne({ email });
    if (!usuario) {
      return res.status(401).json({ error: 'Credenciales inválidas' });
    }
    
    // ✅ VERIFICAR contraseña con bcrypt
    const esValida = await verificarContraseña(contraseña, usuario.contraseña);
    
    if (!esValida) {
      return res.status(401).json({ error: 'Credenciales inválidas' });
    }
    
    // ✅ Login correcto
    res.json({ 
      mensaje: 'Login exitoso',
      usuario: { 
        id: usuario._id, 
        email: usuario.email,
        nombre: usuario.nombre
      }
    });
    
  } catch (error) {
    console.error('Error en login:', error);
    res.status(500).json({ error: 'Error en el login' });
  }
});
```

---

## PASO 7: CAMBIAR CONTRASEÑA (BONUS)

```javascript
app.put('/cambiar-contraseña', async (req, res) => {
  try {
    const { usuarioId, contraseñaActual, contraseñaNueva } = req.body;
    
    // Buscar usuario
    const usuario = await Usuario.findById(usuarioId);
    if (!usuario) {
      return res.status(404).json({ error: 'Usuario no encontrado' });
    }
    
    // Verificar contraseña actual
    const esValida = await verificarContraseña(
      contraseñaActual, 
      usuario.contraseña
    );
    
    if (!esValida) {
      return res.status(401).json({ error: 'Contraseña actual inválida' });
    }
    
    if (contraseñaNueva.length < 6) {
      return res.status(400).json({ 
        error: 'Nueva contraseña mínimo 6 caracteres' 
      });
    }
    
    // Encriptar nueva contraseña
    const nuevaEncriptada = await encriptarContraseña(contraseñaNueva);
    
    // Actualizar
    usuario.contraseña = nuevaEncriptada;
    await usuario.save();
    
    res.json({ mensaje: 'Contraseña actualizada' });
    
  } catch (error) {
    res.status(500).json({ error: 'Error al cambiar contraseña' });
  }
});
```

---

## PASO 8: VERIFICAR QUE TODO FUNCIONA

### Prueba en POSTMAN o CURL:

**1. Registrar usuario:**
```bash
curl -X POST http://localhost:3000/registro \
  -H "Content-Type: application/json" \
  -d '{"email":"usuario@example.com","contraseña":"Password123"}'
```

**2. Login con contraseña CORRECTA:**
```bash
curl -X POST http://localhost:3000/login \
  -H "Content-Type: application/json" \
  -d '{"email":"usuario@example.com","contraseña":"Password123"}'
```

**Respuesta esperada:**
```json
{
  "mensaje": "Login exitoso",
  "usuario": {
    "id": "123abc",
    "email": "usuario@example.com"
  }
}
```

**3. Login con contraseña INCORRECTA:**
```bash
curl -X POST http://localhost:3000/login \
  -H "Content-Type: application/json" \
  -d '{"email":"usuario@example.com","contraseña":"ContraseñaWrong"}'
```

**Respuesta esperada:**
```json
{
  "error": "Credenciales inválidas"
}
```

---

## ⚠️ CHECKLIST FINAL (Para no romper nada)

- [ ] Hiciste BACKUP de tu proyecto
- [ ] Instalaste `npm install bcryptjs`
- [ ] Copiaste `encriptacion.js` a `utils/`
- [ ] Importaste funciones en tus rutas
- [ ] **NO tocaste** la BD existente
- [ ] Probaste REGISTRO
- [ ] Probaste LOGIN con contraseña CORRECTA
- [ ] Probaste LOGIN con contraseña INCORRECTA
- [ ] Todo funciona? ✅ → Listo para PRODUCCIÓN

---

## 🚨 COSAS QUE NUNCA DEBES HACER

❌ **Nunca** cambies contraseñas que ya existen en BD sin encriptar  
❌ **Nunca** hagas `usuario.contraseña === contraseña` (es inseguro)  
❌ **Nunca** intentes desencriptar una contraseña de bcrypt  
❌ **Nunca** guardes contraseña en TEXTO PLANO  
❌ **Nunca** elimines el `await` en las funciones async

---

## 💡 MIGRAR USUARIOS EXISTENTES (Si tienes usuarios sin encriptar)

Si tu BD tiene usuarios con contraseñas sin encriptar:

```javascript
// Script para migrar SOLO UNA VEZ
const { encriptarContraseña } = require('./utils/encriptacion');
const Usuario = require('./models/Usuario');

async function migrarContraseñas() {
  try {
    // Busca usuarios sin encriptación (ejemplo: la contraseña no comienza con $2)
    const usuariosSinEncriptar = await Usuario.find({
      contraseña: { $not: /^\$2/ }
    });
    
    console.log(`Encontrados ${usuariosSinEncriptar.length} usuarios sin encriptar`);
    
    for (let usuario of usuariosSinEncriptar) {
      const contraseñaEncriptada = await encriptarContraseña(usuario.contraseña);
      usuario.contraseña = contraseñaEncriptada;
      await usuario.save();
      console.log(`✅ Encriptado: ${usuario.email}`);
    }
    
    console.log('✅ Migración completada');
  } catch (error) {
    console.error('Error en migración:', error);
  }
}

// Ejecutar: node migration.js
migrarContraseñas();
```

---

## ¿DUDAS?

- Si algo no funciona, revisa que `bcryptjs` esté instalado: `npm list bcryptjs`
- Verifica que importas las funciones correctamente en tus rutas
- Asegúrate de usar `await` con las funciones async

**¡Listo! Ahora tu web tiene seguridad de nivel profesional 🔐**
