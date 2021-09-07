<?php

declare(strict_types=1);

use Solidbase\Geometria\Dominio\Vetor;

include '../vendor/autoload.php';

$vetor = new Vetor(10, 0);
$vetor2 = new Vetor(-10, 0);
echo $vetor->angulo($vetor2);
