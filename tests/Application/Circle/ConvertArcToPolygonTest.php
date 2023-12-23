<?php

use Solidbase\Geometry\Application\Circle\ConvertArcToPolygon;
use Solidbase\Geometry\Application\Circle\ConvertCircleToPolygon;
use Solidbase\Geometry\Domain\Arc;
use Solidbase\Geometry\Domain\Circle;
use Solidbase\Geometry\Domain\Factory\CircleArchFactory;
use Solidbase\Geometry\Domain\Point;

dataset("arc-base", [CircleArchFactory::CreateArcFromThreePoint(new Point(-2, 0), new Point(0, 2), new Point(2, 0))]);

test("Converte arc em poligono", function (Arc $circle, int $subdivision, array $expeted) {

    $resultado = ConvertArcToPolygon::execute($circle, $subdivision);
    $p1 = $resultado->getPoints()->get(0);
    $p2 = $resultado->getPoints()->get($subdivision - 1);

    expect($p1->x)->toEqual($expeted[0]->x);
    expect($p1->y)->toEqual($expeted[0]->y);

    expect($p2->x)->toEqual($expeted[1]->x);
    expect($p2->y)->toEqual($expeted[1]->y);
})->with("arc-base")
->with([
    [4,[new Point(-2, 0), new Point(2, 0)]],
    [10,[new Point(-2, 0), new Point(2, 0)]]
]);
