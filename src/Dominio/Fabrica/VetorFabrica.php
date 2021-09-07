<?php

declare(strict_types=1);

namespace Solidbase\Geometria\Dominio\Fabrica;

use Solidbase\Geometria\Dominio\Ponto;
use Solidbase\Geometria\Dominio\Vetor;

final class VetorFabrica
{
    public static function apartirPonto(Ponto $ponto): Vetor
    {
        return new Vetor($ponto->x, $ponto->y, $ponto->z);
    }

    public static function apartirDoisPonto(Ponto $ponto1, Ponto $ponto2): Vetor
    {
        $ponto = $ponto2->subtrair($ponto1);

        return self::apartirPonto($ponto);
    }

    public static function BaseX(): Vetor
    {
        return new Vetor(1, 0, 0);
    }

    public static function BaseY(): Vetor
    {
        return new Vetor(0, 1, 0);
    }

    public static function BaseZ(): Vetor
    {
        return new Vetor(0, 0, 1);
    }
}
