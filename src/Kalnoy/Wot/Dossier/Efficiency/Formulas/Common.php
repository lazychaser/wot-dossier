<?php namespace Kalnoy\Wot\Dossier\Efficiency\Formulas;

use Kalnoy\Wot\Dossier\Stats\Battle;

class Common extends Base {

    public function computeForBattle(Battle $b, $isTotals)
    {
        $battles = $b->battles;

        $tier = (float)$b->tier / $battles;
        $frag = (float)$b->frags / $battles;
        $dmg  = (float)$b->damage_dealt / $battles;
        $spot = (float)$b->spotted / $battles;
        $def  = (float)$b->dropped_capture_points / $battles;
        $capt = (float)$b->capture_points / $battles;

        $v = 0;
        $v += $dmg * (10 / ($tier + 2)) * (0.21 + 3 * $tier / 100);
        $v += $frag * 250;
        $v += $spot * 150;
        $v += log($capt + 1, 1.732) * 150;
        $v += $def * 150;

        return round($v);
    }

    public function getId()
    {
        return 'eff';
    }
}