/**
 * Muestra una notificación en pantalla
 * @param {string} mensaje - Texto a mostrar
 * @param {string} tipo - Tipo de notificación (error, success)
 */
function mostrarNotificacion(mensaje, tipo = 'error') {
    // Crear notificación si no existe
    let notificacion = document.getElementById('notificacion');
    if (!notificacion) {
        notificacion = document.createElement('div');
        notificacion.id = 'notificacion';
        notificacion.className = 'notificacion';
        document.body.appendChild(notificacion);
    }
    
    // Configurar contenido
    notificacion.textContent = mensaje;
    notificacion.className = `notificacion ${tipo}`;
    notificacion.style.display = 'block';
    
    // Auto-ocultar después de 5 segundos
    setTimeout(() => {
        notificacion.style.opacity = '0';
        setTimeout(() => {
            notificacion.style.display = 'none';
            notificacion.style.opacity = '1';
        }, 300);
    }, 5000);
}

// Mostrar notificaciones almacenadas al cargar la página
document.addEventListener('DOMContentLoaded', () => {
    const notificacionGuardada = sessionStorage.getItem('notificacion');
    if (notificacionGuardada) {
        const { mensaje, tipo } = JSON.parse(notificacionGuardada);
        mostrarNotificacion(mensaje, tipo);
        sessionStorage.removeItem('notificacion');
    }
});

// Opcional: Para guardar notificaciones entre páginas
function guardarNotificacion(mensaje, tipo = 'error') {
    sessionStorage.setItem('notificacion', JSON.stringify({ mensaje, tipo }));
}