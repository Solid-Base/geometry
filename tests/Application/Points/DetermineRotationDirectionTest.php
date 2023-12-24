<?php

use Solidbase\Geometry\Application\Circle\ConvertArcToPolygon;
use Solidbase\Geometry\Application\Circle\ConvertCircleToPolygon;
use Solidbase\Geometry\Application\Points\DetermineRotationDirection;
use Solidbase\Geometry\Application\Points\RotationDirectionEnum;
use Solidbase\Geometry\Domain\Arc;
use Solidbase\Geometry\Domain\Circle;
use Solidbase\Geometry\Domain\Factory\CircleArchFactory;
use Solidbase\Geometry\Domain\Point;

dataset("point-application", [new Point(-2, 0), new Point(0, 2), new Point(2, 0)]);

test("Checa o sentido da rotação dos pontos", function (array $points, $expeted) {

    $resultado = DetermineRotationDirection::execute($points[0], $points[1], $points[2]);
    expect($resultado)->toEqual($expeted);
})
->with([
    [[new Point(-2, 0), new Point(0, 2), new Point(2, 0)],RotationDirectionEnum::Clockwise],
    [[new Point(2, 0), new Point(0, 2),new Point(-2, 0)],RotationDirectionEnum::Counterclockwise],
    [[new Point(0, 0), new Point(4, 4), new Point(1, 2)],RotationDirectionEnum::Counterclockwise],
    [[new Point(0, 0), new Point(4, 4), new Point(1, 1)],RotationDirectionEnum::Collinear],
])->group("application-point");
