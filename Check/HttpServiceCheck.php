<?php

namespace Liip\Monitor\Check;

use Liip\Monitor\Check\Check;
use Liip\Monitor\Exception\CheckFailedException;
use Liip\Monitor\Result\CheckResult;
use InvalidArgumentException;

class HttpServiceCheck extends Check
{
    /**
     * @var string
     */
    protected $hosts;

    /**
     * @param array $host
     * @param int $port
     */
    public function __construct($hosts)
    {
        if (!is_array($hosts)) {
            $host    = func_get_arg(0);
            $numArgs = func_num_args();
            $hosts   = array();
            $hosts['default'] = array(
                'host'       => $host,
                'port'       => $numArgs >= 1 ? func_get_arg(1) : 80,
                'path'       => $numArgs >= 2 ? func_get_arg(2) : '/',
                'statusCode' => $numArgs >= 3 ? func_get_arg(3) : 200,
                'content'    => $numArgs >= 4 ? func_get_arg(4) : null,
            );
        }

        $this->setHosts($hosts);
    }

    /**
     * @see Liip\MonitorBundle\Check\CheckInterface::check()
     */
    public function check()
    {
        try {
            foreach ($this->hosts as $alias => $options) {
                $fp = @fsockopen($options['host'], $options['port'], $errno, $errstr, 10);
                if (!$fp) {
                    $message = 'No http service \'%s\' running at host %s on port %s';
                    throw new CheckFailedException(
                        sprintf($message, $alias, $options['host'], $options['port'])
                    );
                } else {
                    $header = "GET {$options['path']} HTTP/1.1\r\n";
                    $header .= "Host: {$options['host']}\r\n";
                    $header .= "Connection: close\r\n\r\n";
                    fputs($fp, $header);
                    $str = '';
                    while (!feof($fp)) {
                        $str .= fgets($fp, 1024);
                    }

                    fclose($fp);

                    $regex = "HTTP\/1\.[10] " . $options['statusCode'];
                    if ($options['statusCode'] && !preg_match("/$regex/", $str, $matches)) {
                        $message = "Status code %s to \'%s\' does not match in response from %s:%s%s";
                        throw new CheckFailedException(
                            sprintf(
                                $message,
                                $options['statusCode'],
                                $alias,
                                $options['host'],
                                $options['port'],
                                $options['path']
                            )
                        );
                    } elseif ($options['content'] && !strpos($str, $options['content'])) {
                        $message = "Content %s not found to \'%s\' in response from %s:%s%s";
                        throw new CheckFailedException(
                            sprintf(
                                $message,
                                $options['content'],
                                $alias,
                                $options['host'],
                                $options['port'],
                                $options['path']
                            )
                        );
                    }
                }
            }
            $result = $this->buildResult('OK', CheckResult::OK);
        } catch (\Exception $e) {
            $result = $this->buildResult($e->getMessage(), CheckResult::CRITICAL);
        }

        return $result;
    }

    /**
     * @param array $hosts
     * @return      $this
     */
    public function setHosts(array $hosts)
    {
        $this->hosts = array();
        foreach ($hosts as $alias => $options) {
            $this->addHost($alias, $options);
        }

        return $this;
    }

    /**
     * @param string $alias
     * @param array  $options
     * @return       $this
     */
    public function addHost($alias, array $options)
    {
        $this->checkRequiredOptions($options);;
        $options = $this->mergeDefaultOptions($options);
        $this->hosts[$alias] = $options;

        return $this;
    }

    /**
     * Make merge between options info and value default
     *
     * @param array $options
     * @return array
     */
    private function mergeDefaultOptions(array $options)
    {
        $defaultOptions = array(
            'port'       => 80,
            'path'       => '/',
            'statusCode' => 200,
            'content'    => null
        );

        return $options + $defaultOptions;
    }

    /**
     * Check options requireds
     *
     * @param $options
     * @throws \InvalidArgumentException
     */
    private function checkRequiredOptions($options)
    {
        if (!isset($options['host'])) {
            throw new InvalidArgumentException('Host is required');
        }
    }

    /**
     * @see Liip\MonitorBundle\Check\Check::getName()
     */
    public function getName()
    {
        return 'Http Service';
    }
}
