<?php

namespace Liip\Monitor\Check;

interface CheckInterface
{
    /**
     * @return \Liip\Monitor\Result\CheckResult
     */
    public function check();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getGroup();
}