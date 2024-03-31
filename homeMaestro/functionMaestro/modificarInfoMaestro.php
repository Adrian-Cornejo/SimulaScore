<?php



session_start();
if (!isset($_SESSION['Maestro'])) {
  header('Location:../loginMaestro.php'); // Redirigir al login si no está logueado
  exit;
}
$errorFlag = false;
require '../../config/db.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $db = new db();
    $con = $db->conexion();

    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $codigoEscuela = $_POST['codigoEscuela'];
    $escuela = $_POST['escuela'];
    $correoMaestro= $_SESSION['Maestro'];

    

    // Inicia la transacción
   // $con->beginTransaction();
    try {
                $sqlHelp = $con->prepare("SELECT codigoDirectivo  FROM profesor WHERE correo = :correoMaestro");
                $sqlHelp->bindParam(':correoMaestro', $correoMaestro, PDO::PARAM_STR);
                $sqlHelp->execute();
                $resultadoaux = $sqlHelp->fetchAll(PDO::FETCH_ASSOC);
                $codigoDirectivo = $resultadoaux[0]['codigoDirectivo'];

                $sqlUpdate = $con->prepare("SELECT escuela, codigoEscuela  FROM directivo WHERE codigoDirectivo = :codigoDirectivo");
                $sqlUpdate->bindParam(':codigoDirectivo', $codigoDirectivo, PDO::PARAM_STR);
                $sqlUpdate->execute();
                $datosDirec = $sqlUpdate->fetchAll(PDO::FETCH_ASSOC);

                $escuela = $datosDirec[0]['escuela'];
                $codigoEscuela = $datosDirec[0]['codigoEscuela'];


             


        // Actualiza los datos del directivo
        $sqlUpdate = "UPDATE profesor SET nombre = :nombre, apellido = :apellido, codigoEscuela = :codigoEscuela, escuela = :escuela WHERE correo = :correoMaestro";
        $stmtUpdate = $con->prepare($sqlUpdate);
        $stmtUpdate->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $stmtUpdate->bindParam(':apellido', $apellido, PDO::PARAM_STR);
        $stmtUpdate->bindParam(':codigoEscuela', $codigoEscuela, PDO::PARAM_STR);
        $stmtUpdate->bindParam(':escuela', $escuela, PDO::PARAM_STR);
        $stmtUpdate->bindParam(':correoMaestro', $correoMaestro, PDO::PARAM_STR);
        $stmtUpdate->execute();

      

        if (!empty($_FILES["imagenPerfil"]["name"])) {
        // Carga de la imagen   
     //   $directorio = "uploads/";
        $archivo = $directorio . basename($_FILES["imagenPerfil"]["name"]);

        $directorioRaiz = $_SERVER['DOCUMENT_ROOT'];
        $directorioDestino = $directorioRaiz . '/Proyecto/src/img/imgPerfilesMaestro/';
        echo $archivo = $directorioDestino . basename($_FILES["imagenPerfil"]["name"]);
        $tipoArchivo = strtolower(pathinfo($archivo, PATHINFO_EXTENSION));
        $checarSiImagen = getimagesize($_FILES["imagenPerfil"]["tmp_name"]);

        
        if($checarSiImagen != false){
            $size = $_FILES["imagenPerfil"]["size"];
            if($size > 500000000    ){
                echo "El archivo tiene que ser menor a 500kb";
            }else{
                //validar tipo de imagen
                if($tipoArchivo == "jpg" || $tipoArchivo == "jpeg" || $tipoArchivo == "png"){
                    // se validó el archivo correctamente
                    if(move_uploaded_file($_FILES["imagenPerfil"]["tmp_name"], $archivo)){
                        echo "El archivo se subió correctamente";
                        // Imprime la ruta donde se guardó el archivo
                        echo "Ruta del archivo: " . $archivo;


                        $rutaRelativa = '/Proyecto/src/img/imgPerfiles/' . basename($_FILES["imagenPerfil"]["name"]); // Ruta relativa para guardar en DB
                        $sqlImage = "UPDATE profesor SET urlImagen = :urlImagen WHERE correo = :correoMaestro";
                        $stmtImage = $con->prepare($sqlImage);
                        $stmtImage->bindParam(':urlImagen', $rutaRelativa, PDO::PARAM_STR);
                        $stmtImage->bindParam(':correoMaestro', $correoMaestro, PDO::PARAM_STR);
                        $stmtImage->execute();

                        
                    }else{
                        $_SESSION['error'] = "Hubo un error en la subida del archivo";
                        $errorFlag = true; // Indicar que ocurrió un error
                       
                    }
                }else{
                    $_SESSION['error'] = "Solo se admiten archivos jpg/jpeg";
                    $errorFlag = true; // Indicar que ocurrió un error
                    
                }
            }
        }else{
            $_SESSION['error'] = "El documento no es una imagen";
            $errorFlag = true; // Indicar que ocurrió un error
            
        }
        
    }





    } catch (PDOException $e) {
        // Si ocurre un error, revierte la transacción
        $con->rollBack();
        $_SESSION['error'] = $e->getMessage();
    }
    if ($errorFlag) {
        // Ocurrió un error, redirige de vuelta a perfilDirectivo.php para mostrar el error
        header('Location: perfilMaestro.php');
    } else {
        // Todo bien, redirige al panel de control
        header('Location: ../panelControlMaestro.php');
    }
    exit;
}
?>