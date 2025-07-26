<?php

class Connection
{
    private $host = "localhost";
    private $nombreBD = "WAS";
    private $usuario = "root";
    private $contraseña = "";
    private $puerto = "3306";

    /*
    private $host = "localhost";
    private $nombreBD = "u761758295_WAS2";
    private $usuario = "u761758295_ADMIN";
    private $contraseña = "n@VmtRoNE0V";
    private $puerto = "3306";
    */

    public function connect()
    {
        try {
            $connection = new PDO(
                "mysql:host=$this->host;dbname=$this->nombreBD;port=$this->puerto;charset=utf8",
                $this->usuario,
                $this->contraseña
            );
            $connection->setAttribute(
                PDO::ATTR_ERRMODE,
                PDO::ERRMODE_EXCEPTION
            );
            return $connection;
        } catch (PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }
}