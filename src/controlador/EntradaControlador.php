<?php
namespace dwesgram\controlador;

use dwesgram\controlador\Controlador;
use dwesgram\modelo\EntradaBd;
use dwesgram\modelo\Entrada;
use dwesgram\utilidades\Ficheros;
use dwesgram\modelo\ComentarioBd;

class EntradaControlador extends Controlador
{
    public function lista(): array
    {
        $this->vista = 'entrada/lista';
        return EntradaBd::getEntradas();
    }

    public function detalle(): Entrada|null
    {
        $this->vista = 'entrada/detalle';
        $id = $_GET && isset($_GET['id']) ? htmlspecialchars($_GET['id']) : null;
        if ($id !== null) {
            $entrada = EntradaBd::getEntrada($id);
            if ($entrada === null) {
                return null;
            }

            $comentarios = ComentarioBd::getComentarios($id);
            foreach ($comentarios as $comentario) {
                $entrada->addComentario($comentario);
            }

            return $entrada;
        } else {
            return null;
        }
    }

    public function nuevo(): Entrada|null
    {
        if ($this->denegar()) {
            return null;
        }

        // Si no hay POST mostramos el formulario
        if (!$_POST) {
            $this->vista = 'entrada/nuevo';
            return null;
        }

        // Si hay POST creamos la entrada desde los datos que vienen por POST y en FILES
        $entrada = Entrada::nuevaEntradaDesdePost($_POST, $_FILES);

        // Si la entrada no es válida volvemos al formulario con los datos que hay en el objeto
        if (!$entrada->esValida()) {
            $this->vista = 'entrada/nuevo';
            return $entrada;
        }

        // Si la entrada es válida intentamos subir la imagen
        if ($entrada->getImagen()) {
            $resultado = Ficheros::subirImagen($_FILES['imagen'], $entrada->getImagen());

            // No se ha podido subir volvemos al formulario enviando el objeto
            if ($resultado !== null) {
                $entrada->setError('imagen', $resultado);
                $this->vista = 'entrada/nuevo';
                return $entrada;
            }
        }

        // Todo correcto: insertamos y mostramos el detalle de la entrada recién insertada
        $this->vista = 'entrada/detalle';
        $idEntrada = EntradaBd::insertar($entrada);
        $entrada->setId($idEntrada);

        return $entrada;
    }

    public function eliminar(): bool|null
    {
        if ($this->denegar()) {
            return null;
        }

        $this->vista = 'entrada/eliminado';
        if (!$_GET || !isset($_GET['id'])) {
            return false;
        }

        return EntradaBd::eliminar(htmlspecialchars($_GET['id']));
    }
}
