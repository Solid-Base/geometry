<?php

declare(strict_types=1);

namespace Solidbase\Geometria\Aplicacao\Modificadores\Fabrica;

use Solidbase\Geometria\Dominio\Plano;
use Solidbase\Geometria\Dominio\Ponto;
use Solidbase\Geometria\Dominio\Vetor;
use SolidBase\Matematica\Algebra\FabricaMatriz;
use SolidBase\Matematica\Algebra\Matriz;

class FabricaMatrizTransformacao
{
    public static function MatrizRotacao(Vetor $eixo, float|int $angulo): Matriz
    {
        $retorno = FabricaMatriz::Nula(3);
        $cos = cos($angulo);
        $sen = sin($angulo);
        $unitario = $eixo->VetorUnitario();
        $umCos = 1 - $cos;
        $retorno['1,1'] = $unitario->x ** 2 * $umCos + $cos;
        $retorno['1,2'] = ($unitario->x * $unitario->y) * $umCos - $unitario->z * $sen;
        $retorno['1,3'] = $unitario->x * $unitario->z * $umCos + $unitario->y * $sen;

        $retorno['2,1'] = $unitario->x * $unitario->y * $umCos + $unitario->z * $sen;
        $retorno['2,2'] = $unitario->y ** 2 * $umCos + $cos;
        $retorno['2,3'] = $unitario->y * $unitario->z * $umCos - $unitario->x * $sen;

        $retorno['3,1'] = $unitario->x * $unitario->z * $umCos - ($unitario->y * $sen);
        $retorno['3,2'] = $unitario->y * $unitario->z * $umCos + ($unitario->x * $sen);
        $retorno['3,3'] = ($unitario->z ** 2) * ($umCos) + ($cos);

        return $retorno;
    }

    public static function Reflexao(Plano $plano): Matriz
    {
        $normal = $plano->normal;
        $m00 = 1 - 2 * $normal->x ** 2;
        $m01 = -2 * ($normal->x * $normal->y);
        $m02 = -2 * ($normal->x * $normal->z);

        $m10 = -2 * ($normal->x * $normal->y);
        $m11 = 1 - 2 * $normal->y ** 2;
        $m12 = -2 * ($normal->y * $normal->z);

        $m20 = -2 * ($normal->x * $normal->z);
        $m21 = -2 * ($normal->y * $normal->z);
        $m22 = 1 - 2 * ($normal->z ** 2);

        $matriz = [[$m00, $m01, $m02], [$m10, $m11, $m12], [$m20, $m21, $m22]];

        return new Matriz($matriz);
    }

    public static function MatrizPonto(Ponto $ponto): Matriz
    {
        $matriz = [[$ponto->x], [$ponto->y], [$ponto->z]];

        return new Matriz($matriz);
    }
}
