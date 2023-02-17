<?php
namespace dwesgram\controlador;

use dwesgram\modelo\Comentario;
use dwesgram\modelo\ComentarioBd;
use dwesgram\modelo\Entrada;
use dwesgram\modelo\EntradaBd;

class ComentarioControlador extends Controlador
{
    public function nuevo(): Entrada|null
    {
        if ($this->denegar()) {
            return null;
        }

        // Siempre volvemos a la vista del detalle de la entrada
        $this->vista = 'entrada/detalle';

        // Obtenemos datos del GET
        $idEntrada = $_GET && isset($_GET['entrada']) ? htmlspecialchars($_GET['entrada']) : null;
        $idUsuario = $_GET && isset($_GET['usuario']) ? htmlspecialchars($_GET['usuario']) : null;
        $entrada = EntradaBd::getEntrada($idEntrada);
        if ($entrada === null || $idUsuario === null) {
            return null;
        }

        // Si no hay POST no creamos nada y volvemos al detalle de la entrada
        if (!$_POST || !isset($_POST['comentario'])) {
            return $entrada;
        }

        // Cargamos los comentarios de la entrada
        foreach (ComentarioBd::getComentarios($idEntrada) as $comentario) {
            $entrada->addComentario($comentario);
        }

        // Tenemos todos los datos para intentar guardar el comentario
        $texto = htmlspecialchars(trim($_POST['comentario']));
        $comentario = new Comentario(
            comentario: $texto,
            entrada: $idEntrada,
            usuario: $idUsuario
        );
        if ($comentario->esValido()) {
            $resultado = ComentarioBd::insertar($comentario);
            if ($resultado !== null) {
                $comentario->setId($resultado);
                $entrada->addComentario($comentario);
            }
        }

        return $entrada;
    }
}
