<?php

declare(strict_types=1);

namespace Solidbase\Geometry\Application\Transform\Factory;

use Solidbase\Geometry\Domain\Plane;
use Solidbase\Geometry\Domain\Point;
use Solidbase\Geometry\Domain\Vector;
use SolidBase\Math\Algebra\FabricaMatriz;
use SolidBase\Math\Algebra\Matriz;

class FactoryMatrizTranform
{
    public static function CreateMatrizRotation(Vector $eixo, float|int $angulo): Matriz
    {
        $retorno = FabricaMatriz::Zero(3);
        $cos = cos($angulo);
        $sen = sin($angulo);
        $unitario = $eixo->getUnitary();
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

    public static function CreateMatrixReflection(Plane $plano): Matriz
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

    public static function CreateMatrizFromPoint(Point $ponto): Matriz
    {
        $matriz = [[$ponto->x], [$ponto->y], [$ponto->z]];

        return new Matriz($matriz);
    }
}
