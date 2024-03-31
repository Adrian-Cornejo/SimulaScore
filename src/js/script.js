function showModal(codigoProfesor) {
    // Configura la acción del botón de confirmación dentro del modal
    document.getElementById('confirmBtn').onclick = function() {
        window.location.href = 'eliminarProfesor.php?codigoProfesor=' + codigoProfesor;
    };
    // Muestra el modal
    document.getElementById('confirmationModal').style.display = 'flex';
}

function hideModal() {
    // Oculta el modal
    document.getElementById('confirmationModal').style.display = 'none';
}




  
// Verifica si el mensaje de error existe
if (document.getElementById("error-message")) {
    setTimeout(function() {
        // Oculta el mensaje de error después de 5 segundos
        document.getElementById("error-message").style.display = "none";
    }, 5000); // 5000 milisegundos = 5 segundos
}