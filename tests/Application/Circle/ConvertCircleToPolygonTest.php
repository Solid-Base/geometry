<?php

use Solidbase\Geometry\Application\Circle\ConvertCircleToPolygon;
use Solidbase\Geometry\Domain\Circle;
use Solidbase\Geometry\Domain\Point;

dataset("circle-base", [new Circle(new Point(), 2)]);

test("Converte circulo em poligono", function (Circle $circle, int $subdivision, array $expeted) {

    $resultado = ConvertCircleToPolygon::execute($circle, $subdivision);

    foreach($expeted as $key => $e) {

        $p = $resultado->getPoints()->get($key);
        expect($p->x)->toEqual($e->x);
        expect($p->y)->toEqual($e->y);
    }
})->with("circle-base")
->with([
    [2,[new Point(2, 0), new Point(-2, 0)]],
    [4,[new Point(2, 0),new Point(0, 2), new Point(-2, 0),new Point(0, -2)]]
]);
