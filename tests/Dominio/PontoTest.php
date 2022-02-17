<?php

declare(strict_types=1);

namespace Tests\Dominio;

use PHPUnit\Framework\TestCase;
use Solidbase\Geometria\Dominio\Enum\QuadranteEnum;
use Solidbase\Geometria\Dominio\Ponto;
use Tests\PontoEIgual;

/**
 * @internal
 * @coversNothing
 */
final class PontoTest extends TestCase
{
    /**
     * @psalm-param Ponto $p1
     * @psalm-param Ponto $p2
     * @psalm-param float $esperado
     *
     * @dataProvider pontosDistancias
     * @test
     */
    public function testDistanciaParaPonto(Ponto $p1, Ponto $p2, float $esperado): void
    {
        static::assertSame($p1->distanciaParaPonto($p2), $esperado);
    }

    public function testSomar(): void
    {
        $ponto = new Ponto();
        $ponto2 = new Ponto(3, 4);
        $ponto3 = new Ponto(0, 0, 1);
        static::assertTrue($ponto->somar($ponto2)->eIgual(new Ponto(3, 4, 0)));
        static::assertTrue($ponto2->somar($ponto3)->eIgual(new Ponto(3, 4, 1)));
    }

    public function testSubtrair(): void
    {
        $ponto = new Ponto();
        $ponto2 = new Ponto(3, 4);
        $ponto3 = new Ponto(0, 0, 1);
        static::assertTrue($ponto->subtrair($ponto2)->eIgual(new Ponto(-3, -4), $ponto->subtrair($ponto2)));
        static::assertTrue($ponto2->subtrair($ponto3)->eIgual(new Ponto(3, 4, -1)));
    }

    /**
     * @psalm-param Ponto $p1
     * @psalm-param Ponto $p2
     * @psalm-param Ponto $esperado
     *
     * @dataProvider pontosPontoMedio
     * @test
     */
    public function testPontoMedio(Ponto $p1, Ponto $p2, Ponto $esperado): void
    {
        static::assertTrue($p1->pontoMedio($p2)->eIgual($esperado));
    }

    /**
     * @psalm-param Ponto $ponto
     * @psalm-param QuadranteEnum $esperado
     *
     * @dataProvider pontosQuadrante
     * @test
     */
    public function testQuadrante(Ponto $ponto, QuadranteEnum $esperado): void
    {
        static::assertSame($esperado, $ponto->quadrante());
    }

    public function testEIgual(): void
    {
        $ponto1 = new Ponto(3, 4);
        $ponto2 = new Ponto(3, 4);

        static::assertThat($ponto1, new PontoEIgual($ponto2));
        static::assertTrue($ponto1->eIgual($ponto2));
        static::assertFalse($ponto1->eIgual(new Ponto()));
    }

    /**
     * @psalm-return non-empty-list<array{
     *     Ponto,
     *     Ponto,
     *     float
     * }>
     */
    public function pontosDistancias(): array
    {
        $ponto = new Ponto();
        $ponto2 = new Ponto(3, 4);
        $ponto3 = new Ponto(0, 0, 1);
        $ponto4 = new Ponto(1.5, 2.8, 0);

        return [
            [$ponto, $ponto2, 5.0],
            [$ponto, $ponto3, 1.0],
            [$ponto2, $ponto3, sqrt(26)],
            [$ponto, $ponto4, sqrt(10.09)],
        ];
    }

    /**
     * @psalm-return non-empty-list<array{
     *     Ponto,
     *     QuadranteEnum
     * }>
     */
    public function pontosQuadrante(): array
    {
        $ponto = new Ponto();
        $ponto2 = new Ponto(3, 4);
        $ponto3 = new Ponto(-20, 10, 1);
        $ponto4 = new Ponto(-20, -10, 1);
        $ponto5 = new Ponto(20, -10, 1);

        return [
            [$ponto, QuadranteEnum::PRIMEIRO],
            [$ponto2, QuadranteEnum::PRIMEIRO],
            [$ponto3, QuadranteEnum::SEGUNDO],
            [$ponto4, QuadranteEnum::TERCEIRO],
            [$ponto5, QuadranteEnum::QUARTO],
        ];
    }

    /**
     * @psalm-return non-empty-list<array{
     *     Ponto,
     *     Ponto,
     *     Ponto
     * }>
     */
    public function pontosPontoMedio(): array
    {
        $ponto = new Ponto();
        $ponto2 = new Ponto(3, 4);
        $ponto3 = new Ponto(0, 0, 1);
        $ponto4 = new Ponto(1.5, 2.8, 0);

        return [
            [$ponto, $ponto2, new Ponto(1.5, 2)],
            [$ponto, $ponto, new Ponto(0, 0)],
            [$ponto3, $ponto4, new Ponto(0.75, 1.4, 0.5)],
            [$ponto2, $ponto4, new Ponto(2.25, 3.4)],
        ];
    }
}
