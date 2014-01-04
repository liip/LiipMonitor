<?php

namespace Liip\Monitor\Check;

use Liip\Monitor\Check\Check;
use Liip\Monitor\Exception\CheckFailedException;
use Liip\Monitor\Result\CheckResult;

use PhpAmqpLib\Connection\AMQPConnection;

/**
 * RabbitMQCheck.
 *
 * @uses \Liip\Monitor\Check\Check
 * @author Cédric Dugat <cedric@dugat.me>
 */
class RabbitMQCheck extends Check
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
     * @param string  $user
     * @param string  $password
     * @param string  $vhost
     */
    public function __construct(
        $host = 'localhost',
        $port = 5672,
        $user = 'guest',
        $password = 'guest',
        $vhost = '/'
    )
    {
        $this->host     = $host;
        $this->port     = $port;
        $this->user     = $user;
        $this->password = $password;
        $this->vhost    = $vhost;
    }

    /**
     * {@inheritdoc}
     * @see \Liip\MonitorBundle\Check\CheckInterface::check()
     */
    public function check()
    {
        try {
            $conn = new AMQPConnection(
                $this->host,
                $this->port,
                $this->user,
                $this->password,
                $this->vhost
            );
            $ch = $conn->channel();
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
        return 'RabbitMQ';
    }
}
