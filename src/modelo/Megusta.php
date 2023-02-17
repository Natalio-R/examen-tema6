<?php
namespace dwesgram\modelo;

use dwesgram\modelo\Modelo;

class Megusta extends Modelo
{
    public function __construct(
        private int|null $usuario,
        private int|null $entrada,
        private int|null $id = null
    ) {}

    public static function nuevoMegustaDesdeGet(array $get): Megusta
    {
        return new Megusta(
            usuario: $_GET && isset($_GET['usuario']) ? htmlspecialchars($_GET['usuario']) : null,
            entrada: $_GET && isset($_GET['entrada']) ? htmlspecialchars($_GET['entrada']) : null,
        );
    }

    public function getUsuario(): int|null
    {
        return $this->usuario;
    }

    public function getEntrada(): int|null
    {
        return $this->entrada;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function esValido(): bool
    {
        return count($this->getErrores()) == 0;
    }

    public function getErrores(): array
    {
        $errores = [];

        if ($this->usuario === null) {
            $errores['usuario'] = 'El usuario es obligatorio';
        }

        if ($this->entrada === null) {
            $errores['entrada'] = 'La entrada es obligatorio';
        }

        return $errores;
    }
}
