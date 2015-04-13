ShibbolethBundle
================

Provides Shibboleth authentication, using environmental variables, for your Symfony >=2.2 application.

Special thanks goes out to Ronny Moreas, https://github.com/rmoreas/ShibbolethBundle. I've implemented his Shibboleth.php
object idea to make accessing config.yml settings, from within the bundle, easier. If your Shibboleth instance is setup
to use request headers instead of environmental variables, check out his bundle.

Installation
-------------

### 1. Add the ShibbolethBundle to composer.json

```js
  "require": {
      ...
      "fahl/shibboleth-bundle": "dev-master"
      ...
  },
  "repositories": [
      {
          "type": "git",
          "url": "git@github.com:gabefahl/ShibbolethBundle.git"
      }
  ],  
```

### 2. Execute the Composer update command

```bash
  php composer.phar update fahl/shibboleth-bundle
```

### 3. Enable the bundle in app/AppKernel.php

```php
  public function registerBundles()
  {
      $bundles = array(
          // ...
          new Fahl\ShibbolethBundle\ShibbolethBundle(),
      );
  }
```

Configuration
-------------

### 1. app/config/config.yml

FYI: In my local setup, I use port 8080. By default, Symfony redirects the user to port 80 when using
SSL. You can change this behavior under the framework configuration as shown below.

```yml
framework:
  ...
  router:
    http_port: 8080
    https_port: 8080
  
shibboleth:
  # Shib sample login
  login: /shibboleth
    
  # Shib sample logout
  logout: /Shibboleth.sso/Logout
    
  # Sample returnto query param value used by IDP
  returnto: http://myapplication.com
    
  # Sample ENV var used for the username
  username: REMOTE_USER 
```

### 2. app/config/security.yml

```yml
  security:
    ...
    firewalls:
      secured_area:
        ...    
        
        # Add the authentication listener
        shibboleth: ~
        logout:
          
          # Logout path of for local logout
          path: /logout
          
          # Handler which handle redirect to Shib logout specified in config.yml
          success_handler: shibboleth.security.logout_handler
         
          # Needed for PHP < 5.4.11 bug
          invalidate_session: false

    access_control:
  
      # Sample ACL path. If logged in users should be using SSL, add the requires_channel attribute to force SSL
      - { path: ^/ roles: [ROLE_ADMIN, ROLE_GUEST], requires_channel: https }
      
```

### 3. Create a UserProvider

An example UserProvider exists in this project's MISC directory. The sample demonstrates a UserProvider, which
relies on a document manager (I'm using Mongodb) to retreive users from a database. If the user doesn't exist it is created.

### 3a. Create your user class and UserProvider (UserProvider sample found in MISC/ of this project)

http://symfony.com/doc/current/cookbook/security/entity_provider.html

### 3b. Add UserProvider

```yml
# app/config/security.yml
  security:
    ... 
    providers:
    
      # A sample document provider
      document_provider:
        id: shibboleth_user_provider
```

### 3c. Create UserProvider service

I'm injecting a document manager into my service because my project uses Mongodb, but you could just as easily inject an entity manager


```yml
# Resources/config/services.yml
  services:
    shibboleth_user_provider:
      class: THE\SAMPLE\ShibbolethUserProvider
      arguments: [@doctrine.odm.mongodb.document_manager]
```
