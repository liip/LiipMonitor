<?php

namespace Liip\MonitorExtraBundle\Check;

use Liip\MonitorBundle\Check\Check;
use Liip\MonitorBundle\Exception\CheckFailedException;
use Liip\MonitorBundle\Result\CheckResult;

/**
 * Check if the given directory is writable.
 *
 * @author Cédric Girard <c.girard@lexik.fr>
 */
class WritableDirectoryCheck extends Check
{
    /**
     * @var string
     */
    protected $path;

    /**
     * Construct.
     *
     * @param string $path
     */
    public function __construct($path)
    {
        $this->path = $path;
    }

    /**
     * @see Liip\MonitorBundle\Check.CheckInterface::check()
     */
    public function check()
    {
        try {
            $user = exec("whoami");

            if (!is_writable($this->path)) {
                throw new CheckFailedException(sprintf('The user "%s" is NOT able to write in "%s"', $user, $this->path));
            }

            $message = sprintf('The user "%s" is able to write in "%s"', $user, $this->path);
            $result = $this->buildResult($message, CheckResult::SUCCESS);

        } catch (\Exception $e) {
            $result = $this->buildResult($e->getMessage(), CheckResult::FAILURE);
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