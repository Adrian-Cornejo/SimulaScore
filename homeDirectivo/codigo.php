<?php

function generarCodigo() {
    $caracteres = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $longitud = strlen($caracteres);
    $codigo = '';
    // Generar primera parte del código
    for ($i = 0; $i < 3; $i++) {
        $codigo .= $caracteres[rand(0, $longitud - 1)];
    }
    $codigo .= '-'; // Añadir el guion
    // Generar segunda parte del código
    for ($i = 0; $i < 3; $i++) {
        $codigo .= $caracteres[rand(0, $longitud - 1)];
    }

    return $codigo;
}

// Ejemplo de uso
//echo generarCodigo();

?>
