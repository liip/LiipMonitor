<?php

namespace Liip\Monitor\Check;

class Runner
{
    protected $chain;

    /**
     * @param \Liip\Monitor\Check\CheckChain $chain
     */
    public function __construct(CheckChain $chain)
    {
        $this->chain = $chain;
    }

    /**
     * @param string $checkId
     * @return \Liip\Monitor\Result\CheckResult
     */
    public function runCheckById($checkId)
    {
        return $this->runCheck($this->chain->getCheckById($checkId));
    }

    /**
     * @param \Liip\Monitor\Check\CheckInterface $checkService
     * @return \Liip\Monitor\Result\CheckResult
     */
    public function runCheck(CheckInterface $checkService)
    {
        return $checkService->check();
    }

    /**
     * @return array
     */
    public function runAllChecks()
    {
        $results = array();
        foreach ($this->chain->getChecks() as $id => $checkService) {
            $results[$id] = $this->runCheck($checkService);
        }

        return $results;
    }
}