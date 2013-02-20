<?php

namespace Liip\Monitor\Check;

use Liip\Monitor\Exception\CheckFailedException;
use Liip\Monitor\Check\Check;
use Liip\Monitor\Result\CheckResult;
use SensioLabs\Security\SecurityChecker;

/**
 * Checks installed dependencies against the SensioLabs Security Advisory database.
 *
 * @author Baldur Rensch <brensch@gmail.com>
 */
class SecurityAdvisoryCheck extends Check
{
    /**
     * @var string
     */
    protected $lockFilePath;

    /**
     * Construct.
     *
     * @param string $kernelRootDir
     */
    public function __construct($lockFilePath)
    {
        $this->lockFilePath = $lockFilePath;
    }

    /**
     * {@inheritdoc}
     */
    public function check()
    {
        try {
            $advisories = $this->checkSymfonyAdvisories();
            if (empty($advisories)) {
                $result = $this->buildResult('OK', CheckResult::OK);
            } else {
                $result = $this->buildResult('Advisories for ' . count($advisories) . ' packages', CheckResult::WARNING);
            }
        } catch (\Exception $e) {
            $result = $this->buildResult($e->getMessage(), CheckResult::UNKNOWN);
        }

        return $result;
    }

    private function checkSymfonyAdvisories()
    {
        if (!file_exists($this->lockFilePath)) {
            throw new CheckFailedException("No composer lock file found");
        }

        $checker = new SecurityChecker();
        $alerts = $checker->check($this->lockFilePath, 'json');

        return json_decode($alerts);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'Security advisory';
    }
}