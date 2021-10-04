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
    public function __construct(private Circulo $circulo, private int $numeroDivisao)
    {
        if ($numeroDivisao <= 0) {
            throw new DomainException('O número de divisão deve ser maior que 0');
        }
    }

    public function executar(): Polilinha
    {
        $angulo = 2 * M_PI / $this->numeroDivisao;
        $pontos = [];
        $raio = $this->circulo->raio;
        $centro = $this->circulo->centro;
        for ($i = 0; $i < $this->numeroDivisao; ++$i) {
            $x = $raio * cos($angulo * $i) + $centro->x;
            $y = $raio * sin($angulo * $i) + $centro->y;
            $z = $centro->z;
            $pontos[] = new PontoPoligono($x, $y, $z);
        }

        return PolilinhaFabrica::criarPolilinhaPontos($pontos);
    }
}
