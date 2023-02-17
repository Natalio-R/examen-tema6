<?php

namespace dwesgram\controlador;

use dwesgram\controlador\Controlador;
use dwesgram\modelo\EstadisticasBd;
use dwesgram\modelo\Estadisticas;
use dwesgram\modelo\Entrada;

class EstadisticasControlador extends Controlador
{
    public function mejorEntrada(): array
    {
        $this->vista = 'estadisticas/lista';
        return EstadisticasBd::getEstadisticas();
    }
}
