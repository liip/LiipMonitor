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
            $notLoaded = array();
            foreach ($this->extensions as $extension) {
                if (!extension_loaded($extension)) {
                    $notLoaded[] = $extension;
                }
            }

            if (count($notLoaded) > 0) {
                throw new CheckFailedException(sprintf('The following extensions are not loaded: "%s"', implode('", "', $notLoaded)));
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
