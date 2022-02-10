<?php

declare(strict_types=1);

namespace Solidbase\Geometria\Aplicacao\Interseccao;

use Solidbase\Geometria\Dominio\Fabrica\VetorFabrica;
use Solidbase\Geometria\Dominio\Linha;
use Solidbase\Geometria\Dominio\Ponto;
use SolidBase\Matematica\Aritimetica\Numero;

class InterseccaoLinhas
{
    private function __construct()
    {
    }

    public static function executar(Linha $linha1, Linha $linha2): ?Ponto
    {
        if ($linha1->eParelo($linha2)) {
            return null;
        }
        if (!$linha1->eCoplanar($linha2)) {
            return null;
        }
        if ($linha1->origem->eIgual($linha2->origem)) {
            return $linha1->origem;
        }
        if ($linha1->final->eIgual($linha2->final)) {
            return $linha1->final;
        }
        [$s,] = self::calcularTS($linha1, $linha2);

        return $linha1->origem->somar($linha1->direcao->escalar($s));
    }

    /**
     * @return Numero[]
     */
    private static function calcularTS(Linha $linha1, Linha $linha2): array
    {
        $k = $linha1->origem;
        $l = $linha1->pontoRetaComprimento(1);
        $m = $linha2->origem;
        $n = $linha2->pontoRetaComprimento(1);

        $diretorS = VetorFabrica::apartirDoisPonto($k, $l);
        $diretorR = VetorFabrica::apartirDoisPonto($n, $m);

        $determinante = $diretorR->produtoVetorial($diretorS);

        $diretorMk = VetorFabrica::apartirDoisPonto($k, $m);
        $vetorialRMk = $diretorR->produtoVetorial($diretorMk);
        $vetorialSMk = $diretorS->produtoVetorial($diretorMk);
        if ((!self::retaPertenceOx($linha1) || !self::retaPertenceOx($linha2))
        && (!self::retaPertenceOy($linha1) || !self::retaPertenceOy($linha2)) && !eZero($determinante->z)) {
            $s = dividir($vetorialRMk->z, $determinante->z);
            $t = dividir($vetorialSMk->z, $determinante->z);

            return [$s, $t];
        }
        if ((!self::retaPertenceOx($linha1) || !self::retaPertenceOx($linha2))
        && (!self::retaPertenceOz($linha1) || !self::retaPertenceOz($linha2)) && !eZero($determinante->y)) {
            $s = dividir($vetorialRMk->y, $determinante->y);
            $t = dividir($vetorialSMk->y, $determinante->y);

            return [$s, $t];
        }
        $s = dividir($vetorialRMk->x, $determinante->x);
        $t = dividir($vetorialSMk->x, $determinante->x);

        return [$s, $t];
    }

    private static function retaPertenceOx(Linha $linha): bool
    {
        $direcao = $linha->direcao->vetorUnitario();

        return eIgual($direcao->x, 1);
    }

    private static function retaPertenceOy(Linha $linha): bool
    {
        $direcao = $linha->direcao->vetorUnitario();

        return eIgual($direcao->y, 1);
    }

    private static function retaPertenceOz(Linha $linha): bool
    {
        $direcao = $linha->direcao->vetorUnitario();

        return eIgual($direcao->z, 1);
    }
}
