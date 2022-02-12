<?php

declare(strict_types=1);

namespace Tests\Dominio;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Solidbase\Geometria\Dominio\Enum\QuadranteEnum;
use Solidbase\Geometria\Dominio\Ponto;

/**
 * @internal
 * @coversNothing
 */
final class PontoTest extends TestCase
{
    public function testDistanciaParaPonto(): void
    {
        $ponto = new Ponto();
        $ponto2 = new Ponto(3, 4);
        $ponto3 = new Ponto(0, 0, 1);
        static::assertSame($ponto->distanciaParaPonto($ponto2), 5.0);
        static::assertSame($ponto->distanciaParaPonto($ponto3), 1.0);
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

    public function testPontoMedio(): void
    {
        $ponto = new Ponto();
        $ponto2 = new Ponto(3, 4);
        $ponto3 = new Ponto(0, 0, 1);
        static::assertTrue($ponto->pontoMedio($ponto2)->eIgual(new Ponto(1.5, 2)));
        static::assertTrue($ponto2->pontoMedio($ponto3)->eIgual(new Ponto(1.5, 2, 0.5)));
    }

    public function testQuadrante(): void
    {
        $reflexao = new ReflectionClass(Ponto::class);
        $quadrante = $reflexao->getMethod('quadrante');
        $quadrante->setAccessible(true);
        $ponto = new Ponto();
        $ponto2 = new Ponto(3, 4);
        $ponto3 = new Ponto(-20, 10, 1);
        $ponto4 = new Ponto(-20, -10, 1);
        $ponto5 = new Ponto(20, -10, 1);

        static::assertSame(QuadranteEnum::PRIMEIRO, $quadrante->invoke($ponto));
        static::assertSame(QuadranteEnum::PRIMEIRO, $quadrante->invoke($ponto2));
        static::assertSame(QuadranteEnum::SEGUNDO, $quadrante->invoke($ponto3));
        static::assertSame(QuadranteEnum::TERCEIRO, $quadrante->invoke($ponto4));
        static::assertSame(QuadranteEnum::QUARTO, $quadrante->invoke($ponto5));
    }

    public function testEIgual(): void
    {
        $ponto1 = new Ponto(3, 4);
        $ponto2 = new Ponto(3, 4);

        static::assertTrue($ponto1->eIgual($ponto2));
        static::assertFalse($ponto1->eIgual(new Ponto()));
    }
}
