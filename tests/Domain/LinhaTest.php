<?php

use Solidbase\Geometry\Domain\Line;
use Solidbase\Geometry\Domain\Point;
use Solidbase\Geometry\Domain\Vector;

dataset("linha-base", [new Line(new Point(1, 3), Vector::CreateBaseX(), 5)]);

test("Parelo", function (Line $linha, Line $linhaComparacao, bool $esperado) {
    $resultado = $linha->isParallel($linhaComparacao);
    expect($resultado)->toEqual($esperado);
})->with("linha-base")
->with([
    [new Line(new Point(0, 2), Vector::CreateBaseX(), 1),true],
    [new Line(new Point(0, 2), Vector::CreateBaseY(), 1),false]
]);
