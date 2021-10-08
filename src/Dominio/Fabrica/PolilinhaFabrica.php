<?php

declare(strict_types=1);

namespace Solidbase\Geometria\Dominio\Fabrica;

use Solidbase\Geometria\Aplicacao\Poligono\PropriedadePoligono;
use Solidbase\Geometria\Dominio\Polilinha;
use Solidbase\Geometria\Dominio\Ponto;

class PolilinhaFabrica
{
    /**
     * @param Ponto[] $pontos
     */
    public static function criarPolilinhaPontos(array $pontos): Polilinha
    {
        $polilinha = new Polilinha();
        foreach ($pontos as $ponto) {
            $polilinha->adicionarPonto($ponto);
        }

        return $polilinha;
    }

    public static function criarPoligonoRetangularDoisPontos(Ponto $p1, Ponto $p2): Polilinha
    {
        $comprimento = abs($p1->x - $p2->x);
        $largura = abs($p1->y - $p2->y);
        $centro = $p1->pontoMedio($p2);

        $retangulo = self::criarPoligonoRetangular($comprimento, $largura);
        $pontos = array_map(fn (Ponto $p) => $p->somar($centro), $retangulo->pontos());

        return self::criarPolilinhaPontos($pontos);
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
        $propriedades = new PropriedadePoligono($poligono);
        $propriedades->executar();
        $centro = $propriedades->centro;
        $pontos = array_map(fn (Ponto $ponto) => $ponto->subtrair($centro), $poligono->pontos());

        return self::criarPolilinhaPontos($pontos);
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
        $propriedades = new PropriedadePoligono($poligono);
        $propriedades->executar();
        $centro = $propriedades->centro;
        $pontos = array_map(fn (Ponto $ponto) => $ponto->subtrair($centro), $poligono->pontos());

        return self::criarPolilinhaPontos($pontos);
    }
}
