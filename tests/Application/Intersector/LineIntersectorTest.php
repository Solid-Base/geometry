<?php


use Solidbase\Geometry\Application\Intersector\LineIntersector;
use Solidbase\Geometry\Domain\Factory\LineFactory;
use Solidbase\Geometry\Domain\Line;
use Solidbase\Geometry\Domain\Point;

dataset("application-intersector-line", [LineFactory::CreateFromLineEquation(2, 6)]);

test("Calcula o ponto de intersecção", function (Line $line, Line $lineIntersection, Point $expeted) {

    $resultado = LineIntersector::Calculate($line, $lineIntersection);
    expect($resultado->isEquals($expeted))->toEqual(true, "Ponto {$expeted->x}, {$expeted->y} não é igual ao esperado {$resultado->x}, {$resultado->y}");


})->with("application-intersector-line")
->with([
    [LineFactory::CreateFromLineEquationFull(-2, -3, 6), new Point(-3 / 2, 3)],
])->group("application-intersector");
