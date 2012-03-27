# Liip Monitor Extra Bundle #

This bundle aims to provide shareable and reusable Health Checks for the [Liip Monitor Bundle](https://github.com/liip/LiipMonitorBundle).

The idea is that you fork this project and add your own health checks that you think they can useful for someone else project. For example the bundle now ships with one health check for PHP Extensions availability and of course we can add many more.

## Installation ##

Add the following code to your deps file:

    [LiipMonitorExtraBundle]
        git=git://github.com/liip/LiipMonitorExtraBundle.git
        target=bundles/Liip/MonitorExtraBundle

And then run the vendors install command:

    $ ./bin/vendors install

Then open your autoload.php file and register the bundle namespace in case you haven't used any other Liip bundle before:

    $loader->registerNamespaces(array(
        ...
        'Liip'             => __DIR__.'/../vendor/bundles',
        ...
    ));

Then register the bundle in the `AppKernel.php` file:

    public function registerBundles()
    {
        $bundles = array(
            ...
            new Liip\MonitorExtraBundle\LiipMonitorExtraBundle(),
            ...
        );

        return $bundles;
    }

## Usage ##

The bundle ships with a file called `services.yml.sample` inside its `Resources` folder. Just pick the services you want from there and add them to a place that suits your project, for example the App's `config.yml` file.

# Contributions #

## Creating new health checks ##

1. Create your own Health Check class. To understand how health checks work and what they should do take a look at the [Liip Monitor Bundle](https://github.com/liip/LiipMonitorBundle) `README.md` file. Your new health check must go into the bundle's `Check` folder.
2. Add the sample configuration for the service into the `services.yml.sample` file.
3. Add your health check name and the description of what it does into the `HEALTH_CHECKS.md` file from this project. **Please do so alphabetically**.

## Available Health Checks ##

To see a list of the health checks that this bundle provides please consult the file `HEALTH_CHECKS.md`.

# Note to contributors #

BY CONTRIBUTING TO THE LiipMonitorExtraBundle SOURCE CODE REPOSITORY YOU AGREE TO LICENSE YOUR CONTRIBUTION UNDER THE TERMS OF THE MIT LICENSE AS SPECIFIED IN THE 'LICENSE.md' FILE IN THIS DIRECTORY.