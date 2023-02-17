<?php

namespace dwesgram\modelo;

use dwesgram\modelo\BaseDatos;
use dwesgram\modelo\Entrada;
use dwesgram\modelo\Usuario;
use dwesgram\modelo\MegustaBd;

class EntradaBd
{
    use BaseDatos;

    /**
     * Devuelve la entrada cuyo id viene como argumento.
     */
    public static function getEntrada(int $id): Entrada|null
    {
        try {
            $conexion = BaseDatos::getConexion();
            $query = <<<END
                select
                    e.id entrada_id,
                    e.texto entrada_texto,
                    e.imagen entrada_imagen,
                    u.id usuario_id,
                    u.nombre usuario_nombre,
                    u.avatar usuario_avatar
                from
                    entrada e, usuario u
                where
                    e.id=? and
                    e.autor=u.id
            END;
            $sentencia = $conexion->prepare($query);
            $sentencia->bind_param('i', $id);
            $sentencia->execute();
            $resultado = $sentencia->get_result();
            if ($resultado === false) {
                return null;
            }

            $fila = $resultado->fetch_assoc();
            $usuario = new Usuario(
                id: $fila['usuario_id'],
                nombre: $fila['usuario_nombre'],
                avatar: $fila['usuario_avatar']
            );
            $entrada = new Entrada(
                id: $fila['entrada_id'],
                texto: $fila['entrada_texto'],
                imagen: $fila['entrada_imagen'],
                usuario: $usuario,
                listaUsuariosMegusta: MegustaBd::getUsuarios($fila['entrada_id'])
            );
            return $entrada;
        } catch (\Exception $e) {
            echo $e->getMessage();
            return null;
        }
    }

    /**
     * Devuelve todas las entradas que hay ne la base de datos.
     */
    public static function getEntradas(): array
    {
        try {
            $conexion = BaseDatos::getConexion();
            $query = <<<END
                select
                    e.id entrada_id,
                    e.texto entrada_texto,
                    e.imagen entrada_imagen,
                    u.id usuario_id,
                    u.nombre usuario_nombre,
                    u.avatar usuario_avatar
                from
                    entrada e, usuario u
                where
                    e.autor=u.id
                order by
                    e.creado desc
            END;
            $resultado = $conexion->query($query);
            $entradas = [];
            while (($fila = $resultado->fetch_assoc()) !== null) {
                $usuario = new Usuario(
                    id: $fila['usuario_id'],
                    nombre: $fila['usuario_nombre'],
                    avatar: $fila['usuario_avatar']
                );
                $entrada = new Entrada(
                    id: $fila['entrada_id'],
                    texto: $fila['entrada_texto'],
                    imagen: $fila['entrada_imagen'],
                    usuario: $usuario,
                    listaUsuariosMegusta: MegustaBd::getUsuarios($fila['entrada_id'])
                );
                $entradas[] = $entrada;
            }
            return $entradas;
        } catch (\Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }

    /**
     * Inserta la entrada en la base de datos y devuelve el identificador de la
     * nueva entrada o null si no se pudo insertar.
     */
    public static function insertar(Entrada $entrada): int|null
    {
        try {
            $conexion = BaseDatos::getConexion();
            $query = <<<END
                insert into entrada (texto, imagen, autor)
                values (?, ?, ?)
            END;
            $sentencia = $conexion->prepare($query);
            $texto = substr($entrada->getTexto(), 0, 128);
            $imagen = $entrada->getImagen();
            $usuario = $entrada->getUsuario() !== null ? $entrada->getUsuario()->getId() : null;
            $sentencia->bind_param("ssi", $texto, $imagen, $usuario);
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

    public static function eliminar(int $id): bool
    {
        try {
            $conexion = BaseDatos::getConexion();
            $sentencia = $conexion->prepare("delete from entrada where id=?");
            $sentencia->bind_param("i", $id);
            return $sentencia->execute();
        } catch (\Exception $e) {
            echo $e->getMessage();
            return false;
        }
    }
}
