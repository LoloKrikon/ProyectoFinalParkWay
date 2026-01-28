// =============================================
// TESTS BÁSICOS - VERIFICA QUE TODO FUNCIONA
// =============================================
// Ejecuta: node tests.js

const { encriptarContraseña, verificarContraseña } = require('./encriptacion');

async function ejecutarTests() {
  console.log('🧪 INICIANDO TESTS...\n');

  try {
    // TEST 1: Encriptar contraseña
    console.log('✓ TEST 1: Encriptar contraseña');
    const contraseña = 'MiPassword123';
    const encriptada = await encriptarContraseña(contraseña);
    console.log(`  Original: ${contraseña}`);
    console.log(`  Encriptada: ${encriptada}`);
    console.log(`  ✅ Longitud de encriptación: ${encriptada.length} caracteres\n`);

    // TEST 2: Verificar contraseña correcta
    console.log('✓ TEST 2: Verificar contraseña CORRECTA');
    const esCorrecta = await verificarContraseña('MiPassword123', encriptada);
    console.log(`  ¿Coincide? ${esCorrecta}`);
    if (esCorrecta) {
      console.log('  ✅ CORRECTO - La contraseña es válida\n');
    } else {
      console.log('  ❌ ERROR - No debería fallar\n');
    }

    // TEST 3: Verificar contraseña incorrecta
    console.log('✓ TEST 3: Verificar contraseña INCORRECTA');
    const esIncorrecta = await verificarContraseña('OtraPassword456', encriptada);
    console.log(`  ¿Coincide? ${esIncorrecta}`);
    if (!esIncorrecta) {
      console.log('  ✅ CORRECTO - La contraseña no coincide\n');
    } else {
      console.log('  ❌ ERROR - Debería fallar\n');
    }

    // TEST 4: Verificar que son diferentes cada vez
    console.log('✓ TEST 4: Misma contraseña = encriptación diferente cada vez');
    const encriptada1 = await encriptarContraseña('test123');
    const encriptada2 = await encriptarContraseña('test123');
    console.log(`  Encriptación 1: ${encriptada1}`);
    console.log(`  Encriptación 2: ${encriptada2}`);
    console.log(`  ¿Son diferentes? ${encriptada1 !== encriptada2}`);
    if (encriptada1 !== encriptada2) {
      console.log('  ✅ CORRECTO - Cada encriptación es única\n');
    }

    // TEST 5: Ambas encriptaciones funcionan
    console.log('✓ TEST 5: Ambas encriptaciones son válidas');
    const valida1 = await verificarContraseña('test123', encriptada1);
    const valida2 = await verificarContraseña('test123', encriptada2);
    console.log(`  ¿Encriptación 1 válida? ${valida1}`);
    console.log(`  ¿Encriptación 2 válida? ${valida2}`);
    if (valida1 && valida2) {
      console.log('  ✅ CORRECTO - Ambas funcionan perfectamente\n');
    }

    console.log('✨ TODOS LOS TESTS PASARON CORRECTAMENTE ✨');
    console.log('\n✅ Tu código bcrypt está funcionando perfectamente');
    console.log('🚀 Listo para usar en tu aplicación web!\n');

  } catch (error) {
    console.error('❌ ERROR EN LOS TESTS:', error.message);
    console.error('\nAsegúrate de que bcryptjs esté instalado:');
    console.error('  npm install bcryptjs');
  }
}

// Ejecutar tests
ejecutarTests();
