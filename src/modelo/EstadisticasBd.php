<?php

namespace dwesgram\modelo;

use dwesgram\modelo\BaseDatos;
use dwesgram\modelo\Estadisticas;
use dwesgram\modelo\Usuario;
use dwesgram\modelo\UsuarioBd;
use dwesgram\modelo\EntradaBd;
use dwesgram\modelo\MegustaBd;

class EstadisticasBd
{
    use BaseDatos;

    /**
     * Devuelve todas las 3 mejores entradas que hay ne la base de datos.
     */
    public static function getEstadisticas(): array
    {
        try {
            $conexion = BaseDatos::getConexion();
            $query = <<<END
                select
                    entrada,
                    count(*) numeroMegusta
                from megusta
                group by entrada
                order by numeroMegusta desc
                limit 3
            END;
            $resultado = $conexion->query($query);
            $estadisticas = [];
            while (($fila = $resultado->fetch_assoc()) !== null) {

                $estadistica = new Estadisticas(
                    entrada: EntradaBd::getEntrada($fila['entrada']),
                    numeroMegusta: $fila['numeroMegusta'],
                    usuarios: MegustaBd::getUsuarios($fila['entrada']),

                    //listaUsuariosMegusta: MegustaBd::getUsuarios($fila['entrada'])
                );
                $estadisticas[] = $estadistica;
            }
            return $estadisticas;
        } catch (\Exception $e) {
            echo $e->getMessage();
            return [];
        }
    }
}
