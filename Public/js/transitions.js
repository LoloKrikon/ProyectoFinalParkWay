/**
 * Sistema de transiciones suaves entre páginas.
 * Maneja el efecto fade-in al cargar y fade-out al navegar.
 */

document.addEventListener('DOMContentLoaded', () => {
    // 1. Entrada: Al cargar, quitamos la clase de ocultamiento si existe
    // Agregamos una pequeña demora para asegurar que el CSS se ha procesado
    setTimeout(() => {
        document.body.classList.add('visible');
        // O alternativamente, buscamos el contenedor .view y le aumentamos la opacidad
        const view = document.querySelector('.view');
        if (view) {
            view.style.opacity = '1';
        }
    }, 50);

    // 2. Salida: Interceptamos clics en enlaces
    document.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', function (e) {
            const href = this.getAttribute('href');

            // Ignoramos enlaces vacíos, anclas, o externos (que abran en nueva pestaña)
            if (!href || href.startsWith('#') || this.target === '_blank' || href.startsWith('javascript:')) return;

            // Prevenimos navegación inmediata
            e.preventDefault();

            // Iniciamos transición de salida
            const view = document.querySelector('.view');
            if (view) {
                view.style.opacity = '0';
            }

            // Esperamos a que termine la animación (500ms según CSS) y navegamos
            setTimeout(() => {
                window.location.href = href;
            }, 500);
        });
    });
});
