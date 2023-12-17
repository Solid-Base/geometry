<?php

declare(strict_types=1);

namespace Solidbase\Geometry\Domain\Factory;

use Solidbase\Geometry\Application\Polygon\PolygonPropertiesCalculator;
use Solidbase\Geometry\Application\Points\PointsAlignmentChecker;
use Solidbase\Geometry\Collection\PointCollection;
use Solidbase\Geometry\Domain\Polyline;
use Solidbase\Geometry\Domain\Point;

class PolylineFactory
{
    /**
     * @param PointCollection|Point[] $pontos
     * @param bool                  $fechado
     */
    public static function CreateFromPoints(array|PointCollection $pontos, bool $limpar = false, $fechado = false): Polyline
    {
        $pontos = PointCollection::deArray($pontos, clonar: true);
        $polilinha = new Polyline($fechado);
        if ($limpar) {
            $pontos = self::ClearPointsPolygon($pontos);
        }
        foreach ($pontos as $ponto) {
            $polilinha->addPoint($ponto);
        }

        return $polilinha;
    }

    public static function CreateRetangleFromTwoPoints(Point $p1, Point $p2): Polyline
    {
        $comprimento = sbModule(($p1->_x - $p2->_x));
        $largura = sbModule(($p1->_y - $p2->_y));
        $centro = $p1->midpoint($p2);

        $retangulo = self::CreateFromLenghtAndWidht($comprimento, $largura);
        $retangulo->move($centro->_x, $centro->_y, $centro->_z);

        return $retangulo;
    }

    public static function CreateFromLenghtAndWidht(float $lenght, float $width): Polyline
    {
        $pontos = new PointCollection();
        $pontos[] = new Point(-$lenght / 2, -$width / 2);
        $pontos[] = new Point($lenght / 2, -$width / 2);
        $pontos[] = new Point($lenght / 2, $width / 2);
        $pontos[] = new Point(-$lenght / 2, $width / 2);

        return self::CreateFromPoints($pontos, fechado: true);
    }

    public static function CreateLShapedPolygon(float $lenght, float $width, float $lenght1, float $width1): Polyline
    {
        //0.8, 0.5, 0.2, 0.2
        $p1 = new Point();
        $p2 = $p1->add(new Point($lenght, 0));
        $p3 = $p2->add(new Point(0, $width));
        $p4 = $p3->add(new Point(-$lenght1, 0));
        $p5 = $p4->difference(new Point(0, -$width1 + $width));
        $p6 = $p5->add(new Point($lenght1 - $lenght, 0));
        $poligono = self::CreateFromPoints([$p1, $p2, $p3, $p4, $p5, $p6], fechado: true);
        $propriedades = PolygonPropertiesCalculator::Calculate($poligono);
        $centro = $propriedades->center;
        $poligono->move(-$centro->_x, -$centro->_y, -$centro->_z);

        return $poligono;
    }

    public static function CreateUShapedPolygon(float $lenght, float $width, float $offset): Polyline
    {
        $p1 = new Point();
        $p2 = $p1->add(new Point(0, -$width));
        $p3 = $p2->add(new Point($lenght, 0));
        $p4 = $p3->add(new Point(0, $width));
        $p5 = $p4->difference(new Point($offset, 0));
        $p6 = $p5->difference(new Point(0, ($width - $offset)));
        $p7 = $p6->difference(new Point($lenght - 2 * $offset, 0));
        $p8 = $p7->add(new Point(0, $width - $offset));
        $poligono = self::CreateFromPoints([$p1, $p2, $p3, $p4, $p5, $p6, $p7, $p8], fechado: true);
        $propriedades = PolygonPropertiesCalculator::Calculate($poligono);
        $centro = $propriedades->center;
        $poligono->move(-$centro->_x, -$centro->_y, -$centro->_z);

        return $poligono;
    }

    public static function CreateRegularPolygonFromSideLength(Point $origem, int $numeroLados, float $lado, float $angulo = 0): Polyline
    {
        $raio = self::SideRadius($lado, $numeroLados);

        return self::CreateRegularPolygonFromCircleRadius($origem, $numeroLados, true, $raio, $angulo);
    }

    public static function CreateRegularPolygonFromCircleRadius(Point $origem, int $numeroLados, bool $inscrito, float $raioCirculo, float $angulo = 0): Polyline
    {
        $anguloInterno = self::anguloExternoRegular($numeroLados);
        $anguloInicial = self::StartAngle($numeroLados);
        if (!$inscrito) {
            $raioCirculo = self::raioPoligonoRegular($raioCirculo, $numeroLados);
        }
        $pontos = [];
        for ($i = 0; $i < $numeroLados; ++$i) {
            $anguloCalculo = ($anguloInterno * $i) + ($anguloInicial);
            $x = $raioCirculo * cos($anguloCalculo);
            $y = $raioCirculo * sin($anguloCalculo);
            $pontos[] = new Point($x, $y);
        }
        $poligono = self::CreateFromPoints($pontos, fechado: true);

        $poligono->move($origem->_x, $origem->_y, $origem->_z);
        $poligono->rotate($angulo);

        return $poligono;
    }

    public static function ClearPointsPolygon(PointCollection $pontos): PointCollection
    {
        if (count($pontos) < 3) {
            return $pontos;
        }
        $quantidade = count($pontos);
        for ($i = 2; $i < $quantidade; ++$i) {
            $p1 = $pontos[$i - 2];
            $p2 = $pontos[$i - 1];
            $p3 = $pontos[$i];
            if (PointsAlignmentChecker::Check($p1, $p2, $p3)) {
                unset($pontos[$i - 1]);
                $pontos->enumerarIndices();

                return self::ClearPointsPolygon($pontos);
            }
        }

        return $pontos;
    }

    private static function StartAngle(int $numeroLados): float
    {
        if (0 === $numeroLados % 2) {
            return 0;
        }
        $anguloExterno = self::anguloExternoRegular($numeroLados);

        return fmod(1.5 * (M_PI + $anguloExterno), 2 * M_PI);
    }

    private static function anguloInternoRegular(int $numeroLados): float
    {
        return M_PI - 2 * M_PI / $numeroLados;
    }

    private static function anguloExternoRegular(int $numeroLados): float
    {
        return M_PI * 2 / $numeroLados;
    }

    private static function apotemaPoligonoRegular(float $raio, int $numeroLados): float
    {
        return $raio * cos(M_PI / $numeroLados);
    }

    private static function raioPoligonoRegular(float $apotema, int $numeroLados): float
    {
        return $apotema / cos(M_PI / $numeroLados);
    }

    private static function SideRadius(float $lado, int $numeroLados): float
    {
        return $lado / (2 * sin(M_PI / $numeroLados));
    }
}
