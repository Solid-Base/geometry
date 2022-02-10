<?php

declare(strict_types=1);

namespace Solidbase\Geometria\Aplicacao\Poligono;

use Solidbase\Geometria\Dominio\Polilinha;
use Solidbase\Geometria\Dominio\Ponto;
use SolidBase\Matematica\Aritimetica\Numero;

class PropriedadePoligono
{
    private function __construct(private Polilinha $poligono)
    {
    }

    public static function executar(Polilinha $poligono): ?DadosPoligono
    {
        $poligono = clone $poligono;
        $tipo = TipoPoligono::executar($poligono);
        $area = AreaPoligono::executar($poligono);
        if (null === $area) {
            return null;
        }
        $sentido = $area->eMaior(0) ? 1 : -1;
        $area = modulo($area);
        $centro = CentroPoligono::executar($poligono);

        [$ix,$iy] = SegundoMomentoInercia::executar($poligono);

        $momentoInerciaX = multiplicar($ix, $sentido);
        $momentoInerciaY = multiplicar($iy, $sentido);

        if ($centro->eIgual(new Ponto())) {
            $momentoInerciaPrincipalX = multiplicar($ix, $sentido);
            $momentoInerciaPrincipalY = multiplicar($iy, $sentido);

            return self::montarRetorno(
                $area,
                (int) $sentido,
                $centro,
                $tipo,
                $momentoInerciaX,
                $momentoInerciaY,
                $momentoInerciaPrincipalX,
                $momentoInerciaPrincipalY
            );
        }
        $poligono = clone $poligono;
        $poligono->mover(multiplicar($centro->x, -1), multiplicar($centro->y, -1));

        [$ix,$iy] = SegundoMomentoInercia::executar($poligono);
        $momentoInerciaPrincipalX = multiplicar($ix, $sentido);
        $momentoInerciaPrincipalY = multiplicar($iy, $sentido);

        return self::montarRetorno(
            $area,
            (int) $sentido,
            $centro,
            $tipo,
            $momentoInerciaX,
            $momentoInerciaY,
            $momentoInerciaPrincipalX,
            $momentoInerciaPrincipalY
        );
    }

    private static function montarRetorno(
        Numero $area,
        int $sentido,
        Ponto $centro,
        TipoPoligonoEnum $tipo,
        Numero $momentoInerciaX,
        Numero $momentoInerciay,
        Numero $momentoInerciaPrincipalX,
        Numero $momentoInerciaPrincipalY
    ): DadosPoligono {
        return new DadosPoligono(
            $area,
            $sentido,
            $tipo,
            $centro,
            $momentoInerciaX,
            $momentoInerciay,
            $momentoInerciaPrincipalX,
            $momentoInerciaPrincipalY
        );
    }
}
