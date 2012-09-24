<?php

namespace Liip\Monitor\Check;

use Liip\Monitor\Check\Check;
use Liip\Monitor\Exception\CheckFailedException;
use Liip\Monitor\Result\CheckResult;

class ProcessActiveCheck extends Check
{
    /**
     * @var string
     */
    private $command;

    public function __construct($command)
    {
        $this->command = $command;
    }

    /**
     * @see Liip\MonitorBundle\Check\CheckInterface::check()
     */
    public function check()
    {
        try {
            exec('ps -ef | grep ' . escapeshellarg($this->command) . ' | grep -v grep', $output, $return);
            if ($return == 1) {
                throw new CheckFailedException(sprintf('There is no process running containing "%s"', $this->command));
            }
            $result = $this->buildResult('OK', CheckResult::OK);
        } catch (\Exception $e) {
            $result = $this->buildResult($e->getMessage(), CheckResult::CRITICAL);
        }

        return $result;
    }

    /**
     * @see Liip\MonitorBundle\Check\Check::getName()
     */
    public function getName()
    {
        return 'Process Active: ' . $this->command;
    }
}
