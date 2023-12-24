<?php

use Solidbase\Geometry\Application\Intersector\LineArcIntersector;
use Solidbase\Geometry\Application\Intersector\LineCircleIntersector;
use Solidbase\Geometry\Domain\Arc;
use Solidbase\Geometry\Domain\Circle;
use Solidbase\Geometry\Domain\Factory\CircleArchFactory;
use Solidbase\Geometry\Domain\Factory\LineFactory;
use Solidbase\Geometry\Domain\Factory\VectorFactory;
use Solidbase\Geometry\Domain\Line;
use Solidbase\Geometry\Domain\Point;

dataset("application-intersector-circle", [CircleArchFactory::CreateCircleFromThreePoints(new Point(-2, 0), new Point(0, 2), new Point(2, 0))]);

test("Calcula o ponto de intersecção entre a linha e o circulo", function (Circle $circle, Line $lineIntersection, array $expeteds) {

    $resultado = LineCircleIntersector::calculate($lineIntersection, $circle);
    $numberOfPointExpecteds = count($expeteds);
    $numberOfPointResult = count($resultado);
    expect($numberOfPointResult)->toEqual($numberOfPointExpecteds, "O numero de pontos de intersecção esperado {$numberOfPointExpecteds} é diferente do resultado {$numberOfPointResult}");
    foreach($expeteds as $expected) {
        $exist = array_filter($resultado, fn(Point $p) => $p->isEquals($expected));
        expect(reset($exist) != null)->toEqual(true, "Ponto {$expected->x}, {$expected->y}  não faz parte das intersecções");
    }
})->with("application-intersector-circle")
->with([
    [LineFactory::CreateFromLineEquation(2, 2), [new Point(0, 2), new Point(-1.6, -1.2)]],
    [LineFactory::CreateFromLineEquation(1.999999999745, 4.4721359545434), [new Point(-1.7888543819542, 0.89442719109115)]],
])->group("application-intersector");
