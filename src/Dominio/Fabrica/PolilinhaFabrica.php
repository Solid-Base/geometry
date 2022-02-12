<?php

declare(strict_types=1);

namespace Solidbase\Geometria\Dominio\Fabrica;

use Solidbase\Geometria\Aplicacao\Poligono\PropriedadePoligono;
use Solidbase\Geometria\Aplicacao\Pontos\PontosAlinhados;
use Solidbase\Geometria\Dominio\Polilinha;
use Solidbase\Geometria\Dominio\Ponto;

class PolilinhaFabrica
{
    /**
     * @param Ponto[] $pontos
     */
    public static function criarPolilinhaPontos(array $pontos, bool $limpar = false): Polilinha
    {
        $polilinha = new Polilinha();
        if ($limpar) {
            $pontos = self::limparPontosPoligono($pontos);
        }
        foreach ($pontos as $ponto) {
            $polilinha->adicionarPonto($ponto);
        }

        return $polilinha;
    }

    public static function criarPoligonoRetangularDoisPontos(Ponto $p1, Ponto $p2): Polilinha
    {
        $comprimento = modulo(($p1->x - $p2->x));
        $largura = modulo(($p1->y - $p2->y));
        $centro = $p1->pontoMedio($p2);

        $retangulo = self::criarPoligonoRetangular($comprimento, $largura);
        $retangulo->mover($centro->x, $centro->y, $centro->z);

        return $retangulo;
    }

    public static function criarPoligonoRetangular(float $comprimento, float $largura): Polilinha
    {
        $pontos = [];
        $pontos[] = new Ponto(-$comprimento / 2, -$largura / 2);
        $pontos[] = new Ponto($comprimento / 2, -$largura / 2);
        $pontos[] = new Ponto($comprimento / 2, $largura / 2);
        $pontos[] = new Ponto(-$comprimento / 2, $largura / 2);

        return self::criarPolilinhaPontos($pontos);
    }

    public static function criarPoligonoL(float $comprimento, float $largura, float $comprimento1, float $largura1): Polilinha
    {
        //0.8, 0.5, 0.2, 0.2
        $p1 = new Ponto();
        $p2 = $p1->somar(new Ponto($comprimento, 0));
        $p3 = $p2->somar(new Ponto(0, $largura));
        $p4 = $p3->somar(new Ponto(-$comprimento1, 0));
        $p5 = $p4->subtrair(new Ponto(0, -$largura1 + $largura));
        $p6 = $p5->somar(new Ponto($comprimento1 - $comprimento, 0));
        $poligono = self::criarPolilinhaPontos([$p1, $p2, $p3, $p4, $p5, $p6]);
        $propriedades = PropriedadePoligono::executar($poligono);
        $centro = $propriedades->centro;
        $poligono->mover(-$centro->x, -$centro->y, -$centro->z);

        return $poligono;
    }

    public static function criarPoligonoU(float $comprimento, float $largura, float $deslocamento): Polilinha
    {
        $p1 = new Ponto();
        $p2 = $p1->somar(new Ponto(0, -$largura));
        $p3 = $p2->somar(new Ponto($comprimento, 0));
        $p4 = $p3->somar(new Ponto(0, $largura));
        $p5 = $p4->subtrair(new Ponto($deslocamento, 0));
        $p6 = $p5->subtrair(new Ponto(0, ($largura - $deslocamento)));
        $p7 = $p6->subtrair(new Ponto($comprimento - 2 * $deslocamento, 0));
        $p8 = $p7->somar(new Ponto(0, $largura - $deslocamento));
        $poligono = self::criarPolilinhaPontos([$p1, $p2, $p3, $p4, $p5, $p6, $p7, $p8]);
        $propriedades = PropriedadePoligono::executar($poligono);
        $centro = $propriedades->centro;
        $poligono->mover(-$centro->x, -$centro->y, -$centro->z);

        return $poligono;
    }

    public static function criarPoligonoRegularLado(Ponto $origem, int $numeroLados, float $lado, float $angulo = 0): Polilinha
    {
        $raio = self::RaioLado($lado, $numeroLados);

        return self::criarPoligonoRegular($origem, $numeroLados, true, $raio, $angulo);
    }

    public static function criarPoligonoRegular(Ponto $origem, int $numeroLados, bool $inscrito, float $raioCirculo, float $angulo = 0): Polilinha
    {
        $anguloInterno = self::anguloExternoRegular($numeroLados);
        $anguloInicial = self::anguloInicial($numeroLados);
        if (!$inscrito) {
            $raioCirculo = self::raioPoligonoRegular($raioCirculo, $numeroLados);
        }
        $pontos = [];
        for ($i = 0; $i < $numeroLados; ++$i) {
            $anguloCalculo = ($anguloInterno * $i) + ($anguloInicial);
            $x = $raioCirculo * cos($anguloCalculo);
            $y = $raioCirculo * sin($anguloCalculo);
            $pontos[] = new Ponto($x, $y);
        }
        $poligono = self::criarPolilinhaPontos($pontos);
        $poligono->fecharPolilinha();
        $poligono->mover($origem->x, $origem->y, $origem->z);
        $poligono->rotacionar($angulo);

        return $poligono;
    }

    private static function anguloInicial(int $numeroLados): float
    {
        if (0 === $numeroLados % 2) {
            return 0;
        }
        $anguloExterno = self::anguloExternoRegular($numeroLados);

        return fmod(1.5 * (M_PI + $anguloExterno), 2 * M_PI);
    }

    private static function anguloInternoRegular(int $numeroLados): float
    {
        return M_PI - 2 * M_PI / $numeroLados;
    }

    private static function anguloExternoRegular(int $numeroLados): float
    {
        return M_PI * 2 / $numeroLados;
    }

    private static function apotemaPoligonoRegular(float $raio, int $numeroLados): float
    {
        return $raio * cos(M_PI / $numeroLados);
    }

    private static function raioPoligonoRegular(float $apotema, int $numeroLados): float
    {
        return $apotema / cos(M_PI / $numeroLados);
    }

    private static function RaioLado(float $lado, int $numeroLados): float
    {
        return $lado / (2 * sin(M_PI / $numeroLados));
    }

    private static function limparPontosPoligono(array $pontos): array
    {
        if (count($pontos) < 3) {
            return $pontos;
        }
        $quantidade = count($pontos);
        for ($i = 2; $i < $quantidade; ++$i) {
            $p1 = $pontos[$i - 2];
            $p2 = $pontos[$i - 1];
            $p3 = $pontos[$i];
            if (PontosAlinhados::executar($p1, $p2, $p3)) {
                unset($pontos[$i - 1]);

                return self::limparPontosPoligono(array_values($pontos));
            }
        }

        return $pontos;
    }
}
