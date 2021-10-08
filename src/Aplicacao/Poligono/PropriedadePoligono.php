<?php

declare(strict_types=1);

namespace Solidbase\Geometria\Aplicacao\Poligono;

use InvalidArgumentException;
use Solidbase\Geometria\Dominio\Fabrica\PolilinhaFabrica;
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
            default => throw new InvalidArgumentException("Propriedade inexistente: $name"),
        };
    }

    public function executar(): void
    {
        $calculoArea = new AreaPoligono($this->poligono);
        $area = $calculoArea->executar();
        $this->sentido = $area > 0 ? 1 : -1;
        $this->area = abs($area);
        $calculoCentro = new CentroPoligono($this->poligono);
        $this->centro = $calculoCentro->executar();

        $calculoMomentoInercia = new SegundoMomentoInercia($this->poligono);
        [$ix,$iy] = $calculoMomentoInercia->executar();
        $this->momentoInerciaX = $ix * $this->sentido;
        $this->momentoInerciaY = $iy * $this->sentido;

        $pontos = array_map(fn (Ponto $ponto) => $ponto->subtrair($this->centro), $this->poligono->pontos());
        $calculoMomentoInercia = new SegundoMomentoInercia(PolilinhaFabrica::criarPolilinhaPontos($pontos));
        [$ix,$iy] = $calculoMomentoInercia->executar();
        $this->momentoInerciaPrincipalX = $ix * $this->sentido;
        $this->momentoInerciaPrincipalY = $iy * $this->sentido;
    }
}
