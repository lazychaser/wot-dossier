<?php namespace Kalnoy\Wot\Dossier\Efficiency\Formulas;

use Kalnoy\Wot\Dossier\Stats\Battle;

class Wn7 extends Base {

    public function computeForBattle(Battle $b, $isTotals)
    {
        $battles = $b->battles;

        $tier = (float)$b->tier / $battles;
        $frag = (float)$b->frags / $battles;
        $dmg  = (float)$b->damage_dealt / $battles;
        $spot = (float)$b->spotted / $battles;
        $def  = (float)$b->dropped_capture_points / $battles;
        $win  = $isTotals ? (float)$b->wins / $battles * 100 : 50;

        $v = 0;
        $v += (1240 - 1040 / pow(min($tier, 6), 0.164)) * $frag;
        $v += $dmg * 530 / (184 * exp(0.24 * $tier) + 130);
        $v += $spot * 125 * min($tier, 3) / 3;
        $v += min($def, 2.2) * 100;
        $v += (185 / (0.17 + exp(($win - 35) * -0.134)) - 500) * 0.45;
        $v -= ((5 - min($tier, 5)) * 125) /  
            (1 + exp(($tier - pow($b->battles / 220, 3 / $tier)) * 1.5));

        return round($v);
    }

    public function getId()
    {
        return "wn7";
    }
}