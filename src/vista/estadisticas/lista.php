<?php
$estadisticas = $datosParaVista['datos'];

echo "<div class='container'>";
if (count($estadisticas) == 0) {
    echo <<<END
    <div class="alert alert-primary" role="alert">
        No hay entradas publicadas
    </div>
    END;
} else {
    foreach ($estadisticas as $estadistica) {
        $entrada = $estadistica->getEntrada()->getTexto();
        $numeroMegusta = $estadistica->getNumeroMegusta();
        //$usuarios = $estadistica->getUsuarios(['nombre']);

        echo <<<END
            <div class="col">
                <p class="card-text">{$entrada} <br>
                {$numeroMegusta} Me gusta
                </p>
                <br>
            </div>
        END;
    }
}
echo "</div>";
