<?php

declare(strict_types=1);

namespace Solidbase\Geometria\Aplicacao\Poligono;

use InvalidArgumentException;
use Solidbase\Geometria\Dominio\Polilinha;
use Solidbase\Geometria\Dominio\Ponto;

/**
 * @property-read float $area
 * @property-read int   $sentido
 * @property-read Ponto $centro
 * @property-read float $segundoMomentoInerciaX
 * @property-read float $segundoMomentoInerciaY
 * @property-read float $momentoInerciaPrincipalX
 * @property-read float $momentoInerciaPrincipalY
 */
class PropriedadePoligono
{
    private float $area;
    private float $sentido;
    private Ponto $centro;
    private float $momentoInerciaX;
    private float $momentoInerciaY;
    private float $momentoInerciaPrincipalX;
    private float $momentoInerciaPrincipalY;

    public function __construct(private Polilinha $poligono)
    {
        $poligono->fecharPolilinha();
    }

    public function __get($name)
    {
        return match ($name) {
            'area' => $this->area,
            'sentido' => $this->sentido,
            'centro' => $this->centro,
            'segundoMomentoInerciaX' => $this->momentoInerciaX,
            'segundoMomentoInerciaY' => $this->momentoInerciaY,
            'momentoInerciaPrincipalX' => $this->momentoInerciaPrincipalX,
            'momentoInerciaPrincipalY' => $this->momentoInerciaPrincipalY,
            default => throw new InvalidArgumentException("Propriedade inexistente: {$name}"),
        };
    }

    public static function executar(Polilinha $poligono): ?DadosPoligono
    {
        $area = AreaPoligono::executar($poligono);
        if (null === $area) {
            return null;
        }
        $sentido = $area > 0 ? 1 : -1;
        $area = abs($area);
        $centro = CentroPoligono::executar($poligono);

        [$ix,$iy] = SegundoMomentoInercia::executar($poligono);

        $momentoInerciaX = $ix * $sentido;
        $momentoInerciaY = $iy * $sentido;

        if ($centro->eIgual(new Ponto())) {
            $momentoInerciaPrincipalX = $ix * $sentido;
            $momentoInerciaPrincipalY = $iy * $sentido;

            return self::montarRetorno(
                $area,
                (int) $sentido,
                $centro,
                $momentoInerciaX,
                $momentoInerciaY,
                $momentoInerciaPrincipalX,
                $momentoInerciaPrincipalY
            );
        }
        $poligono = clone $poligono;
        $poligono->mover(-$centro->x, -$centro->y);

        [$ix,$iy] = SegundoMomentoInercia::executar($poligono);
        $momentoInerciaPrincipalX = $ix * $sentido;
        $momentoInerciaPrincipalY = $iy * $sentido;

        return self::montarRetorno(
            $area,
            (int) $sentido,
            $centro,
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
        float $momentoInerciaX,
        float $momentoInerciay,
        float $momentoInerciaPrincipalX,
        float $momentoInerciaPrincipalY
    ): DadosPoligono {
        return new DadosPoligono(
            $area,
            $sentido,
            $centro,
            $momentoInerciaX,
            $momentoInerciay,
            $momentoInerciaPrincipalX,
            $momentoInerciaPrincipalY
        );
    }
}
