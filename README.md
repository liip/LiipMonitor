# Liip Monitor #

This library provides shareable and reusable health checks. It ha been deprecated
in favor of [zendframework/ZendDiagnostics](https://github.com/zendframework/ZendDiagnostics).

For integration into Symfony2 see the [Liip Monitor Bundle](https://github.com/liip/LiipMonitorBundle).

The idea is that you fork this project and add your own health checks that you
think they can useful for someone else project. This library provides a set of
interfaces and a runner class to execute the health checks. On top of that it
also provides a set of health check implementations (see the list below).

## Installation ##

To get the source of this library simply use git:

    # get source
    git clone git://github.com/liip/LiipMonitor.git
    cd LiipMonitor

To add this library to an existing project it is recommended to use the composer installer.
Add the following to your projects ``composer.json``:

    "require": {
        ..
        "liip/monitor": "dev-master"
    },

Get the composer installer if its not yet installed on your system and run ``update``

    # install dependencies
    curl -s http://getcomposer.org/installer | php
    php composer.phar update liip/monitor


## Check groups ##

Checks can be grouped by implementing the `getGroup` method of the `CheckInterface`.
By grouping checks it's possible to implement end-user status pages which provide feedback
but hide implementation details, similar to [status.github.com](http://status.github.com).

## Available Health Checks ##

### DiscUsageCheck ###

Checks if the maximum disc usage in percentage is reached.

### DoctrineDbalCheck ###

Checks if a doctrine dbal server is running.

### HttpServiceCheck ###

Checks if an http server is running on the host, port and path specified in the service configuration,
returning the expected status code and content.

### MemcacheCheck ###

Checks if a memcache server is running on the host and port specified in the service configuration.

### RedisCheck ###

Checks if a redis server is running on the host and port specified in the service configuration.

### PhpExtensionsCheck ###

Checks if the extensions specified in the service configuration are enabled in your PHP installation.

### ProcessActiveCheck ###

Checks if a process containing a phrase specified in the service configuration is running on the machine.

### SecurityAdvisoryCheck ###

Checks any composer dependency has an open security advisory.

### WritableDirectoryCheck ###

Checks if the user executing the script is able to write in the given directory.

### RabbitMQCheck ###

Checks if a rabbitmq server is running on the host and port specified in the service configuration,
for declared user/password/vhost.

## Writing Health Checks ##

Let's see an example on how to implement a health check class.
In this case we are going to test for the availability of PHP Extensions:

    namespace Acme\Hello\Check;

    use Liip\Monitor\Check\Check;
    use Liip\Monitor\Exception\CheckFailedException;
    use Liip\Monitor\Result\CheckResult;

    class PhpExtensionsCheck extends Check
    {
        protected $extensions;

        public function __construct($extensions)
        {
            $this->extensions = $extensions;
        }

        public function check()
        {
            try {
                foreach ($this->extensions as $extension) {
                    if (!extension_loaded($extension)) {
                        throw new CheckFailedException(sprintf('Extension %s not loaded', $extension));
                    }
                }
                return $this->buildResult('OK', CheckResult::OK);
            } catch (\Exception $e) {
                return $this->buildResult(sprintf('KO - %s', $e->getMessage()), CheckResult::CRITICAL);
            }
        }

        public function getName()
        {
            return "PHP Extensions Health Check";
        }
    }

Once you implemented the class then it's time to register the check service with your runner:

    $checkChain = new \Liip\Monitor\Check\CheckChain();
    $runner = new \Liip\Monitor\Check\Runner($checkChain);

    $phpExtensionCheck = new \Acme\Hello\Check\PhpExtensionsCheck(array('apc', 'memcached'));
    $checkChain->addCheck('php_extension_check', $phpExtensionCheck);

Finally to run health checks use:

    $runner->runAllChecks() // runs all checks
    $runner->runCheckById('php_extension_check'); // runs an individual check by id

To get a list of available checks use:

    $chain->getAvailableChecks();

### CheckResult values ###

These values has been taken from the [nagios documentation](http://nagiosplug.sourceforge.net/developer-guidelines.html#RETURNCODES) :

 * ``CheckResult::OK``          - The plugin was able to check the service and it appeared to be functioning properly
 * ``CheckResult::WARNING``     - The plugin was able to check the service, but it appeared to be above some "warning"
                                   threshold or did not appear to be working properly
 * ``CheckResult::CRITICAL``    - The plugin detected that either the service was not running or it was above some "critical" threshold
 * ``CheckResult::UNKNOWN``     - Invalid command line arguments were supplied to the plugin or low-level failures
                                  internal to the plugin (such as unable to fork, or open a tcp socket) that prevent it
                                  from performing the specified operation. Higher-level errors (such as name resolution
                                  errors, socket timeouts, etc) are outside of the control of plugins and should generally
                                  NOT be reported as UNKNOWN states.

As you can see our constructor will take an array with the names of the extensions our
application requires. Then on the `check` method it will iterate over that array to
test for each of the extensions. If there are no problems then the check will return a
`CheckResult` object with a message (`OK` in our case) and the result status
(`CheckResult::SUCCESS` in our case). As you can see this is as easy as it gets.

# Contributions #

Fork this project, add a health check and then open a pull request.

# Note to contributors #

BY CONTRIBUTING TO THE LiipMonitor SOURCE CODE REPOSITORY YOU AGREE TO LICENSE YOUR CONTRIBUTION
UNDER THE TERMS OF THE MIT LICENSE AS SPECIFIED IN THE 'LICENSE' FILE IN THIS DIRECTORY.
