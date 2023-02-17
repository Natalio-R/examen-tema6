<?php
namespace dwesgram\modelo;

use dwesgram\modelo\BaseDatos;
use dwesgram\modelo\Usuario;

class UsuarioBd
{
    use BaseDatos;

    public static function getUsuarioPorId(int $id): Usuario|null
    {
        try {
            $conexion = BaseDatos::getConexion();
            $sentencia = $conexion->prepare("select id, nombre, clave, email, avatar, registrado from usuario where id=?");
            $sentencia->bind_param("i", $id);
            $sentencia->execute();
            $resultado = $sentencia->get_result();
            
            if ($resultado === false) {
                echo "ERROR: no se ha podido obtener el usuario de la base de datos con id $id";
                return null;
            }

            $fila = $resultado->fetch_assoc();
            if ($fila === false || $fila === null) {
                echo "ERROR: no se ha podido obtener la fila de la consulta para obtener el usuario con id $id";
                return null;
            }

            return new Usuario(
                id: $fila['id'],
                nombre: $fila['nombre'],
                email: $fila['email'],
                clave: $fila['clave'],
                avatar: $fila['avatar'],
                registrado: $fila['registrado']
            );
        } catch (\Exception $e) {
            echo $e->getMessage();
            return null;
        }
    }

    public static function getUsuarioPorNombre(string $nombre): Usuario|null
    {
        try {
            $conexion = BaseDatos::getConexion();
            $sentencia = $conexion->prepare("select id, nombre, clave, email, avatar, registrado from usuario where nombre=?");
            $sentencia->bind_param("s", $nombre);
            $sentencia->execute();
            $resultado = $sentencia->get_result();
            
            if ($resultado === false) {
                echo "ERROR: no se ha podido obtener el usuario de la base de datos con nombre $nombre";
                return null;
            }

            $fila = $resultado->fetch_assoc();
            if ($fila === false || $fila === null) {
                return null;
            }

            return new Usuario(
                id: $fila['id'],
                nombre: $fila['nombre'],
                email: $fila['email'],
                clave: $fila['clave'],
                avatar: $fila['avatar'],
                registrado: $fila['registrado']
            );
        } catch (\Exception $e) {
            echo $e->getMessage();
            return null;
        }
    }

    public static function insertar(Usuario $usuario): int|null
    {
        try {
            $conexion = BaseDatos::getConexion();
            $query = <<<END
                insert into usuario (nombre, email, clave, avatar)
                values (?, ?, ?, ?)
            END;
            $sentencia = $conexion->prepare($query);
            $nombre = $usuario->getNombre();
            $email = $usuario->getEmail();
            $clave = $usuario->getClaveCifrada();
            $avatar = $usuario->getAvatar();
            $sentencia->bind_param("ssss", $nombre, $email, $clave, $avatar);
            $resultado = $sentencia->execute();
            if ($resultado) {
                return $conexion->insert_id;
            } else {
                return null;
            }
        } catch (\Exception $e) {
            echo $e->getMessage();
            return null;
        }
    }
}
