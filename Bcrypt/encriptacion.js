// =============================================
// MÓDULO DE ENCRIPTACIÓN CON BCRYPT
// =============================================
// Este archivo contiene todas las funciones para:
// - Encriptar contraseñas (registro de usuarios)
// - Verificar contraseñas (login de usuarios)
// - Listo para usar en tu base de datos

const bcrypt = require('bcryptjs');

// =============================================
// FUNCIÓN 1: ENCRIPTAR CONTRASEÑA (REGISTRO)
// =============================================
// Uso: cuando un usuario se registra o cambia su contraseña
// Devuelve la contraseña encriptada para guardar en BD

async function encriptarContraseña(contraseña) {
  try {
    // Nivel de encriptación (rounds): 10 es seguro y rápido
    const salt = await bcrypt.genSalt(10);
    
    // Encriptar la contraseña
    const contraseñaEncriptada = await bcrypt.hash(contraseña, salt);
    
    return contraseñaEncriptada;
  } catch (error) {
    console.error('Error al encriptar contraseña:', error);
    throw new Error('Error en la encriptación');
  }
}

// =============================================
// FUNCIÓN 2: VERIFICAR CONTRASEÑA (LOGIN)
// =============================================
// Uso: cuando un usuario intenta hacer login
// Compara la contraseña ingresada con la guardada en BD
// Devuelve true o false

async function verificarContraseña(contraseña, contraseñaEncriptada) {
  try {
    // Compara la contraseña en texto plano con la encriptada
    const esValida = await bcrypt.compare(contraseña, contraseñaEncriptada);
    
    return esValida;
  } catch (error) {
    console.error('Error al verificar contraseña:', error);
    throw new Error('Error en la verificación');
  }
}

// =============================================
// FUNCIÓN 3: ENCRIPTAR CON ROUNDAS PERSONALIZADAS
// =============================================
// Uso: si quieres más o menos seguridad
// Más roundas = más seguro pero más lento
// Menos roundas = más rápido pero menos seguro

async function encriptarConRoundas(contraseña, roundas = 10) {
  try {
    const salt = await bcrypt.genSalt(roundas);
    const contraseñaEncriptada = await bcrypt.hash(contraseña, salt);
    return contraseñaEncriptada;
  } catch (error) {
    console.error('Error al encriptar:', error);
    throw new Error('Error en la encriptación');
  }
}

// =============================================
// EXPORTAR FUNCIONES
// =============================================
module.exports = {
  encriptarContraseña,
  verificarContraseña,
  encriptarConRoundas
};
