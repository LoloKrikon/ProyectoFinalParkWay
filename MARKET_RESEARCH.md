# Proyecto: App de Encuentro de Aparcamiento Social

## Descripción General
La idea consiste en una aplicación móvil colaborativa diseñada para facilitar la búsqueda de aparcamiento en zonas urbanas, abarcando zona azul (pago), aparcamientos públicos y plazas libres en la calle. El núcleo de la aplicación es la colaboración entre usuarios: informar cuando se deja una plaza libre y recibir avisos sobre huecos disponibles.

## Análisis de Mercado y Competencia
Existen y han existido varias empresas con modelos similares. El mercado es competitivo y difícil debido a la necesidad de coordinar a muchos usuarios en tiempo real.

### Competidores Directos e Indirectos
1. **Wazypark (Referencia Histórica):** Fue la aplicación líder en España con este mismo concepto (avisar de huecos, comunidad). Llegó a tener muchos usuarios pero cerró/pivotó debido a la dificultad de monetizar y mantener la fiabilidad de los datos.
2. **Parkifast:** Utiliza algoritmos de detección automática (acelerómetros del móvil) para detectar cuándo un coche aparca o desaparca sin necesidad de que el usuario lo reporte manualmente.
3. **Parquo:** Enfocada más en el alquiler de plazas privadas entre particulares (tipo Airbnb de garajes), pero compite en la solución de "encontrar sitio".
4. **Telpark / ElParking / EasyPark:** Dominan el pago de la zona azul. Aunque su función principal es el pago, cada vez integran más funciones de "disponibilidad" basadas en datos estadísticos, lo que las convierte en competencia fuerte porque el usuario ya las tiene instaladas para pagar.
5. **Google Maps / Waze:** Ofrecen predicciones de dificultad de aparcamiento basadas en datos históricos y movimiento de usuarios, aunque no suelen dar huecos específicos en tiempo real con tanta precisión como una app dedicada.

## Problemática y Desafíos
Identificamos varios problemas críticos que este tipo de aplicaciones suelen enfrentar:

*   **Masa Crítica (El problema del huevo y la gallina):** Para que la app sea útil, necesitas que haya plazas reportadas. Para que se reporten plazas, necesitas usuarios activos. Si un usuario entra y no ve plazas, desinstala la app. Conseguir esa densidad de usuarios inicial es el reto más grande.
*   **Veracidad y Fiabilidad:** Depender de que el usuario reporte manualmente que "se va" es arriesgado. A menudo a la gente se le olvida. Además, en el tiempo que pasa entre el aviso y la llegada de otro usuario, la plaza puede haber sido ocupada por alguien que no usa la app.
*   **Seguridad y Distracción:** Usar el móvil para reportar o buscar mientras se conduce o maniobra puede ser peligroso y conllevar multas. La interfaz debe ser extremadamente simple o automática (manos libres/voz).
*   **Saturación en Zonas Calientes:** En centros de ciudades, un hueco dura segundos. La notificación de "hueco libre" puede generar frustración si al llegar ya está ocupado.

## Modelo de Negocio Propuesto

### Versión Gratuita
*   Acceso al mapa de aparcamientos y alertas básicas visuales.
*   **Monetización:** Anuncios integrados de forma no intrusiva (banners en menús, no tapando el mapa).
*   **Periodo de Prueba:** 1 mes y medio gratis con todas las funciones Premium para enganchar al usuario.

### Versión Premium (Suscripción ~3,50€/mes)
*   **Sin publicidad:** Experiencia limpia.
*   **Notificaciones Avanzadas:**
    *   Avisos proactivos de usuarios cercanos que están liberando una plaza.
    *   Notificaciones de "Alta probabilidad de aparcamiento" en la zona de destino.
*   **Prioridad:** Ver los huecos libres segundos antes que los usuarios gratuitos (ventaja competitiva).

## Funcionalidades e Ideas Adicionales
1.  **Gamificación (Sistema de Karma):** Para incentivar que la gente avise de que se va, ofrecer puntos canjeables por meses de Premium o descuentos en parquímetros/gasolineras. Si un usuario reporta huecos falsos, pierde karma.
2.  **Detección Automática:** Implementar detección por Bluetooth (desconexión del coche) o velocidad para marcar automáticamente que el usuario ha dejado el sitio, reduciendo la fricción de tener que pulsar un botón.
3.  **Integración con Pagos:** Intentar integrar el pago de la zona azul dentro de la misma app para que el usuario no tenga que cambiar a Telpark/ElParking.
4.  **Mapa de "Calor":** Mostrar zonas donde es históricamente más fácil aparcar a ciertas horas, no solo huecos en tiempo real.
