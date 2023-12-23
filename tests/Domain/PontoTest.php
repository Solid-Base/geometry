<?php

use Solidbase\Geometry\Domain\Enum\QuadrantEnum;
use Solidbase\Geometry\Domain\Point;

$deltaEqual = 0.00000001;
test("Distância", function (Point $ponto, Point $segundoPonto, float $esperado) {
    $resultado = $ponto->distanceToPoint($segundoPonto);

    expect($resultado)->toBe($esperado);
})->with([
    [new Point(1, 1, 0), new Point(4, 1),3.0]
]);


test("Soma", function (Point $ponto, Point $pontoSoma, Point $esperado) use ($deltaEqual) {
    $resultado = $ponto->add($pontoSoma);

    expect($resultado)->toEqualWithDelta($esperado, $deltaEqual);
})->with([
    [new Point(1, 2, 3), new Point(0, -1, -3), new Point(1, 1, 0)],
    [new Point(-1, 2.58, 2.85), new Point(1, 1.3, 2.9), new Point(0, 3.88, 5.75)]
]);

test("Subtração", function (Point $ponto, Point $pontoSubtracao, Point $esperado) use ($deltaEqual) {
    $resultado = $ponto->difference($pontoSubtracao);

    expect($resultado)->toEqualWithDelta($esperado, $deltaEqual);
})->with([
    [new Point(1, 2, 3), new Point(0, -1, -3), new Point(1, 3, 6)],
    [new Point(-1, 2.58, 2.85), new Point(1, 1.3, 2.9), new Point(-2, 1.28, -0.05)]
]);

test("Ponto-Médio", function (Point $ponto, Point $segundoPonto, Point $esperado) use ($deltaEqual) {
    $pontoMedio = $ponto->midpoint($segundoPonto);
    expect($pontoMedio)->toEqualWithDelta($esperado, $deltaEqual);
})->with([
    [new Point(1, 2, 3), new Point(0, -1, -3), new Point(0.5, 0.5, 0)],
    [new Point(1, 2, 3), new Point(6, 2.5, 2.99), new Point(3.5, 2.25, 2.995)],
]);

test("Igualdade", function (Point $ponto, Point $segundoPonto, bool $esperado) {
    $resultado = $ponto->isEquals($segundoPonto);

    expect($resultado)->toBe($esperado);
})->with([
    [new Point(1, 2, 3), new Point(0.999999999999, 1.99999999999999, 2.999999999999999), true],
    [new Point(2, 2, 3), new Point(6, 2.5, 2.99), false],
]);


test("Quadrante", function (Point $ponto, QuadrantEnum $esperado) {
    $resultado = $ponto->getQuadrant();
    expect($resultado)->toEqual($esperado);
})->with([
    [new Point(1, 2, 0), QuadrantEnum::First],
    [new Point(1, 0, 0), QuadrantEnum::First],
    [new Point(0, 1, 0), QuadrantEnum::Second],
    [new Point(-1, 0, 0), QuadrantEnum::Third],
    [new Point(0, -1, 0), QuadrantEnum::Fourth],
    [new Point(2, -1, 0), QuadrantEnum::Fourth]
]);
