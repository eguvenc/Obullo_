
### Obullo Php Framework

Small, fast & expandable.

* Small because of it has 8 core components.
* Easy to run your application from dev.app.php in 5 steps.
* Supports Http Middlewares, Ps6 and Psr7 Standarts, Dependency Injection, Service Providers.
* Bundle creation. ( A Bundle is a directory containing a set of files ( Controllers, Routes, Middlewares, Console Commands, Services Providers, Templates, Data, …) that implement a single feature (a blog, a forum, etc).
* The core natively supports HMVC design pattern.
* The router contains just 400 lines of code.
* Ready for php7.

### Philosophy

* The smaller parts that consist the whole framework should be compatible with each other.

[![Gitter](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/obullo/framework?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge) [![Follow us on twitter !](https://img.shields.io/badge/twitter-follow me-green.svg?style=flat-square)](http://twitter.com/obullo)

### Status

----

There is no release yet, we are still working on it.

### Configuration of Vhost File for "dev" environment

Put the latest version to your web root (<kbd>/var/www/project/</kbd>). Create your apache vhost file and set your project root as <kbd>public</kbd>.

```xml
<VirtualHost *:80>

	DocumentRoot /var/www/project/public

	ServerAdmin webmaster@localhost
	ServerName project

	SetEnv APP_ENV dev
	DirectoryIndex dev.app.php

</VirtualHost>
```

#### License

<a href="http://obullo.com/license.txt" targe="_blank">http://obullo.com/license.txt</a>

