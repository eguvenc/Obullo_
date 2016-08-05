
### Obullo

Small, fast & expandable php framework.

* Small because of it has 10 core components.
* Easy to run your application from <kbd>dev_app.php</kbd> in 5 steps.
* Supports Psr2, Ps6, Psr7 Standarts.
* Http Middlewares, Dependency Injection and Service Providers.
* Bundle creation. ( A Bundle is a directory containing a set of files ( Controllers, Route, Middlewares, Console Commands, Services Providers, View files and Resources, …) that implement a single feature (a blog, a forum, etc).
* The kernel natively supports HMVC design pattern.
* The router contains just 400 lines of code.
* Supports Doctrine/DBAL and Doctrine ORM.
* It use Zend, Symfony and The Php League libraries via composer.

### Philosophy

* The smaller parts that consist the whole framework should be compatible with each other.

[![Gitter](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/obullo/framework?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge) [![Follow us on twitter !](https://img.shields.io/badge/twitter-follow me-green.svg?style=flat-square)](http://twitter.com/obullo)

### Server Requirements

* php >=5.5.9

### Status

----

[x] There is no release yet, we are still working on it.

### Configuration of Vhost File for "dev" environment

Put the latest version to your web root (<kbd>/var/www/project/</kbd>). Create your apache vhost file and set your project root as <kbd>public</kbd>.

```xml
<VirtualHost *:80>

	DocumentRoot /var/www/project/public

	ServerAdmin webmaster@localhost
	ServerName project

	SetEnv APP_ENV dev
	DirectoryIndex dev_app.php

</VirtualHost>
```
