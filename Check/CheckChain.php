<?php

namespace Liip\Monitor\Check;

use Liip\Monitor\Check\CheckInterface;

final class CheckChain
{
    protected $checks = array();

    /**
     * @param array $checks
     */
    public function __construct(array $checks = array())
    {
        foreach ($checks as $serviceId => $check) {
            $this->addCheck($serviceId, $check);
        }
    }

    /**
     * @param string $serviceId
     * @param CheckInterface $check
     */
    public function addCheck($serviceId, CheckInterface $check)
    {
        $this->checks[$serviceId] = $check;
    }

    /**
     * @return array
     */
    public function getChecks()
    {
        return $this->checks;
    }

    /**
     * @return array
     */
    public function getAvailableChecks()
    {
        return array_keys($this->checks);
    }

    /**
     * @throws \InvalidArgumentException
     * @param string $id
     * @return \Liip\Monitor\Check\CheckInterface
     */
    public function getCheckById($id)
    {
        if (!isset($this->checks[$id])) {
            throw new \InvalidArgumentException(sprintf("Check with id: %s doesn't exist", $id));
        }

        return $this->checks[$id];
    }

    /**
     * @param string $name
     * @return array
     */
    public function getChecksByGroup($name)
    {
        $checks = array();

        foreach ($this->checks as $id => $check) {
            if ($check->getGroup() === $name) {
                $checks[] = $id;
            }
        }

        return $checks;
    }

    /**
     * @return array
     */
    public function getGroups()
    {
        $groups = array();

        foreach ($this->checks as $check) {

            if (!in_array($check->getGroup(), $groups)) {
                $groups[] = $check->getGroup();
            }
        }

        return $groups;
    }
}