# GUÍA RÁPIDA - START HERE 🚀

## En 3 minutos tienes BCRYPT funcionando

### 1. Instala las dependencias
```bash
npm install
```

### 2. Prueba que todo funciona
```bash
node tests.js
```

Deberías ver:
```
✨ TODOS LOS TESTS PASARON CORRECTAMENTE ✨
✅ Tu código bcrypt está funcionando perfectamente
```

### 3. Inicia el servidor
```bash
npm start
```

---

## 📁 ARCHIVOS QUE TIENES

| Archivo | Para qué sirve |
|---------|----------------|
| **encriptacion.js** | Funciones core (encriptar/verificar) |
| **rutas-autenticacion.js** | Rutas Express listas (registro, login) |
| **server.js** | Servidor completo funcionando |
| **ejemplos-uso.js** | Ejemplos de cómo usar |
| **tests.js** | Pruebas para verificar todo funciona |
| **README.md** | Documentación completa |
| **INTEGRACION_BD.md** | Cómo conectar tu BD |

---

## 🔥 CÓDIGO MÍNIMO PARA USAR

```javascript
const { encriptarContraseña, verificarContraseña } = require('./encriptacion');

// REGISTRO: encriptar contraseña ANTES de guardar en BD
const contraseñaEncriptada = await encriptarContraseña('usuario123');
// Guarda 'contraseñaEncriptada' en tu BD

// LOGIN: comparar contraseña
const esValida = await verificarContraseña('usuario123', contraseñaDeBD);
if (esValida) {
  console.log('✅ Login correcto');
}
```

---

## 📌 LOS 3 PUNTOS CLAVES

1. **REGISTRO**: Encriptas con `encriptarContraseña()` y guardas en BD
2. **LOGIN**: Verificas con `verificarContraseña()` (sin guardar nada)
3. **NUNCA**: Nunca guardes contraseña sin encriptar

---

## 🔗 SIGUIENTES PASOS

1. Lee `INTEGRACION_BD.md` para conectar tu BD
2. Descomentar líneas en `rutas-autenticacion.js`
3. Tu código está 100% funcional ✅

---

## 🆘 PROBLEMAS COMUNES

**"Cannot find module 'bcryptjs'"**
```bash
npm install bcryptjs
```

**Port 3000 en uso**
```bash
npm start -- --port 3001
```

**¿Cómo conecto MongoDB?**
→ Ver `INTEGRACION_BD.md`

**¿Quiero usar JWT tokens?**
→ Ver `ejemplos-uso.js` (línea ~105)

---

## ✅ CHECKLIST FINAL

- [ ] `npm install` ejecutado
- [ ] `node tests.js` pasó todos los tests
- [ ] `npm start` inicia el servidor
- [ ] Leíste `INTEGRACION_BD.md`
- [ ] Tu BD está conectada
- [ ] Descomentaste líneas en `rutas-autenticacion.js`

**¡Listo! Solo bcrypt, sin complicaciones 🔐**
