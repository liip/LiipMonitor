<?php
namespace Liip\Monitor\Check;

use Liip\Monitor\Check\Check;
use Liip\Monitor\Result\CheckResult;
use Doctrine\DBAL\Connection;
use Symfony\Bridge\Doctrine\ManagerRegistry;

class DoctrineDbalCheck extends Check
{
    /**
     * @var ManagerRegistry
     */
    protected $manager;

    /**
     * @var string
     */
    protected $connectionName;

    public function __construct(ManagerRegistry $manager, $connectionName = 'default')
    {
        $this->manager = $manager;
        $this->connectionName = $connectionName;
    }

    public function check()
    {
        try {
            $connection = $this->manager->getConnection($this->connectionName);
            $connection->fetchColumn('SELECT 1');
            $result = $this->buildResult('OK', CheckResult::OK);
        } catch (\Exception $e) {
            $result = $this->buildResult($e->getMessage(), CheckResult::CRITICAL);
        }

        return $result;
    }

    public function getName()
    {
        return "Doctrine DBAL Connnection";
    }
}