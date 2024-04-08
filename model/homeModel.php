<?php
    class homeModel{
        
        private $PDO;
        public function __construct()
        {
            require_once("../config/db.php");
            $pdo = new db();
            $this->PDO = $pdo->conexion();
        }
        //Funcion para agregar nuevo estrudiente
        public function agregarNuevoUsuario($codigoAlumno,$nombre, $apellido, $codigoProfesor, $correo, $password){
            $statement = $this->PDO->prepare("INSERT INTO alumno(codigoAlumno,nombre, apellido, codigoProfesor, correo, password) VALUES (:codigoAlumno,:nombre, :apellido, :codigoProfesor, :correo, :password)");
            
            // Vincular los parámetros a las variables
            $statement->bindParam(":codigoAlumno", $codigoAlumno);
            $statement->bindParam(":nombre", $nombre);
            $statement->bindParam(":apellido", $apellido);
            $statement->bindParam(":codigoProfesor", $codigoProfesor);
            $statement->bindParam(":correo", $correo);
            $statement->bindParam(":password", $password);
            try {
                $statement->execute();
                return true;
            } catch (PDOException $e) {
                
                return false;
            }
        }

        public function obtenerclave($correo){
            $statement = $this->PDO->prepare("SELECT password FROM alumno WHERE correo = :correo");
            $statement->bindParam(":correo",$correo);
            return ($statement->execute()) ? $statement->fetch()['password'] : false;
        }



            // Funcion para agregar los directivos
        public function agregarNuevoDirectivo($codigoDirectivo, $nombre, $apellido, $correo, $password){
            $statement = $this->PDO->prepare("INSERT INTO directivo(codigoDirectivo,nombre, apellido, correo, password) VALUES (:codigoDirectivo, :nombre, :apellido, :correo, :password)");
            
            // Vincular los parámetros a las variables
            $statement->bindParam(":codigoDirectivo", $codigoDirectivo);
            $statement->bindParam(":nombre", $nombre);
            $statement->bindParam(":apellido", $apellido);
            $statement->bindParam(":correo", $correo);
            $statement->bindParam(":password", $password);
            try {
                $statement->execute();
                return true;
            } catch (PDOException $e) {
                
                return false;
            }
        }

        public function obtenerclaveDirectivo($correo){
            $statement = $this->PDO->prepare("SELECT password FROM directivo WHERE correo = :correo");
            $statement->bindParam(":correo",$correo);
            return ($statement->execute()) ? $statement->fetch()['password'] : false;
        }


        //Maestro

        public function agregarNuevoMaestro($codigoProfesor, $codigoDirectivo, $nombre, $apellido, $correo, $password){
            $statement = $this->PDO->prepare("INSERT INTO profesor(codigoProfesor,codigoDirectivo,nombre, apellido, correo, password) VALUES (:codigoProfesor,:codigoDirectivo, :nombre, :apellido, :correo, :password)");
            
            // Vincular los parámetros a las variables
            $statement->bindParam(":codigoProfesor", $codigoProfesor);
            $statement->bindParam(":codigoDirectivo", $codigoDirectivo);
            $statement->bindParam(":nombre", $nombre);
            $statement->bindParam(":apellido", $apellido);
            $statement->bindParam(":correo", $correo);
            $statement->bindParam(":password", $password);
            try {
                $statement->execute();
                
                


                return true;
            } catch (PDOException $e) {
                
                return false;
            } 
        }

        public function obtenerclaveMaestro($correo){
            $statement = $this->PDO->prepare("SELECT password FROM profesor WHERE correo = :correo");
            $statement->bindParam(":correo",$correo);
            return ($statement->execute()) ? $statement->fetch()['password'] : false;
        }
    }

?>