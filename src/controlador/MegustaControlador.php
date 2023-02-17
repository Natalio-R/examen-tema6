<?php
namespace dwesgram\controlador;

use dwesgram\controlador\Controlador;
use dwesgram\modelo\Megusta;
use dwesgram\modelo\Entrada;
use dwesgram\modelo\EntradaBd;
use dwesgram\modelo\MegustaBd;

class MegustaControlador extends Controlador
{
    public function megusta(): array|Entrada
    {
        if (!$this->permitido()) {
            $this->vista = 'entrada/lista';
            return EntradaBd::getEntradas();
        }

        // Crea el objeto desde el GET
        $megusta = Megusta::nuevoMegustaDesdeGet($_GET);
        if (!$megusta->esValido()) {
            $this->vista = 'entrada/lista';
            return EntradaBd::getEntradas();
        }

        // Inserta el objeto y obtiene el identificador
        $id = MegustaBd::insertar($megusta);
        if ($id !== null) {
            $megusta->setId($id);
        }

        // Carga una vista u otra en función de la variable 'volver' en el GET
        $volver = $_GET && isset($_GET['volver']) ? htmlspecialchars($_GET['volver']) : 'lista';
        switch ($volver) {
            case 'detalle':
                $this->vista = 'entrada/detalle';
                return EntradaBd::getEntrada($megusta->getEntrada());
            default:
                $this->vista = 'entrada/lista';
                return EntradaBd::getEntradas();
        }
    }

    /**
     * Se permiten "Me Gusta" por parte de usuarios que han inciado sesión y cuya entrada
     * no es suya.
     */
    private function permitido(): bool
    {
        if ($this->denegar()) {
            return false;
        }

        $usuarioId = $_GET && isset($_GET['usuario']) ? htmlspecialchars($_GET['usuario']) : null;
        $entrada = EntradaBd::getEntrada($_GET && isset($_GET['entrada']) ? htmlspecialchars($_GET['entrada']) : null);

        if ($usuarioId == null || $entrada == null) {
            return false;
        }

        return $usuarioId != $entrada->getUsuario()->getId();
    }
}
