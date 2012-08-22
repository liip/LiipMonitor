<?php
namespace Liip\MonitorExtraBundle\Check;

use Liip\MonitorBundle\Check\Check;
use Liip\MonitorBundle\Exception\CheckFailedException;
use Liip\MonitorBundle\Result\CheckResult;

class DiscUsageCheck extends Check
{
    /**
     * Maximum disc usage in percentage
     *
     * @var int
     */
    protected $maxDiscUsage;
        
    protected $path;

    public function __construct($maxDiscUsage, $path = "/")
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
            return $this->buildResult(sprintf('Disc usage LOW - %s', $e->getMessage()), CheckResult::CRITICAL);
        }
        return $this->buildResult('OK', CheckResult::OK);
    }

    public function getName()
    {
        return "Disc Usage Health Check";
    }



}