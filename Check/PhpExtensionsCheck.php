<?php

namespace Liip\MonitorExtraBundle\Check;

use Liip\MonitorBundle\Check\Check;
use Liip\MonitorBundle\Exception\CheckFailedException;
use Liip\MonitorBundle\Result\CheckResult;

class PhpExtensionsCheck extends Check
{
    protected $extensions;

    /**
     * @param array $extensions List of extensions names you want to test availability
     */
    public function __construct($extensions)
    {
        $this->extensions = $extensions;
    }

    public function check()
    {
        try {
            foreach ($this->extensions as $extension) {
                if (!extension_loaded($extension)) {
                    throw new CheckFailedException(sprintf('Extension %s not loaded', $extension));
                }
            }
            return $this->buildResult('OK', CheckResult::SUCCESS);
        } catch (\Exception $e) {
            return $this->buildResult(sprintf('KO - %s', $e->getMessage()), CheckResult::FAILURE);
        }
    }

    public function getName()
    {
        return "PHP Extensions Health Check";
    }
}