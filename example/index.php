<?php

// echo exec("python --version 2>&1");
// die;

include "../vendor/autoload.php";
include "helpers.php";

use Kalnoy\Wot\Dossier\Converters\PythonConverter;
use Kalnoy\Wot\Dossier\Tanks;
use Kalnoy\Wot\Dossier\TankInfo;
use Kalnoy\Wot\Dossier\Dossier;
use Kalnoy\Wot\Dossier\Stats\Battle as BattleStats;
use Kalnoy\Wot\Dossier\Efficiency\Formulas\Wn7 as Wn7Rating;
use Kalnoy\Wot\Dossier\Efficiency\Formulas\Common as CommonRating;
use Kalnoy\Wot\Dossier\Efficiency\Formulas\Wot as WotRating;
use Kalnoy\Wot\Dossier\Aggregated\Aggregator as DossierAggregator;

// Simple profiler to output performance info
$p = new Profiler;

$p->begin('global');

if (!$files = glob('data/*.dat')) die("No dossier files found.");

// Get the user preferred battle types
$battleTypes = array(
    BattleStats::RANDOM => 'Random battles',
    BattleStats::B7_42 => '7/42 battles',
    BattleStats::CLAN => 'Clan battles',
    BattleStats::COMPANY => 'Company battles',
);

$battleType = isset($_GET['stats']) ? $_GET['stats'] : array(BattleStats::RANDOM);

// Get the user preferred formula
$ratingTypes = array(
    'wn7' => 'Wn7',
    'common' => 'Efficiency rating',
    'wot' => 'Official',
);

$rating = isset($_GET['rating']) ? $_GET['rating'] : null;

switch ($rating)
{
    case 'common': $eff = new CommonRating(); break;
    case 'wot':    $eff = new WotRating(); break;
    default:       $eff = new Wn7Rating(); $rating = 'wn7'; break;
}

$scriptRoot = realpath('../vendor/phalynx/dossier-to-json');

// Get tanks repository that holds information about specific tanks.
$p->begin('tanks');
$tanks = Tanks::fromJson($scriptRoot.'/tanks.json');
$p->end();

$converter = new PythonConverter($scriptRoot.'/wotdc2j.py', $tanks);
$aggregator = new DossierAggregator;

$dossiers = array();
foreach ($files as $file) 
{
    $cached = __DIR__.'/data/cache/'.md5($file.Dossier::VERSION);

    if (is_file($cached) && filemtime($cached) > filemtime($file))
    {
        $dossiers[] = unserialize(file_get_contents($cached));
    }
    else
    {
        try
        {
            $p->begin('convert');

            $dossier = $converter->convert(realpath($file));
            $dossier->applyMeta($file);

            $p->end();

            file_put_contents($cached, serialize($dossier));

            $dossiers[] = $dossier;
        }
        catch (Exception $e)
        {
            echo "Error trying to load {$file}: {$e->getMessage()}";
        }
    }
}

$output = array();
foreach ($dossiers as $item) 
{
    $times = 1;

    while ($times-- > 0)
    {
        $p->begin('aggregate');

        $aggregated = $aggregator->aggregate($item, $battleType);

        $aggregated->aggregateTotals()->computeEfficiency($eff);

        // * Put tanks with no battles at the bottom
        // * Sort by last played
        $aggregated->getTanks()->sort(function ($a, $b) {

            $aBattle = $a->getBattle();
            $bBattle = $b->getBattle();
            $a = $a->getTank();
            $b = $b->getTank();

            if ($aBattle->battles == 0 && $bBattle->battles > 0) return 1;
            if ($aBattle->battles > 0 && $bBattle->battles == 0) return -1;

            if ($aBattle->battles == 0 && $bBattle->battles == 0)
            {
                return strcmp($a->getInfo()->title, $b->getInfo()->title);
            }

            if ($a->last_played_at > $b->last_played_at) return -1;

            return 1;
        });

        $p->end();
    }

    $output[] = $aggregated;
}

$symbolsByTankType = array(
    TankInfo::LITE => '▴',
    TankInfo::MEDIUM => '♦',
    TankInfo::HEAVY => '♦',
    TankInfo::TANK_DESTROYER => '▾',
    TankInfo::ARTILLERY => '◾',
);

?>

<form method="get">
    <fieldset>
        <legend>Statistics for</legend>
<?php foreach ($battleTypes as $key => $value): ?>
        <label>
            <input type="checkbox" name="stats[]" value="<?php echo $key ?>" <?php if (in_array($key, $battleType)) echo "checked" ?>>
            <?php echo $value ?>
        </label>
<?php endforeach ?>
    </fieldset>

    <fieldset>
        <legend>Rating</legend>
<?php foreach ($ratingTypes as $key => $value): ?>
        <label>
            <input type="radio" name="rating" value="<?php echo $key ?>" <?php if ($key == $rating) echo "checked" ?>>
            <?php echo $value ?>
        </label>
<?php endforeach ?>
    </fieldset>

    <button type="submit">Show</button>
</form>

<?php $p->begin('render') ?>

<?php foreach ($output as $i): ?>
    <?php $items = $i->getTanks()->getItems() ?>
    <?php if ($i->getTotals()) array_push($items, $i->getTotals()) ?>

<h1><?php echo $i->original->player ?></h1>
    
<table border="1" cellpadding="5" cellspacing="0">
    <thead>
        <tr>
            <th>Tier</th>
            <th>Tank Title</th>
            <th>Battles</th>
            <th>Avg. Mileage, m</th>
            <th>Win Rate</th>
            <th>Avg Damage</th>
            <th>Avg Spotted</th>
            <th>Hit Rate</th>
            <th><?php echo $eff->getId() ?></th>
            <th>PE <a href="#" title="Piercing efficiency">?</a></th>
            <th>AE <a href="#" title="Armor efficiency">?</a></th>
        </tr>
    </thead>

    <tbody>
    <?php foreach ($items as $item): ?>
        <?php $battle = $item->getBattle() ?>
        <?php $tank = $item->getTank() ?>
        <?php $info = $tank->getInfo() ?>
        <?php $isTotals = $item->getIsTotals() ?>

        <?php if ($isTotals): ?>
    </tbody>
    <tfoot>
        <?php endif ?>

        <tr>
            <td><?php echo $isTotals ? ff($battle->average_tier) : $info->tier ?></td>
            <td><?php echo $isTotals ? 'Totals' : $symbolsByTankType[$info->type].' '.$info->title ?></td>
        <?php if ($battle->battles == 0): ?>
            <td colspan="9">Never played</td>
        <?php else: ?>
            <td><?php echo $battle->battles ?></td>
            <td><?php echo ff($tank->average_mileage) ?></td>
            <td><?php echo ffp($battle->win_rate) ?></td>
            <td><?php echo ff($battle->average_damage) ?></td>
            <td><?php echo ff($battle->average_spotted) ?></td>
            <td><?php echo ffp($battle->hit_rate) ?></td>
            <td><?php echo $item->efficiency ?></td>
            <?php if ($battle->battles_new == 0): ?>
            <td colspan="2">n/a</td>
            <?php else: ?>
            <td><?php echo ffp($battle->piercing_efficiency) ?></td>
            <td><?php echo ffp($battle->armor_efficiency) ?></td>
            <?php endif ?>
        </tr>
        <?php endif ?>
        <?php if ($isTotals): ?>
    </tfoot>
        <?php endif ?>
    <?php endforeach ?>
</table>

<?php endforeach ?>

<?php echo $p->end(2) ?>