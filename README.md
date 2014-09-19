# Atipik/HoaWebSocketBundle

[![Build Status](https://secure.travis-ci.org/Atipik/Hoa-WebSocket-Bundle.png)](http://travis-ci.org/Atipik/Hoa-WebSocket-Bundle)

This bundle allows you to use Hoa/Websocket/Server and Hoa/Websocket/Client into Symfony2.

With a simple configuration, you will be able to launch your WebSocket server


## 1. Installation

### 1.1 Update your composer.json

Add these lines to your require section:

```json
{
    "require": {
        "atipik/hoa-websocket-bundle" : "1.*@dev"
    }
}
```

### 1.2 Install dependencies

```shell
composer update
```

### 1.3 Update your AppKernel.php file

```php
<?php
# app/AppKernel.php
// ...

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...
            new Atipik\Hoa\WebSocketBundle\AtipikHoaWebSocketBundle(),
            // ...
        );

        return $bundles;
    }

    // ...
}

```

### 1.4 Configure your project

To enable this bundle, you must add these lines in your `config.yml`:

```yaml
# app/config/config.yml
atipik_hoa_web_socket:
    websocket:  ~
```

You can also specify address and port.

```yaml
# app/config/config.yml
atipik_hoa_web_socket:
    address : 1.3.3.7
    port    : 4242
```

By default, server will be started on 127.0.0.1:8080


## 2. Architecture

The Symfony 2 command `php app/console hoa:websocketserver` has only one job: calling a runner.

The runner links modules (your logic code) and WebSocket events which are managed by `Atipik\Hoa\WebSocketBundle\WebSocket\Server`.

When the WebSocket server receives an event, the runner catches it and calls all subscribed modules.


## 3. Server

### 3.1 Simple Usage

- In your bundle, create a class which extends `Atipik\Hoa\WebSocketBundle\WebSocket\Module\Module`:

```php
<?php
# src/My/Bundle/WebSocket/Module/MyModule.php
namespace My\Bundle\WebSocket\Module;

use Atipik\Hoa\WebSocketBundle\WebSocket\Module\Module;

class MyModule extends Module
{
}
```

- Update your `services.yml`:

```yaml
# src/My/Bundle/Resources/config/services.yml
services:
    my_bundle.my_module:
        class: My\Bundle\WebSocket\Module\MyModule
        tags:
            - { name: atipik_hoa_web_socket.module }
```

Don't forget to add `atipik_hoa_web_socket.module` tag !

- In this class, implement the `getSubscribedEvents` method:

```php
<?php
# src/My/Bundle/WebSocket/Module/MyModule.php
namespace My\Bundle\WebSocket\Module;

use Atipik\Hoa\WebSocketBundle\WebSocket\Module\Module;

class MyModule extends Module
{
    public function getSubscribedEvents()
    {
        return array(
            'open'    => 'onOpen',
            'message' => 'onMessage',
        );
    }
}
```

- Add logic code:

You can get the current bucket by using `$this->getBucket()`.

```php
<?php
# src/My/Bundle/WebSocket/Module/MyModule.php
namespace My\Bundle\WebSocket\Module;

use Atipik\Hoa\WebSocketBundle\WebSocket\Module\Module;

class MyModule extends Module
{
    public function getSubscribedEvents()
    {
        return array(
            'open'    => 'onOpen',
            'message' => 'onMessage',
        );
    }

    public function onOpen()
    {
        $this->getLogger()->log('Here comes a new challenger !');
    }

    public function onMessage()
    {
        $data = $this->getBucket()->getData();

        $this->getLogger()->log(
            'Data received in %s: %s',
            __METHOD__,
            $data['message']
        );
    }
}
```

- Launch your WebSocket server:

```shell
php app/console hoa:websocketserver
```

### 3.2 Advanced Usage

#### 3.2.1 Modules group

If you want to scale your server, you can affect modules to different group and launch server for one or more group.

You can affect many modules in the same group

If you launch the server without group, all modules will be used.

- Just override `getGroup()` method in your module:

```php
<?php
# src/My/Bundle/WebSocket/Module/MyModule.php
namespace My\Bundle\WebSocket\Module;

use Atipik\Hoa\WebSocketBundle\WebSocket\Module\Module;

class MyModule extends Module
{
    // ...

    /**
     * Returns group name
     *
     * @return string
     */
    public function getGroup()
    {
        return 'foo';
    }

    // ...
}

```

- Launch your server by specifying group:

```shell
php app/console hoa:websocketserver -g foo
```

You can also specify more than one group:

```shell
php app/console hoa:websocketserver -g foo -g bar
```

#### 3.2.2 Override runner class

If you want to modify how the runner works, you should override its class:

- Create a runner class that extends `Atipik\Hoa\WebSocketBundle\WebSocket\Runner`.
- Update your `services.yml`:

```yaml
# src/My/Bundle/Resources/config/services.yml
parameters:
    atipik_hoa_web_socket.runner.class: My\Bundle\WebSocket\Runner
```

#### 3.2.3 Override server class

If you want to modify how the WebSocket Server works, you should override its class:

- Create a server class which extends `Atipik\Hoa\WebSocketBundle\WebSocket\Server`.
- Update your `services.yml`:

```yaml
# src/My/Bundle/Resources/config/services.yml
parameters:
    atipik_hoa_web_socket.server.class: My\Bundle\WebSocket\Server
```

#### 3.2.4 Override node class

Hoa/Websocket allows you to override node class to add your own data.

Of course, this bundle allow you to specify which class to use:

- Create a node class which extends `Hoa\Websocket\Node`:

```php
<?php
# src/My/Bundle/WebSocket/Node.php
namespace My\Bundle\WebSocket;

class Node extends \Hoa\Websocket\Node
{
    protected $myData;

    public function getMyData()
    {
        return $this->myData;
    }

    public function setMyData($data)
    {
        $this->myData = $data;

        return $this;
    }

    public function doThingsWithMyData()
    {
        // ...
    }
}
```

- Update your `services.yml`:

```yaml
# src/My/Bundle/Resources/config/services.yml
parameters:
    atipik_hoa_web_socket.node.class: My\Bundle\WebSocket\Node
```

- You can now access the current node, an instance of your node class:

```php
<?php
# src/My/Bundle/WebSocket/Module/MyModule.php
namespace My\Bundle\WebSocket\Module;

use Atipik\Hoa\WebSocketBundle\WebSocket\Module\Module;

class MyModule extends Module
{
    // ...

    public function onEvent1()
    {
        // ...

        $node = $this->getNode();

        $node->setMyData('foobar');

        // ..
    }

    // ...

    public function onEvent2()
    {
        // ...

        $node = $this->getNode();

        $node->getMyData(); // contain 'foobar' set in event1

        // ..
    }

    // ...
}
```


## 4. Client

If you want to communicate with a WebSocket Server, you can use service `atipik_hoa_web_socket.client` by using `$this->get('atipik_hoa_web_socket.client')` in your controller or to inject this service directly in services.yml.

For more documentation about WebSocket Client, see [Hoa/WebSocket's documentation](http://hoa-project.net/Literature/Hack/Websocket.html#Write_a_client).


## 5. Launch unit tests

```shell
composer update
./vendor/bin/atoum
```


## 6. More documentation

See [Hoa/WebSocket's documentation)](http://hoa-project.net/Literature/Hack/Websocket.html) to know how to use How/WebSocket and to have an example of JavaScript code.
