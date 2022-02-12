<?php

declare(strict_types=1);

namespace Solidbase\Geometria\Aplicacao\Circulo;

use DomainException;
use Solidbase\Geometria\Dominio\Circulo;
use Solidbase\Geometria\Dominio\Fabrica\PolilinhaFabrica;
use Solidbase\Geometria\Dominio\Polilinha;
use Solidbase\Geometria\Dominio\PontoPoligono;

class ConverteCirculoPoligono
{
    private function __construct()
    {
    }

    public static function executar(Circulo $circulo, int $numeroDivisao): Polilinha
    {
        if ($numeroDivisao <= 0) {
            throw new DomainException('O número de divisão deve ser maior que 0');
        }
        $angulo = M_PI * 2 / $numeroDivisao;
        $pontos = [];
        $raio = $circulo->raio;
        $centro = $circulo->centro;
        for ($i = 0; $i < $numeroDivisao; ++$i) {
            $x = $raio * cos($angulo * $i) + $centro->x;
            $y = $raio * sin($angulo * $i) + $centro->y;
            $z = $centro->z;
            $pontos[] = new PontoPoligono($x, $y, $z);
        }

        return PolilinhaFabrica::criarPolilinhaPontos($pontos);
    }
}
