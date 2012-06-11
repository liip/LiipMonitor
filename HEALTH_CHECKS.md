# Available Health Checks #

## CustomErrorPagesCheck ##

Checks if error pages have been customized for given error codes.

## DepsEntriesCheck ##

Checks all entries from `deps` are defined in `deps.lock`.

## MemcacheCheck ##

Checks if a memcache server is running on the host and port specified in the service configuration.

## PhpExtensionsCheck ##

Checks if the extensions specified in the service configuration are enabled in your PHP installation.

## ProcessActiveCheck ##

Checks if a process containing a phrase specified in the service configuration is running on the machine.

## Symfony Version Check ##

Checks if the local Symfony version is the same or later than the current stable version.

## WritableDirectoryCheck ##

Checks if the user executing the script is able to write in the given directory.
