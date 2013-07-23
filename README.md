ShibbolethBundle
================

Provides Shibboleth authentication, using environmental variables, for your Symfony application. This has only
been tested on Symfony 2.2 and PHP 5.4.9 (It should work on PHP version > 5.3, I believe)

Special thanks goes out to Ronny Moreas, https://github.com/rmoreas/ShibbolethBundle. I've implemented his Shibboleth.php
object idea to make accessing config.yml settings, from within the bundle.


Composer Installation
--------------------------

### 1. Add the ShibbolethBundle to composer.json

```js
    "require": {
        ...
        "duke/shibboleth-bundle": "dev-master"
        ...
    },
   "repositories": [
        {
            "type": "vcs",
            "url": "git@gitorious.oit.duke.edu:tts-symfony2-projects/shibboleth-bundle.git"
        }
    ],  
```
### 2. Execute the download command

```bash
    php composer.phar update duke/shibboleth-bundle
```

### 3. Enable the bundle in app/AppKernel.php

```php
    public function registerBundles()
    {
        $bundles = array(
            // ...
            new Duke\ShibbolethBundle\ShibbolethBundle(),
        );
    }
```

Configuration
-------------

### 1. app/config/config.yml

```yml
	shibboleth:
		login: /shibboleth
    logout: <IDP LOGOUT URI>
    logout_target: <REDIRECT URI>		
```

### 2. app/config/security.yml

```yml
	security:
		firewalls:
			secured_area:
				...
				shibboleth: ~
        logout:
          path: /logout
          success_handler: shib.security.logout_handler
```

### 3. Create a UserProvider

An example UserProvider exists in this project's MISC directory. The sample demonstrates a UserProvider, which
relies on a document manager (mongodb) to retreive users from a database. If the user doesn't exist it is created.

