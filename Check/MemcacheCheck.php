<?php

namespace Liip\MonitorExtraBundle\Check;

use Liip\MonitorBundle\Check\Check;
use Liip\MonitorBundle\Exception\CheckFailedException;
use Liip\MonitorBundle\Result\CheckResult;

class MemcacheCheck extends Check
{
    /**
     * @var string
     */
    protected $host;

    /**
     * @var int
     */
    protected $port;

    /**
     * @param string $host
     * @param int $port
     */
    public function __construct($host, $port = 11211)
    {
        $this->host = $host;
        $this->port = $port;
    }

    /**
     * @see Liip\MonitorBundle\Check.CheckInterface::check()
     */
    public function check()
    {
        try {
            $memcache = new \Memcache();
            $memcache->addServer($this->host, $this->port);
            $stats = @$memcache->getExtendedStats();
            $available = $stats[$this->host . ':' . $this->port] !== false;
            if (!$available && !@$memcache->connect($this->host, $this->port)) {
                throw new CheckFailedException(sprintf('No memcache server running at host %s on port %s', $this->host, $this->port));
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
        return 'Memcache';
    }
}
