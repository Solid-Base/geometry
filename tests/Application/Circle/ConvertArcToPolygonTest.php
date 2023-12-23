<?php

use Solidbase\Geometry\Application\Circle\ConvertArcToPolygon;
use Solidbase\Geometry\Application\Circle\ConvertCircleToPolygon;
use Solidbase\Geometry\Domain\Arc;
use Solidbase\Geometry\Domain\Circle;
use Solidbase\Geometry\Domain\Factory\CircleArchFactory;
use Solidbase\Geometry\Domain\Point;
use Solidbase\Geometry\Domain\PointOfPolygon;

dataset("application-arc", [CircleArchFactory::CreateArcFromThreePoint(new Point(-2, 0), new Point(0, 2), new Point(2, 0))]);

test("Converte arc em poligono", function (Arc $circle, int $subdivision, array $expeted) {

    $resultado = ConvertArcToPolygon::execute($circle, $subdivision);
    $points = $resultado->getPoints();
    foreach($expeted as $point) {

        $exist = $points->findFirst(fn($_, PointOfPolygon $p) => $p->isEquals($point));

        expect($exist != null)->toEqual(true, "Ponto {$point->x}, {$point->y} não faz parte do poligono");
    }
})->with("application-arc")
->with([
    [4,[new Point(2, 0), new Point(-2, 0), new Point(-1, 1.7320508075689)]],
    [10,[new Point(2, 0), new Point(-2, 0),new Point(-1, 1.7320508075689),new Point(0.34729635533386, 1.9696155060244)]]
])->group("application-arc");
