<?php

namespace Liip\MonitorExtraBundle\Check;

use Liip\MonitorBundle\Check\Check;
use Liip\MonitorBundle\Exception\CheckFailedException;
use Liip\MonitorBundle\Result\CheckResult;

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
     * @see Liip\MonitorBundle\Check.CheckInterface::check()
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
     * @see Liip\MonitorBundle\Check.Check::getName()
     */
    public function getName()
    {
        return 'Writable directory';
    }
}