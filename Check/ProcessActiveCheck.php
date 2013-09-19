<?php

namespace Liip\Monitor\Check;

use Liip\Monitor\Check\Check;
use Liip\Monitor\Exception\CheckFailedException;
use Liip\Monitor\Result\CheckResult;

class ProcessActiveCheck extends Check
{
    /**
     * @var array
     */
    private $commands;

    public function __construct($commands)
    {
        if (!is_array($commands)) {
            $commands = array($commands);
        }
        $this->commands = $commands;
    }

    /**
     * @see Liip\MonitorBundle\Check\CheckInterface::check()
     */
    public function check()
    {
        try {
            foreach ($this->commands as $command) {
                exec('ps -ef | grep ' . escapeshellarg($command) . ' | grep -v grep', $output, $return);
                if ($return == 1) {
                    throw new CheckFailedException(sprintf('There is no process running containing "%s"', $command));
                }
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
        return 'Process Active';
    }
}
