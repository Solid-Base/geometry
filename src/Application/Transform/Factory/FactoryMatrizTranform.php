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
        $retorno['1,1'] = $unitario->_x ** 2 * $umCos + $cos;
        $retorno['1,2'] = ($unitario->_x * $unitario->_y) * $umCos - $unitario->_z * $sen;
        $retorno['1,3'] = $unitario->_x * $unitario->_z * $umCos + $unitario->_y * $sen;

        $retorno['2,1'] = $unitario->_x * $unitario->_y * $umCos + $unitario->_z * $sen;
        $retorno['2,2'] = $unitario->_y ** 2 * $umCos + $cos;
        $retorno['2,3'] = $unitario->_y * $unitario->_z * $umCos - $unitario->_x * $sen;

        $retorno['3,1'] = $unitario->_x * $unitario->_z * $umCos - ($unitario->_y * $sen);
        $retorno['3,2'] = $unitario->_y * $unitario->_z * $umCos + ($unitario->_x * $sen);
        $retorno['3,3'] = ($unitario->_z ** 2) * ($umCos) + ($cos);

        return $retorno;
    }

    public static function CreateMatrixReflection(Plane $plano): Matriz
    {
        $normal = $plano->normal;
        $m00 = 1 - 2 * $normal->_x ** 2;
        $m01 = -2 * ($normal->_x * $normal->_y);
        $m02 = -2 * ($normal->_x * $normal->_z);

        $m10 = -2 * ($normal->_x * $normal->_y);
        $m11 = 1 - 2 * $normal->_y ** 2;
        $m12 = -2 * ($normal->_y * $normal->_z);

        $m20 = -2 * ($normal->_x * $normal->_z);
        $m21 = -2 * ($normal->_y * $normal->_z);
        $m22 = 1 - 2 * ($normal->_z ** 2);

        $matriz = [[$m00, $m01, $m02], [$m10, $m11, $m12], [$m20, $m21, $m22]];

        return new Matriz($matriz);
    }

    public static function CreateMatrizFromPoint(Point $ponto): Matriz
    {
        $matriz = [[$ponto->_x], [$ponto->_y], [$ponto->_z]];

        return new Matriz($matriz);
    }
}
