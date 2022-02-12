<?php

declare(strict_types=1);

namespace Solidbase\Geometria\Aplicacao\Poligono;

use DomainException;
use Solidbase\Geometria\Aplicacao\Pontos\RotacaoPontoEnum;
use Solidbase\Geometria\Aplicacao\Pontos\SentidoRotacaoTresPontos;
use Solidbase\Geometria\Dominio\Fabrica\VetorFabrica;
use Solidbase\Geometria\Dominio\PontoPoligono;

class ConcordanciaPoligono
{
    public static function executar(PontoPoligono $p1, PontoPoligono $p2, PontoPoligono $p3, float|int $raio): array
    {
        $sentido = SentidoRotacaoTresPontos::executar($p1, $p2, $p3);
        if (RotacaoPontoEnum::ALINHADO == $sentido) {
            throw new DomainException('Não é possível fazer concordancia de pontos alinhados!');
        }
        $v1 = VetorFabrica::apartirDoisPonto($p2, $p1)->vetorUnitario();
        $v2 = VetorFabrica::apartirDoisPonto($p2, $p3)->vetorUnitario();
        $angulo = $v1->angulo($v2);
        $comprimento2 = ($raio / tan($angulo / 2));
        $p1Retorno = $p2->somar($v1->escalar($comprimento2));
        $p2Retorno = $p2->somar($v2->escalar($comprimento2));
        $anguloBulge = M_PI - $angulo;

        $bulge = tan($anguloBulge * 0.25) * ($sentido->value);
        $p1Retorno->informarConcordancia($bulge);

        return [$p1Retorno, $p2Retorno];
    }
}
