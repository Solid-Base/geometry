<?php

use Solidbase\Geometry\Domain\Vector;

$deltaEqual = 0.00000001;
test("Direção", function (Vector $vetor, Vector $vetorComparacao, bool $esperado) {
    $resultado = $vetor->hasSameDirection($vetorComparacao);

    expect($resultado)->toBe($esperado);
})->with([
    [new Vector(1, 0),new Vector(-1, 0), true],
    [new Vector(1, 0),new Vector(5, 0), true],
    [new Vector(1, 0),new Vector(-1, 1), false],
    [new Vector(1, -1),new Vector(-1, 1), true],
])->group("Vetor");


test("Sentido", function (Vector $vetor, Vector $vetorComparacao, bool $esperado) {
    $resultado = $vetor->hasSameSense($vetorComparacao);

    expect($resultado)->toBe($esperado);
})->with([
    [new Vector(1, 0),new Vector(-1, 0), false],
    [new Vector(1, 0),new Vector(5, 0), true],
    [new Vector(1, 0),new Vector(-1, 1), false],
    [new Vector(1, -1),new Vector(-1, 1), false],
    [new Vector(5, 5),new Vector(1, 1), true],
])->group("Vetor");


test("Produto-Interno", function (Vector $vetor, Vector $vetorComparacao, float $esperado) {
    $resultado = $vetor->product($vetorComparacao);
    expect($resultado)->toBe($esperado);
})->with([
    [new Vector(1, 2, 3),new Vector(4, 5, 6),32.0],
    [new Vector(1, 4, -3),new Vector(-1, 2, 0),7.0]
]);


test("Módulo", function (Vector $vetor, float $esperado) use ($deltaEqual) {
    $resultado = $vetor->module();
    expect($resultado)->toEqualWithDelta($esperado, $deltaEqual);
})->with([
    [new Vector(3, 4, 0),5.0],
    [new Vector(-25, 40, 0),47.16990566]
]);

test("Escalar", function (Vector $vetor, float $escalar, Vector $esperado) {
    $resultado = $vetor->scalar($escalar);
    expect($resultado)->toEqual($esperado);
})->with([
    [new Vector(1, 2, 3),2,new Vector(2, 4, 6)],
    [new Vector(1, 4, -3),5,new Vector(5, 20, -15)]
]);
test("Unitário", function (Vector $vetor, Vector $esperado) use ($deltaEqual) {
    $resultado = $vetor->getUnitary();
    expect($resultado)->toEqualWithDelta($esperado, $deltaEqual);
})->with([
    [new Vector(-3, 4), new Vector(-3 / 5, 4 / 5)],
    [new Vector(1, -2, 3), new Vector(1 / sqrt(14), -2 / sqrt(14), 3 / sqrt(14))]
]);


test("Unitário-Exception", function (Vector $vetor) {
    $vetor->getUnitary();
})->with([
    [new Vector(0, 0, 0)],
])->throws(Exception::class, "Vetores nulos não possui vetor unitário");


test("Ângulo", function (Vector $vetor, Vector $segundo, float $esperado) use ($deltaEqual) {
    $resultado = $vetor->getAngle($segundo);
    expect($resultado)->toEqualWithDelta($esperado, $deltaEqual);
})->with([
    [new Vector(1, 1, 4), new Vector(-1, 2, 2),M_PI / 4],
    [new Vector(2, 1, -2), new Vector(4, 4, 2),1.1102423351135742],
]);

test("Ângulo-Exception", function (Vector $vetor, Vector $segundo) {
    $vetor->getAngle($segundo);
})->with([
    [new Vector(1, 1, 4), new Vector(0, 0, 0)],
    [new Vector(0, 0, 0), new Vector(4, 4, 2)],
    [new Vector(0, 0, 0), new Vector(0, 0, 0)],
])->throws(Exception::class, "Nenhum dos vetores podem ser nulos");

test("Produto-Vetorial", function (Vector $vetor, Vector $segundo, Vector $esperado) use ($deltaEqual) {
    $resultado = $vetor->crossProduct($segundo);
    expect($resultado)->toEqualWithDelta($esperado, $deltaEqual);
})->with([
    [new Vector(2, -1, 3), new Vector(5, -2, 1),new Vector(5, 13, 1)],
    [new Vector(5, 4, 3), new Vector(1, 0, 1),new Vector(4, -2, -4)],
]);


test("Produto-Misto", function (Vector $vetor, Vector $segundo, Vector $terceiro, float $esperado) use ($deltaEqual) {
    $resultado = $vetor->tripleProduct($segundo, $terceiro);
    expect($resultado)->toEqualWithDelta($esperado, $deltaEqual);
})->with([
    [new Vector(2, 3, 5), new Vector(-1, 3, 3),new Vector(4, -3, 2),27],
    [new Vector(1, 2, 3), new Vector(2, 1, 3),new Vector(1, 1, 3),-3],
]);


test("Projeção", function (Vector $vetor, Vector $segundo, Vector $esperado) use ($deltaEqual) {
    $resultado = $vetor->getProjection($segundo);
    expect($resultado)->toEqualWithDelta($esperado, $deltaEqual);
})->with([
    [new Vector(1, 1, 4), new Vector(-1, 2, 2),new Vector(0.5, 0.5, 2)]
]);
