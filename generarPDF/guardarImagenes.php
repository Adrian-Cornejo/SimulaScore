<?php
// guardarImagenes.php
if (isset($_POST['imagenes'])) {
    $imagenes = $_POST['imagenes'];
    $rutas = []; // Para almacenar las rutas de las imágenes guardadas
    
    foreach ($imagenes as $key => $imagenBase64) {
        // Extraer el contenido de la imagen
        list($tipo, $contenido) = explode(';', $imagenBase64);
        list(, $contenido)      = explode(',', $contenido);
        $contenido = base64_decode($contenido);
        
        // Generar un nombre único para la imagen
        $nombreImagen = uniqid($key . "_") . '.png';
        $rutaImagen = 'C:\xampp\tmp\imagenes' . $nombreImagen;
        
        // Guardar la imagen en el servidor
        file_put_contents($rutaImagen, $contenido);

        // Guardar la ruta de la imagen
        $rutas[] = $rutaImagen;
    }

    // Devolver las rutas de las imágenes guardadas como respuesta
    echo json_encode($rutas);
} else {
    echo "No se recibieron imágenes.";
}
?>
