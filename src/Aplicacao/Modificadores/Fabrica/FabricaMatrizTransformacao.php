<?php

declare(strict_types=1);

namespace Solidbase\Geometria\Aplicacao\Modificadores\Fabrica;

use Solidbase\Geometria\Dominio\Plano;
use Solidbase\Geometria\Dominio\Ponto;
use Solidbase\Geometria\Dominio\Vetor;
use SolidBase\Matematica\Algebra\FabricaMatriz;
use SolidBase\Matematica\Algebra\Matriz;
use SolidBase\Matematica\Aritimetica\Numero;

class FabricaMatrizTransformacao
{
    public static function MatrizRotacao(Vetor $eixo, float|Numero $angulo): Matriz
    {
        $angulo = numero($angulo);
        $retorno = FabricaMatriz::Nula(3)->obtenhaMatriz();
        $cos = cosseno($angulo);
        $sen = seno($angulo);
        $unitario = $eixo->VetorUnitario();
        $umCos = subtrair(numero(1, $cos->precisao), $cos);
        $retorno[0][0] = multiplicar(potencia($unitario->x, 2), $umCos)->somar($cos);
        $retorno[0][1] = multiplicar($unitario->x, $unitario->y)->multiplicar($umCos)->subtrair(multiplicar($unitario->z, $sen));
        $retorno[0][2] = multiplicar($unitario->x, $unitario->z)->multiplicar($umCos)->somar(multiplicar($unitario->y, $sen));

        $retorno[1][0] = multiplicar($unitario->x, $unitario->y)->multiplicar($umCos)->somar(multiplicar($unitario->z, $sen));
        $retorno[1][1] = potencia($unitario->y, 2)->multiplicar($umCos)->somar($cos);
        $retorno[1][2] = multiplicar($unitario->y, $unitario->z)->multiplicar($umCos)->subtrair($unitario->x, $sen);

        $retorno[2][0] = multiplicar($unitario->x, $unitario->z)->multiplicar($umCos)->subtrair(multiplicar($unitario->y, $sen));
        $retorno[2][1] = multiplicar($unitario->y, $unitario->z)->multiplicar($umCos)->somar($unitario->x, $sen);
        $retorno[2][2] = potencia($unitario->z, 2)->multiplicar($umCos)->somar($cos);

        return new Matriz($retorno);
    }

    public static function Reflexao(Plano $plano): Matriz
    {
        $normal = $plano->normal;
        $m00 = numero(1, PRECISAO_SOLIDBASE)->subtrair(multiplicar(2, potencia($normal->x, 2)));
        $m01 = numero(-2, PRECISAO_SOLIDBASE)->multiplicar(multiplicar($normal->x, $normal->y));
        $m02 = numero(-2, PRECISAO_SOLIDBASE)->multiplicar(multiplicar($normal->x, $normal->z));

        $m10 = numero(-2, PRECISAO_SOLIDBASE)->multiplicar(multiplicar($normal->x, $normal->y));
        $m11 = numero(1, PRECISAO_SOLIDBASE)->subtrair(-2, potencia($normal->y, 2));
        $m12 = numero(-2, PRECISAO_SOLIDBASE)->multiplicar(multiplicar($normal->y, $normal->z));

        $m20 = numero(-2, PRECISAO_SOLIDBASE)->multiplicar(multiplicar($normal->x, $normal->z));
        $m21 = numero(-2, PRECISAO_SOLIDBASE)->multiplicar(multiplicar($normal->y, $normal->z));
        $m22 = numero(1, PRECISAO_SOLIDBASE)->subtrair(-2, potencia($normal->z, 2));

        $matriz = [[$m00, $m01, $m02], [$m10, $m11, $m12], [$m20, $m21, $m22]];

        return new Matriz($matriz);
    }

    public static function MatrizPonto(Ponto $ponto): Matriz
    {
        $matriz = [[$ponto->x], [$ponto->y], [$ponto->z]];

        return new Matriz($matriz);
    }
}
