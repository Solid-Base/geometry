<?php

declare(strict_types=1);

namespace Solidbase\Geometria\Aplicacao\Elipse;

use Solidbase\Geometria\Dominio\Elipse;
use Solidbase\Geometria\Dominio\Fabrica\PolilinhaFabrica;
use Solidbase\Geometria\Dominio\Polilinha;
use Solidbase\Geometria\Dominio\Ponto;

class ConverteElipsePoligono
{
    public static function executar(Elipse $elipse, int $numDivisao): Polilinha
    {
        $pi = M_PI;
        $angulo = 2 * $pi / $numDivisao;
        $raio = $elipse->raioMaior;
        $pontos = [];
        for ($i = 0; $i < $numDivisao; ++$i) {
            $anguloI = $i * $angulo;
            $x = $raio * cos($anguloI);
            $y = self::calcularOrdenadaY($elipse, $x);
            if ($anguloI > $pi) {
                $y = $y * (-1);
            }
            $ponto = new Ponto($x, $y);
            $pontos[$i] = $ponto->Somar($elipse->centro);
        }

        $retorno = PolilinhaFabrica::criarPolilinhaPontos($pontos, fechado: true);
        $angulo = $elipse->direcao->anguloAbsoluto();
        if (!eZero($angulo)) {
            $retorno->rotacionar($angulo);
        }

        return $retorno;
    }

    private static function calcularOrdenadaY(Elipse $elipse, float $x): float
    {
        $a = $elipse->raioMaior;
        $b = $elipse->raioMenor;

        return sqrt((1 - ($x ** 2 / $a ** 2)) * ($b ** 2));
    }
}
