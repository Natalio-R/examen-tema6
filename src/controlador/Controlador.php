<?php
namespace dwesgram\controlador;

use dwesgram\modelo\Sesion;

abstract class Controlador
{
    protected string|null $vista = null;

    public function getVista(): string
    {
        if ($this->vista !== null) {
            return $this->vista;
        } else {
            return "error/500.php?msg=vista-no-existe";
        }
    }

    public function denegar(): bool
    {
        $sesion = new Sesion();
        if (!$sesion->haySesion()) {
            $this->vista = 'errores/403';
            return true;
        }
        return false;
    }
}
