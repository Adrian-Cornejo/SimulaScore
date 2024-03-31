<?php
require '../../config/db.php';
$db = new db();
$con = $db->conexion();



// Este bloque de PHP se incluiría en busquedaDetallesAlumno.php o un archivo similar
if (isset($_GET['codigoAlumno'])) {
    $codigoAlumno = $_GET['codigoAlumno'];

    // Asumiendo que ya tienes una conexión $con establecida con tu base de datos
    $query = $con->prepare("SELECT * FROM resultados_examen_mejoredu WHERE codigoAlumno = :codigoAlumno ORDER BY fecha ASC");
    $query->bindParam(':codigoAlumno', $codigoAlumno, PDO::PARAM_STR);
    $query->execute();
    $resultadosExamen = $query->fetchAll(PDO::FETCH_ASSOC);

    if (!empty($resultadosExamen)) {
        // Inicio de la tabla
        echo '<table class="tabla-docentes">
            <tr>
                <th>Fecha</th>
                <th>Calificacion Español</th>
                <th>Calificacion Matemáticas</th>
                <th>Calificacion FCE</th>
                <th>Calificacion</th>
               
            </tr>';

        // Bucle para cada examen
        foreach($resultadosExamen as $resultado) {
            echo "<tr class='".($resultado['puntaje_general'] < 6 ? 'puntaje-bajo' : '')."'>
                <td>".htmlspecialchars($resultado['fecha'])."</td>
                <td>".htmlspecialchars($resultado['calificacionEspanol'])."</td>
                <td>".htmlspecialchars($resultado['calificacionMatematicas'])."</td>
                <td>".htmlspecialchars($resultado['calificacionFce'])."</td>
                <td>".htmlspecialchars($resultado['puntaje_general'])."</td>
                
            </tr>";
        }

        // Cierre de la tabla
        echo '</table>';
    } else {
        echo "No se encontraron exámenes para el alumno seleccionado.";
    }
}
?>
