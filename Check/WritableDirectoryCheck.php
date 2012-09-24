<?php

namespace Liip\Monitor\Check;

use Liip\Monitor\Check\Check;
use Liip\Monitor\Exception\CheckFailedException;
use Liip\Monitor\Result\CheckResult;

/**
 * Check if the given directories are writable.
 *
 * @author Cédric Girard <c.girard@lexik.fr>
 */
class WritableDirectoryCheck extends Check
{
    /**
     * @var array
     */
    protected $directories;

    /**
     * Construct.
     *
     * @param array $directories
     */
    public function __construct($directories)
    {
        $this->directories = $directories;
    }

    /**
     * @see Liip\MonitorBundle\Check\CheckInterface::check()
     */
    public function check()
    {
        try {
            $notWritable = array();

            foreach ($this->directories as $dir) {
                if (!is_writable($dir)) {
                    $notWritable[] = $dir;
                }
            }

            if (count($notWritable) > 0) {
                throw new CheckFailedException(sprintf('The following directories are not writable: "%s"', implode('", "', $notWritable)));
            }

            $result = $this->buildResult('OK', CheckResult::OK);

        } catch (\Exception $e) {
            $result = $this->buildResult($e->getMessage(), CheckResult::CRITICAL);
        }

        return $result;
    }

    /**
     * @see Liip\MonitorBundle\Check\CheckInterface::getName()
     */
    public function getName()
    {
        return 'Writable directory';
    }
}
