<?php

use Solidbase\Geometry\Application\Circle\ConvertArcToPolygon;
use Solidbase\Geometry\Application\Circle\ConvertCircleToPolygon;
use Solidbase\Geometry\Domain\Arc;
use Solidbase\Geometry\Domain\Circle;
use Solidbase\Geometry\Domain\Factory\CircleArchFactory;
use Solidbase\Geometry\Domain\Point;

dataset("arc-base", [CircleArchFactory::CreateArcFromThreePoint(new Point(-2, 0), new Point(0, 2), new Point(2, 0))]);
$deltaEqual = 0.00000001;
test("Verifica angulo total", function (Arc $arc, float $expeted) use ($deltaEqual) {

    expect($arc->getAngleTotal())->toEqualWithDelta($expeted, $deltaEqual);
})->with([
    [CircleArchFactory::CreateArcFromThreePoint(new Point(-2, 0), new Point(0, 2), new Point(2, 0)),M_PI],
    [CircleArchFactory::CreateArcFromThreePoint(new Point(2, 0), new Point(0, 2), new Point(-2, 0)),M_PI],
    [CircleArchFactory::CreateArcFromThreePoint(new Point(2.75, 2.87), new Point(5.4, 4.6), new Point(8.3, 4.94)),sbRad(52.901611667098976)],
])->group("domain-arc");



test("Verifica angulo Inicial", function (Arc $arc, float $expeted) use ($deltaEqual) {

    expect($arc->startAngle)->toEqualWithDelta($expeted, $deltaEqual);
})->with([
    [CircleArchFactory::CreateArcFromThreePoint(new Point(-2, 0), new Point(0, 2), new Point(2, 0)),0],
    [CircleArchFactory::CreateArcFromThreePoint(new Point(2, 0), new Point(0, 2), new Point(-2, 0)),0],
    [CircleArchFactory::CreateArcFromThreePoint(new Point(2.75, 2.87), new Point(5.4, 4.6), new Point(8.3, 4.94)),sbRad(84.00335014069484885)],
])->group("domain-arc");
;


test("Verifica angulo final", function (Arc $arc, float $expeted) use ($deltaEqual) {

    expect($arc->endAngle)->toEqualWithDelta($expeted, $deltaEqual);
})->with([
    [CircleArchFactory::CreateArcFromThreePoint(new Point(-2, 0), new Point(0, 2), new Point(2, 0)),M_PI],
    [CircleArchFactory::CreateArcFromThreePoint(new Point(2, 0), new Point(0, 2), new Point(-2, 0)),M_PI],
    [CircleArchFactory::CreateArcFromThreePoint(new Point(2.75, 2.87), new Point(5.4, 4.6), new Point(8.3, 4.94)),sbRad(136.90496180779382485)]
])->group("domain-arc");
;
