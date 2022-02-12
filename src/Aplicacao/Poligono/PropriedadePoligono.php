<?php

declare(strict_types=1);

namespace Solidbase\Geometria\Aplicacao\Poligono;

use Solidbase\Geometria\Dominio\Polilinha;
use Solidbase\Geometria\Dominio\Ponto;

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
        $sentido = $area > 0 ? 1 : -1;
        $area = modulo($area);
        $centro = CentroPoligono::executar($poligono);

        [$ix,$iy] = SegundoMomentoInercia::executar($poligono);

        $momentoInerciaX = ($ix * $sentido);
        $momentoInerciaY = ($iy * $sentido);

        if ($centro->eIgual(new Ponto())) {
            $momentoInerciaPrincipalX = ($ix * $sentido);
            $momentoInerciaPrincipalY = ($iy * $sentido);

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
        $poligono->mover(-$centro->x, -$centro->y);

        [$ix,$iy] = SegundoMomentoInercia::executar($poligono);
        $momentoInerciaPrincipalX = ($ix * $sentido);
        $momentoInerciaPrincipalY = ($iy * $sentido);

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
        float $area,
        int $sentido,
        Ponto $centro,
        TipoPoligonoEnum $tipo,
        float $momentoInerciaX,
        float $momentoInerciay,
        float $momentoInerciaPrincipalX,
        float $momentoInerciaPrincipalY
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
