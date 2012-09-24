<?php

namespace Liip\Monitor\Check;

interface CheckInterface
{
    /**
     * @return \Liip\Monitor\Result\CheckResult
     */
    function check();

    /**
     * @return string
     */
    function getName();
}