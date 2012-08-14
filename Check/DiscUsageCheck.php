<?php
namespace Liip\MonitorExtraBundle\Check;

use Liip\MonitorBundle\Check\Check;
use Liip\MonitorBundle\Exception\CheckFailedException;
use Liip\MonitorBundle\Result\CheckResult;

class DiscUsageCheck extends Check
{
    protected $maximum_disc_usage_in_percent;
    protected $path;

    public function __construct($maximum_disc_usage_in_percent, $path = "/")
    {
        $this->maximum_disc_usage_in_percent = (int)$maximum_disc_usage_in_percent;
        $this->path = $path;
    }

    public function check()
    {
       
        try{

            $df = disk_free_space($this->path);
            $dt = disk_total_space($this->path);
            $du = $dt - $df;
            $dp = ($du / $dt) * 100;


            if($dp >= $this->maximum_disc_usage_in_percent) {
                throw new CheckFailedException(sprintf('Disc usage is %2d percent. The maximum usage to consider healthy is %2d percentage.', $dp, $this->maximum_disc_usage_in_percent));
            }

                
            return $this->buildResult('OK', CheckResult::OK);
                       
            
        } catch (\Exception $e) {
            return $this->buildResult(sprintf('Disc usage LOW - %s', $e->getMessage()), CheckResult::CRITICAL);
        }
    }

    public function getName()
    {
        return "Disc Usage Health Check";
    }



}