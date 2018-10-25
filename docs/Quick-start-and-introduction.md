# Quick Start

## Installation

Install the plugin with [composer](https://getcomposer.org/) from your CakePHP
Project's ROOT directory (where the **composer.json** file is located)

```sh
composer require cakephp/authorization
```

## Getting Started

The Authorization plugin integrates into your application as a middleware layer
and optionally a component to make checking authorization easier. First, lets
apply the middleware. In **src/Application.php** add the following to the class
imports:

```php
use Phauthentic\Authorization\AuthorizationService;
use Phauthentic\Authorization\AuthorizationServiceProviderInterface;
use Phauthentic\Authorization\Middleware\AuthorizationMiddleware;
use Phauthentic\Authorization\Policy\OrmResolver;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
```

Add the `AuthorizationProviderInterface` to the implemented interfaces on your application:

```php
class Application extends BaseApplication implements AuthorizationServiceProviderInterface
```

Then add the following to your `middleware()` method:

```php
// Add authorization (after authentication if you are using that plugin too).
$middleware->add(new AuthorizationMiddleware($this));
```

The `AuthorizationMiddleware` will call a hook method on your application when
it starts handling the request. This hook method allows your application to
define the `AuthorizationService` it wants to use. Add the following method your
**src/Application.php**

```php
public function getAuthorizationService(ServerRequestInterface $request, ResponseInterface $response)
{
    $resolver = new OrmResolver();

    return new AuthorizationService($resolver);
}
```

This configures a very basic [resolver](./Policy-Resolvers.md) that will match
ORM entities with their policy classes.

Next lets add the `AuthorizationComponent` to `AppController`. In
**src/Controller/AppController.php** add the following to the `initialize()`
method:

```php
$this->loadComponent('Authorization.Authorization');
```

By loading the [authorization component](./Component.php) we'll be able to check
authorization on a per action basic more easily. For example, we can do:

```php
public function edit($id = null)
{
    $article = $this->Article->get($id);
    $this->Authorization->authorize('update', $article);

    // Rest of action
}
```

By calling `authorize` we can use our [policies](./Policies.md) to enforce our
application's access control rules. You can check permissions anywhere by using
the [identity stored in the request](./Checking-Authorization.md).


## Further Reading

* [Create a Policy](/docs/Policies.md)
* [Choose a Policy Resolver](/docs/Policy-Resolvers.md)
* [Using the Middleware](/docs/Middleware.md)
* [Using the Component](/docs/Component.md)
* [Checking Authorization](/docs/Checking-Authorization.md)
* [Authorizing based on request params](/docs/Request-Authorization-Middleware.md)
