<?php

namespace App\Models;

/**
 * Plan é um alias/compatibilidade para a classe existente Planos
 * Mantemos a classe Planos como fonte de verdade para não alterar
 * a estrutura existente do projeto.
 */
class Plan extends Planos
{
    // Intencionalmente vazio - reutiliza comportamento de Planos
}
