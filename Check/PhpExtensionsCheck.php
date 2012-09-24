<?php

namespace Liip\Monitor\Check;

use Liip\Monitor\Check\Check;
use Liip\Monitor\Exception\CheckFailedException;
use Liip\Monitor\Result\CheckResult;

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

    /**
     * @see Liip\MonitorBundle\Check\CheckInterface::check()
     */
    public function check()
    {
        try {
            foreach ($this->extensions as $extension) {
                if (!extension_loaded($extension)) {
                    throw new CheckFailedException(sprintf('Extension %s not loaded', $extension));
                }
            }
            return $this->buildResult('OK', CheckResult::OK);
        } catch (\Exception $e) {
            return $this->buildResult(sprintf('KO - %s', $e->getMessage()), CheckResult::CRITICAL);
        }
    }

    /**
     * @see Liip\MonitorBundle\Check\CheckInterface::getName()
     */
    public function getName()
    {
        return "PHP Extensions Health Check";
    }
}
