<?php

namespace dwesgram\modelo;

use dwesgram\modelo\Entrada;

class Estadisticas
{
    public function __construct(
        private Entrada $entrada,
        private int $numeroMegusta,
        private array $usuarios
    ) {
    }

    public function getNumeroMegusta(): int
    {
        return $this->numeroMegusta;
    }

    public function getEntrada(): Entrada
    {
        return $this->entrada;
    }

    public function getUsuarios(): array
    {
        return $this->usuarios;
    }
}
