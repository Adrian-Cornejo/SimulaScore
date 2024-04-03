<?php
// Asegúrate de que el cliente haya enviado las imágenes
if (isset($_POST['graficaPromedioGeneral']) && isset($_POST['graficaMaterias'])) {
    // Recibe las imágenes en base64
    $imageData1 = $_POST['graficaPromedioGeneral'];
    $imageData2 = $_POST['graficaMaterias'];
    
    // No es necesario decodificar las imágenes si solo las vas a mostrar en el navegador
    // Pero si quieres asegurarte de que el formato sea correcto o hacer alguna validación, puedes hacerlo aquí
    
    // Ahora, simplemente imprime las imágenes en la página HTML
    echo '<h2>Gráfica del Promedio General</h2>';
    echo '<img src="' . htmlspecialchars($imageData1) . '" alt="Gráfica del Promedio General">';
    
    echo '<h2>Gráfica de Materias</h2>';
    echo '<img src="' . htmlspecialchars($imageData2) . '" alt="Gráfica de Materias">';
} else {
    echo '<p>No se han enviado datos de gráficas.</p>';
}
?>
