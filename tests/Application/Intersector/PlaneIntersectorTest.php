<?php

use Solidbase\Geometry\Application\Intersector\LineArcIntersector;
use Solidbase\Geometry\Application\Intersector\PlaneIntersector;
use Solidbase\Geometry\Domain\Arc;
use Solidbase\Geometry\Domain\Factory\CircleArchFactory;
use Solidbase\Geometry\Domain\Factory\LineFactory;
use Solidbase\Geometry\Domain\Factory\PlaneFactory;
use Solidbase\Geometry\Domain\Line;
use Solidbase\Geometry\Domain\Plane;
use Solidbase\Geometry\Domain\Point;

dataset("application-intersector-plane", [PlaneFactory::CreateFromThreePoints(new Point(z:-7), new Point(y:7 / 2), new Point(-7 / 5))]);
$deltaEqual = 0.00000001;
test("Calcula o ponto de intersecção entre dois planos", function (Plane $plane1, Plane $plane2, array $expeteds) use ($deltaEqual) {

    $resultado = PlaneIntersector::calculate($plane1, $plane2);
    $equation = $resultado->getEquation();

    expect($expeteds[0])->toEqualWithDelta($equation[0], $deltaEqual);
    expect($expeteds[1])->toEqualWithDelta($equation[1], $deltaEqual);

})->with("application-intersector-plane")
->with([
    [PlaneFactory::CreateFromThreePoints(new Point(z:-4), new Point(y:4 / 3), new Point(-4 / 3)), [-2,-3]],
])->group("application-intersector");
