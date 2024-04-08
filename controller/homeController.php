<?php
require_once("../model/homeModel.php");

class homeController {
    private $MODEL;

    public function __construct() {
        $this->MODEL = new homeModel();
    }

    // Guarda un nuevo usuario
    public function guardarUsuario($codigoAlumno, $nombre, $apellido, $codigoProfesor, $correo, $contraseña) {
        $correoLimpio = $this->limpiarcorreo($correo);
        $contraseñaEncriptada = $this->encriptarcontraseña($this->limpiarcadena($contraseña));
        return $this->MODEL->agregarNuevoUsuario($codigoAlumno, $nombre, $apellido, $codigoProfesor, $correoLimpio, $contraseñaEncriptada);
    }

    // Guarda un nuevo directivo
    public function guardarUsuarioDirectivo($codigoDirectivo, $nombre, $apellido, $correo, $contraseña) {
        $correoLimpio = $this->limpiarcorreo($correo);
        $contraseñaEncriptada = $this->encriptarcontraseña($this->limpiarcadena($contraseña));
        return $this->MODEL->agregarNuevoDirectivo($codigoDirectivo, $nombre, $apellido, $correoLimpio, $contraseñaEncriptada);
    }

    // Guarda un nuevo maestro
    public function guardarUsuarioMaestro($codigoProfesor, $codigoDirectivo, $nombre, $apellido, $correo, $contraseña) {
        $correoLimpio = $this->limpiarcorreo($correo);
        $contraseñaEncriptada = $this->encriptarcontraseña($this->limpiarcadena($contraseña));
        return $this->MODEL->agregarNuevoMaestro($codigoProfesor, $codigoDirectivo, $nombre, $apellido, $correoLimpio, $contraseñaEncriptada);
    }

    // Limpia una cadena de texto
    public function limpiarcadena($campo) {
        $campo = strip_tags($campo);
        $campo = filter_var($campo, FILTER_UNSAFE_RAW);
        $campo = htmlspecialchars($campo);
        return $campo;
    }

    // Limpia un correo electrónico
    public function limpiarcorreo($campo) {
        $campo = strip_tags($campo);
        $campo = filter_var($campo, FILTER_SANITIZE_EMAIL);
        $campo = htmlspecialchars($campo);
        return $campo;
    }

    // Encripta una contraseña
    public function encriptarcontraseña($contraseña) {
        return password_hash($contraseña, PASSWORD_DEFAULT);
    }

    // Verifica si un usuario existe
    public function verificarusuario($correo, $contraseña) {
        $keydb = $this->MODEL->obtenerclave($correo);
        return password_verify($contraseña, $keydb);
    }

    public function verificarDirectivo($correo, $contraseña) {
        $keydb = $this->MODEL->obtenerclaveDirectivo($correo);
        return password_verify($contraseña, $keydb);
    }

    public function verificarMaestro($correo, $contraseña) {
        $keydb = $this->MODEL->obtenerclaveMaestro($correo);
        return password_verify($contraseña, $keydb);
    }
}
?>
