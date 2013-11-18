<?php

/**
 * Format float to leave two signs after dot.
 *
 * @param   float  $value
 *
 * @return  string
 */
function ff($value)
{
    return sprintf("%.2f", $value);
}

/**
 * Format float in range [0..1] to percents.
 *
 * @param   float  $value
 *
 * @return  string
 */
function ffp($value)
{
    return ff($value * 100).'%';
}

class Profiler
{
    protected $profiles = array();

    protected $capturing = array();

    protected $count = array();

    public function begin($name)
    {
        $time = microtime(true);
        $memory = memory_get_usage();

        array_push($this->capturing, array($name, $time, $memory));

        return $this;
    }

    public function end($times = 1)
    {
        while ($times-- > 0)
        {
            if (empty($this->capturing))
            {
                throw new Exception("To many ends.");
            }

            list($name, $time, $memory) = array_pop($this->capturing);

            if (isset($this->profiles[$name]))
            {
                list($totalTime, $totalMemory, $count) = $this->profiles[$name];
            }
            else
            {
                $count = 0;
                $totalTime = 0;
                $totalMemory = 0;
            }

            ++$count;
            $totalTime += microtime(true) - $time;
            $totalMemory += memory_get_usage() - $memory;

            $this->profiles[$name] = array($totalTime, $totalMemory, $count);
        }

        return $this;
    }

    public function next($name)
    {
        $this->end()->begin($name);

        return $this;
    }

    public function render()
    {
        $v = "<pre>";

        foreach ($this->profiles as $name => $data)
        {
            list($totalTime, $totalMemory, $count) = $data;

            $avgTime = (1000 * $totalTime) / $count;
            $totalTime = 1000 * $totalTime;
            // $avgMemory = ff($totalMemory / ($count * 1000));
            
            $v .= sprintf("%15s: avg = % 7.2fms total = % 7.2f ms\n", $name, $avgTime, $totalTime);
        }

        return $v."</pre>";
    }

    public function __toString()
    {
        return $this->render();
    }
}