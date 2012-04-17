<?php

namespace Liip\MonitorExtraBundle\Check;

use \Symfony\Component\HttpKernel\Kernel;
use Liip\MonitorBundle\Check\Check;
use Liip\MonitorBundle\Exception\CheckFailedException;
use Liip\MonitorBundle\Result\CheckResult;

/**
 * Checks the version of this website against the latest stable release.
 *
 * Add this to your config.yml
 *
 *     monitor.check.symfony_version:
 *         class: Liip\MonitorExtraBundle\Check\SymfonyVersionCheck
 *         tags:
 *             - { name: monitor.check }
 *
 * @author Roderik van der Veer <roderik@vanderveer.be>
 */
class SymfonyVersionCheck extends Check
{
    /**
     * @var array
     */
    protected $directories;

    /**
     * Construct.
     */
    public function __construct()
    {

    }

    /**
     * @see Liip\MonitorBundle\Check.CheckInterface::check()
     */
    public function check()
    {
        try {
            $latestRelease = $this->getLatestSymfonyVersion(); // eg. 2.0.12
            $currentVersion = Kernel::VERSION;
            if (version_compare($currentVersion, $latestRelease) >= 0) {
                $result = $this->buildResult('OK', CheckResult::OK);
            } else {
                $result = $this->buildResult('Update to ' . $latestRelease . ' from ' . $currentVersion, CheckResult::CRITICAL);
            }
        } catch (\Exception $e) {
            $result = $this->buildResult($e->getMessage(), CheckResult::CRITICAL);
        }

        return $result;
    }

    private function getLatestSymfonyVersion()
    {
        $githubUser = 'symfony';
        $githubRepo = 'symfony';

        // Get GitHub JSON request

        $githubUrl = 'http://github.com/api/v2/json/repos/show/' . $githubUser . '/' . $githubRepo . '/tags';
        $githubJSONResponse = file_get_contents($githubUrl);

        // Convert it to a PHP object

        $githubResponseArray = json_decode($githubJSONResponse, true);
        $tagList = array_keys($githubResponseArray["tags"]);

        // Filter out non final tags

        $filteredTagList = array_filter($tagList, function($tag)
        {
            return !stripos($tag, "-") && !stripos($tag, "PR") && !stripos($tag, "BETA") && stripos($tag, "v2.0") === 0;
        });

        // Sort tags

        natcasesort($filteredTagList);

        // The first one is the last stable release for Symfony 2

        $reverseFilteredTagList = array_reverse($filteredTagList);
        return str_replace("v", "", $reverseFilteredTagList[0]);
    }

    /**
     * @see Liip\MonitorBundle\Check.Check::getName()
     */
    public function getName()
    {
        return 'Symfony version';
    }
}
