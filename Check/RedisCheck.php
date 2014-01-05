<?php

namespace Liip\Monitor\Check;

use Liip\Monitor\Check\Check;
use Liip\Monitor\Exception\CheckFailedException;
use Liip\Monitor\Result\CheckResult;

use Predis\Client;

/**
 * RedisCheck.
 *
 * @uses \Liip\Monitor\Check\Check
 * @author Cédric Dugat <cedric@dugat.me>
 */
class RedisCheck extends Check
{
    /**
     * @var string
     */
    protected $host;

    /**
     * @var integer
     */
    protected $port;

    /**
     * Construct.
     *
     * @param string  $host
     * @param integer $port
     */
    public function __construct($host = 'localhost', $port = 6379)
    {
        $this->host = $host;
        $this->port = $port;
    }

    /**
     * {@inheritdoc}
     * @see \Liip\MonitorBundle\Check\CheckInterface::check()
     */
    public function check()
    {
        try {
            $client = new Client(array(
                'host' => $this->host,
                'port' => $this->port,
            ));
            if (!$client->ping()) {
                throw new CheckFailedException(
                    sprintf(
                        'No Redis server running at host %s on port %s',
                        $this->host,
                        $this->port
                    )
                );
            }
            $result = $this->buildResult('OK', CheckResult::OK);
        } catch (\Exception $e) {
            $result = $this->buildResult($e->getMessage(), CheckResult::CRITICAL);
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     * @see \Liip\MonitorBundle\Check\Check::getName()
     */
    public function getName()
    {
        return 'Redis';
    }
}
