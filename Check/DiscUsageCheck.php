<?php
namespace Liip\Monitor\Check;

use Liip\Monitor\Check\Check;
use Liip\Monitor\Result\CheckResult;

class DiscUsageCheck extends Check
{
    /**
     * Maximum disc usage in percentage
     *
     * @var int
     */
    protected $maxDiscUsage;
        
    protected $path;

    public function __construct($maxDiscUsage, $path)
    {
        $this->maxDiscUsage = (int)$maxDiscUsage;
        $this->path = $path;
    }

    public function check()
    {
        $df = disk_free_space($this->path);
        $dt = disk_total_space($this->path);
        $du = $dt - $df;
        $dp = ($du / $dt) * 100;

        if ($dp >= $this->maxDiscUsage) {
            return $this->buildResult(sprintf('KO - Disc usage too high: %2d percentage.', $dp), CheckResult::CRITICAL);
        }
        return $this->buildResult('OK', CheckResult::OK);
    }

    public function getName()
    {
        return "Disc Usage Health";
    }
}