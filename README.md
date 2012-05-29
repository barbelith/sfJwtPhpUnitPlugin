# Welcome symfony-project.org visitors!

Please note that the version of JPUP hosted at
  http://symfony-project.org/plugins/sfJwtPhpUnitPlugin is out-of-date
  ([1.0.3](https://github.com/JWT-OSS/sfJwtPhpUnitPlugin/blob/1.0.3/README.md)
  even though it says 1.0.7 on the plugin page).

Unfortunately, the developer who maintains this project no longer has access to
  the symfony-project.org account that owns the plugin page, as he no longer
  works at JWT, so the version on symfony-project.org will likely remain
  obsolete for the foreseeable future.

If you aren't already, it is strongly recommended that you use git submodules to
  manage the JPUP installation for your projects instead of Symfony's plugin
  manager.

# 1.0.10 Update

Please note that 1.0.10 introduces a change that could break existing functional
  tests!

- `Test_Browser->call()` now expects its `$uri` parameter to resolve to a valid
    route.  If you are calling a route with required parameters, they *must* be
    included in `$uri`, or else an `InvalidArgumentException` will be thrown.

  See [USAGE.md](./USAGE.md) ("Query String Parameters") for more information.

# sfJwtPhpUnitPlugin
## About
sfJwtPhpUnitPlugin ("JPUP") was developed at [JWT](http://jwt.com) to assist in
  the development of several Symfony 1.4 projects.

JPUP boasts robust database handling (modeled loosely after
  [Django's test framework](http://docs.djangoproject.com/en/dev/topics/testing/#s-the-test-database)
  and [sfPhpUnitPlugin](https://www.hostedredmine.com/projects/sfphpunitplugin/wiki/1013#Fixtures)),
  unlimited extensibility over Symfony's `sfBrowser` class and user-friendly
  tasks for running collections of tests.

We found this plugin to be exceptionally useful for testing database-driven
  Symfony applications, and we wanted to share it with the Symfony community.

## ANOTHER PHPUnit Plugin for Symfony?
Before embarking upon development for JPUP, we took a look around, and while we
  did discover a number of existing solutions that worked fantastically, we
  found that none of them quite met our needs.

The most critical problems we set out to solve with JPUP are:

- Isolation from production data and files in a project.
- Easy (but powerful!) data manipulation and fixture integration.
- A port of `sfBrowser` that has `sfTestFunctional`'s API but doesn't use Lime.
- Using Symfony tasks to run multiple tests in one go.

## Compatibility

### Symfony
JPUP was developed specifically for projects using Symfony 1.4 and Doctrine.

Propel is not currently supported, but
  [there are plans to add Propel support](https://github.com/JWT-OSS/sfJwtPhpUnitPlugin/issues/29).

### PHPUnit
JPUP requires PHPUnit 3.6.6.

### PHP

JPUP is intended to be compatible with PHP 5.2 and greater.

## Installation
See [INSTALL.md](./INSTALL.md).

## Usage
See [USAGE.md](./USAGE.md).

## Contributing
We welcome any and all suggestions, requests, (constructive) criticism, code,
  fixes, forks, success stories... in short, if you think it would improve the
  quality of JPUP (or at least make us feel good about it), we'd love to see it.