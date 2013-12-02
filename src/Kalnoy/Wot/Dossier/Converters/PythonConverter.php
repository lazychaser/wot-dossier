<?php namespace Kalnoy\Wot\Dossier\Converters;

use Kalnoy\Wot\Dossier\Stats\Battle as BattleStats;
use Kalnoy\Wot\Dossier\Stats\Tank as TankStats;
use Kalnoy\Wot\Dossier\Stats\TankBattles;
use Kalnoy\Wot\Dossier\Tanks;
use Kalnoy\Wot\Dossier\TankInfo;
use Kalnoy\Wot\Dossier\Dossier;
use Kalnoy\Wot\Dossier\Collection;

class PythonConverter implements ConverterInterface {

    /**
     * The path to python script.
     *
     * @var  string
     */
    protected $tool;

    /**
     * Tanks repository.
     *
     * @var  Kalnoy\Wot\Dossier\Tanks
     */
    protected $tanks;

    public function __construct($tool, $tanks)
    {
        $this->tool = $tool;
        $this->tanks = $tanks;
    }

    /**
     * Convert dossier file into Dossier object.
     *
     * @param   string  $filename
     *
     * @return  Dossier
     */
    public function convert($filename)
    {
        $data = $this->getDossierData($filename);

        $tanks = $this->convertAllTanks($data);

        return new Dossier($tanks);
    }

    /**
     * Convert all tanks.
     *
     * @param   stdObject      $data
     *
     * @return  array
     */
    protected function convertAllTanks($data)
    {
        $tanks = array();

        if (isset($data->tanks_v2))
        {
            $this->convertTanks($tanks, $data->tanks_v2);
        }

        if (isset($data->tanks))
        {
            $this->convertTanks($tanks, $data->tanks);
        }

        return $tanks;
    }

    /**
     * Convert tanks from given data.
     *
     * @param   Collection  $tanks
     * @param   stdObject      $data
     *
     * @return  void
     */
    protected function convertTanks(array &$tanks, $data)
    {
        foreach ($data as $id => $tankData)
        {
            try 
            {
                $tanks[] = $this->convertTank($id, $tankData);
            } 
            catch (NotExistsException $e) 
            {
                // TODO: Alert error   
            }
        }
    }

    /**
     * Convert given tank data into TankStats.
     *
     * @param   string  $id
     * @param   stdObject  $data
     *
     * @return  TankStats
     */
    protected function convertTank($id, $data)
    {
        if (!$tankInfo = $this->tanks->get($id))
        {
            throw new NotExistsException("Tank with id $id doesn't exists.");
        }
        
        $version = $data->common->basedonversion;
        $tankStats = $this->convertTankStats($tankInfo, $data);
        $battleStats = $this->convertAllStats($tankInfo->tier, $data);

        // Compute total number of battles for a tank
        foreach ($battleStats as $battle)
        {
            $tankStats->battles += $battle->battles;
            $tankStats->battles_old += $battle->battles_old;
        }

        $tankBattles = new TankBattles($version, $tankStats, $battleStats);

        return $tankBattles->normalize();
    }

    /**
     * Convert tank total stats.
     *
     * @param   [type]  $data
     *
     * @return  [type]
     */
    protected function convertTankStats(TankInfo $tankInfo, $data)
    {
        $version = $data->common->basedonversion;

        $total = new TankStats($version, $tankInfo);

        $total->created_at      = $data->common->creationTime;
        $total->updated_at      = $data->common->updated;
        $total->last_played_at  = $data->common->lastBattleTime;
        
        $total->trees_cut = $version >= 65
            ? $data->total->treesCut
            : $data->tankdata->treesCut;

        if ($version >= 29)
        {
            $total->mileage = $version >= 65
                ? $data->total->mileage
                : $data->tankdata->mileage;
        }

        return $total;
    }

    /**
     * Convert all statistics types for a given tank.
     *
     * @param   int      $tier
     * @param   stdObject      $data
     *
     * @return  void
     */
    protected function convertAllStats($tier, $data)
    {
        $result = array();
        $ver = $data->common->basedonversion;

        foreach (BattleStats::$types as $type)
        {
            list($stats, $extra) = $this->getStats($type, $data);

            if ($stats === null) continue;

            $converted = $this->convertStats($type, $tier, $ver, $stats, $extra);

            if ($converted)
            {
                $result[] = $converted;
            }
        }

        return $result;
    }

    /**
     * Get statistics of the specified type for the specified tank data.
     *
     * @param   int  $type
     * @param   stdObject  $data
     *
     * @return  stdObject
     */
    protected function getStats($type, $data)
    {
        switch ($type)
        {
            case BattleStats::RANDOM:
                if ($data->common->basedonversion >= 65)
                {
                    $field = 'a15x15';
                    $fieldExtra = 'a15x15_2';
                }
                else
                {
                    $field = 'tankdata';
                }

                break;

            case BattleStats::CLAN:
                $field = 'clan';
                $fieldExtra = 'clan2';

                break;

            case BattleStats::COMPANY:
                $field = 'company';
                $fieldExtra = 'company2';

                break;

            case BattleStats::B7_42:
                $field = 'a7x7';

                break;
        }

        $stats = null;
        $extra = null;

        if (isset($data->$field))
        {
            $stats = $data->$field;

            if ($data->common->basedonversion >= 65 && 
                isset($fieldExtra) && isset($data->{$fieldExtra}))
            {
                $extra = $data->$fieldExtra;
            }
        }

        return array($stats, $extra);
    }

    /**
     * Convert dossier data into Stats object.
     *
     * @param   int  $type
     * @param   int  $tier
     * @param   int  $version
     * @param   stdObject  $data
     *
     * @return  Stats
     */
    public function convertStats($type, $tier, $version, $stats, $extra)
    {
        if ($stats->battlesCount == 0) return;

        $fields = array(
            'battles_old' => 'battlesCountBefore8_8',
            'wins',
            'losses',
            'survived' => 'survivedBattles',
            'won_and_survived' => 'winAndSurvived',
            'shots',
            'hits',
            'he_hits',
            'pierced_hits' => 'pierced',
            'received_hits' => 'shotsReceived',
            'received_he_hits' => 'heHitsReceived',
            'received_pierced_hits' => 'piercedReceived',
            'damage_dealt' => 'damageDealt',
            'damage_received' => 'damageReceived',
            'damage_assisted_track' => 'damageAssistedTrack',
            'damage_assisted_radio' => 'damageAssistedRadio',
            'capture_points' => 'capturePoints',
            'dropped_capture_points' => 'droppedCapturePoints',
            'spotted',
            'frags',
            'xp',
            'xp_old' => 'xpBefore8_8',
            'xp_clean' => 'originalXP',
        );

        $s = new BattleStats($version, $type, $tier, $stats->battlesCount);

        foreach ($fields as $field => $original)
        {
            if (is_numeric($field)) $field = $original;

            if (isset($stats->$original))
            {
                $s->$field = $stats->$original;
            }
            elseif ($extra !== null && isset($extra->$original))
            {
                $s->$field = $extra->$original;
            }
        }

        return $s->normalize();
    }

    /**
     * Get dossier data using python script.
     *
     * @param   string  $filename
     *
     * @return  stdClass
     */
    protected function getDossierData($filename)
    {
        $output = exec("python \"{$this->tool}\" \"$filename\" -s -k");

        if (empty($output))
        {
            throw new Exception("Failed to get dossier data.");
        }

        $data = json_decode($output);

        if ($data->header->result !== 'ok')
        {
            throw new Exception($data->header->message);
        }

        return $data;
    }
}