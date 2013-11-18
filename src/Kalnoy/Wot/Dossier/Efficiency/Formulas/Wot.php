<?php namespace Kalnoy\Wot\Dossier\Efficiency\Formulas;

use Kalnoy\Wot\Dossier\Stats\Battle;

class Wot extends Base {

    public function computeForBattle(Battle $b, $isTotals)
    {
        $battles = $b->battles;
        $battles_new = $battles - $b->battles_old;

        if ($battles_new == 0) return 0;

        $win  = (float)$b->wins / $battles;
        $surv = (float)$b->survived / $battles;
        $hit  = (float)$b->hits / $battles;
        $dmg  = (float)$b->damage_dealt / $battles;
        $xp   = (float)$b->xp_clean / $battles_new;

        $v = 0;
        $v += 3000 / (1 + exp(13 - 25 * $win));
        $v += 1300 / (1 + exp(7 - 22 * $surv));
        $v += 700 / (1 + exp(14 - 24 * $hit));
        $v += 5 * $xp * (2 / (1 + exp(-$battles_new / 500)) - 1);
        $v += $dmg;

        $c = 2 / (1 + exp(-$battles / ($isTotals ? 3000 : 50))) - 1;

        return round($v * $c);
    }

    public function getId()
    {
        return 'wotr';
    }
}