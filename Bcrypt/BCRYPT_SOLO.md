# 🔐 BCRYPT - ENCRIPTACIÓN DE CONTRASEÑAS
## 📦 CONTENIDO

| Archivo | Qué hace |
|---------|----------|
| **encriptacion.js** | Funciones core de bcrypt |
| **rutas-autenticacion.js** | Rutas Express (listas para copiar) |
| **server.js** | Servidor funcional |
| **ejemplos-uso.js** | Ejemplos de integración |
| **tests.js** | Tests para verificar todo |
| **INTEGRACION_BD.md** | Cómo conectar con BD |

---

## 🚀 INICIO RÁPIDO

### 1. Instala dependencias
```bash
npm install
```

### 2. Prueba que funciona
```bash
node tests.js
```

### 3. Usa en tu código

```javascript
const { encriptarContraseña, verificarContraseña } = require('./encriptacion');

// AL REGISTRAR
const hash = await encriptarContraseña(contraseña);
// Guarda `hash` en tu BD

// AL HACER LOGIN
const esValida = await verificarContraseña(contraseña, passwordBD);
if (esValida) {
  // Login correcto
}
```

---

## 🔐 2 FUNCIONES PRINCIPALES

### `encriptarContraseña(contraseña)`
```javascript
const hash = await encriptarContraseña('MiPassword123');
// Resultado: $2a$10$xxx...xxx (64 caracteres)
// Guarda esto en BD
```

### `verificarContraseña(contraseña, hash)`
```javascript
const esValida = await verificarContraseña('MiPassword123', hashBD);
// Resultado: true o false
// No desencripta, solo compara
```

---

## 📝 INTEGRACIÓN EN 3 PASOS

### Paso 1: Copiar `encriptacion.js` a tu proyecto
```
tu-proyecto/
└── utils/
    └── encriptacion.js
```

### Paso 2: En tu ruta de REGISTRO
```javascript
const { encriptarContraseña } = require('./utils/encriptacion');

app.post('/registro', async (req, res) => {
  const { email, contraseña } = req.body;
  
  const hash = await encriptarContraseña(contraseña);
  
  const usuario = new Usuario({
    email,
    contraseña: hash  // ← Guardas encriptada
  });
  await usuario.save();
  
  res.json({ mensaje: 'Registrado' });
});
```

### Paso 3: En tu ruta de LOGIN
```javascript
const { verificarContraseña } = require('./utils/encriptacion');

app.post('/login', async (req, res) => {
  const { email, contraseña } = req.body;
  
  const usuario = await Usuario.findOne({ email });
  
  const esValida = await verificarContraseña(contraseña, usuario.contraseña);
  if (!esValida) return res.status(401).json({ error: 'Incorrecta' });
  
  // Login correcto, genera tu JWT aquí (ya lo tienes)
  res.json({ mensaje: 'Login exitoso' });
});
```

---

## ✅ CHECKLIST

- [ ] `npm install` ejecutado
- [ ] `node tests.js` pasó todos los tests
- [ ] Copiaste `encriptacion.js` a tu proyecto
- [ ] Importaste en rutas de registro/login
- [ ] Probaste registro y login
- [ ] Tu BD guarda contraseñas ENCRIPTADAS

---

## ⚠️ IMPORTANTE

✅ Contraseñas SIEMPRE encriptadas  
✅ Usar `await` con las funciones async  
✅ Guardar el HASH en BD, no la contraseña  

❌ Nunca desencriptes bcrypt  
❌ Nunca guardes contraseña sin encriptar  
❌ Nunca hagas `usuario.contraseña === contraseña`

---

## 📚 Más documentación

- **INTEGRACION_BD.md** - Ejemplos con MongoDB, MySQL, PostgreSQL
- **ejemplos-uso.js** - Más ejemplos de código
- **rutas-autenticacion.js** - Rutas completas listas para copiar

**¡Tu web tiene encriptación segura! 🔐**
