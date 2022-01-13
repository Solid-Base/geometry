<?php

declare(strict_types=1);

namespace Solidbase\Geometria\Dominio\Fabrica;

use Solidbase\Geometria\Dominio\Ponto;

class PontoFabrica
{
    public static function apartirArrayJson(string|array $dados): Ponto
    {
        $dados = is_string($dados) ? json_decode($dados, true) : $dados;

        return new Ponto($dados['x'], $dados['y'], $dados['z']);
    }
}
