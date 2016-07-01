
### Obullo Php Framework

Small, fast & expandable.

* Small because of it has 8 core components.
* Easy to run your application from app_dev.php in 7 steps.
* Modern because the core supports Http Middlewares, Psr7 HTTP Standarts, Container, Dependency Injection, Service Providers & composer.
* The router contains just 400 lines of code.
* The core natively supports HMVC design pattern.
* Ready for php7.

### Philosophy

* The smaller parts that consist the whole framework should be compatible with each other.

[![Gitter](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/obullo/framework?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge) [![Follow us on twitter !](https://img.shields.io/badge/twitter-follow me-green.svg?style=flat-square)](http://twitter.com/obullo)

### Status

----

There is no release yet, we are still working on it.

### Configuration of Vhost File

Put the latest version to your web root (<kbd>/var/www/project/</kbd>). Create your apache vhost file and set your project root as <kbd>public</kbd>.

```xml
<VirtualHost *:80>

	DocumentRoot /var/www/project/public

	ServerAdmin webmaster@localhost
	ServerName project

	SetEnv APPLICATION_ENV dev
	DirectoryIndex app_dev.php

</VirtualHost>
```

#### License

<a href="http://obullo.com/license.txt" targe="_blank">http://obullo.com/license.txt</a>

