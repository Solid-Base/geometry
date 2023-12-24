<?php

use Solidbase\Geometry\Application\Intersector\LineArcIntersector;
use Solidbase\Geometry\Domain\Arc;
use Solidbase\Geometry\Domain\Factory\CircleArchFactory;
use Solidbase\Geometry\Domain\Factory\LineFactory;
use Solidbase\Geometry\Domain\Line;
use Solidbase\Geometry\Domain\Point;

dataset("application-intersector-arc", [CircleArchFactory::CreateArcFromThreePoint(new Point(-2, 0), new Point(0, 2), new Point(2, 0))]);

test("Calcula o ponto de intersecção entre a linha e o arco", function (Arc $arc, Line $lineIntersection, array $expeteds) {

    $resultado = LineArcIntersector::calculate($lineIntersection, $arc);

    expect(count($resultado))->toEqual(count($expeteds));
    foreach($expeteds as $expected) {
        $exist = array_filter($resultado, fn(Point $p) => $p->isEquals($expected));
        expect(reset($exist) != null)->toEqual(true, "Ponto {$expected->x}, {$expected->y}  não faz parte das intersecções");
    }
})->with("application-intersector-arc")
->with([
    [LineFactory::CreateFromLineEquation(2, 2), [new Point(0, 2)]],
])->group("application-intersector");
