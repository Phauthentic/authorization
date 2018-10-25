# Policy Resolvers

Mapping resource objects to their respective policy classes is a behavior
handled by a policy resolver. We provide a few resolvers to get you started, but
you can create your own resolver by implementing the
`Authorization\Policy\ResolverInterface`. The built-in resolvers are:

* `MapResolver` allows you to map resource names to their policy class names, or
  to objects and callables.
* `OrmResolver` applies conventions based policy resolution for common ORM
  objects.
* `ResolverCollection` allows you to aggregate multiple resolvers together,
  searching them sequentially.

## Using MapResolver

`MapResolver` lets you map resource class names to policy classnames, policy
objects, or factory callables:

```php
use Phauthentic\Authorization\Policy\MapResolver;

$map = new MapResolver();

// Map a resource class to a policy classname
$map->map(Article::class, ArticlePolicy::class);

// Map a resource class to a policy instance.
$map->map(Article::class, new ArticlePolicy());

// Map a resource class to a factory function
$map->map(Article::class, function ($resource, $mapResolver) {
    // Return a policy object.
});
```

## Using OrmResolver

The `OrmResolver` is a conventions based policy resolver for CakePHP's ORM. The
OrmResolver applies the following conventions:

1. Policies live in `App\Policy`
2. Policy classes end with the `Policy` class suffix.

The OrmResolver can resolve policies for the following object types:

* Entities - Using the entity classname.
* Tables - Using the table classname.
* Queries - Using the return of the query's `repository()` to get a classname.

In all cases the following rules are applied:

1. The resource classname is used to generate a policy class name. e.g
   `App\Model\Entity\Bookmark` will map to `App\Policy\BookmarkPolicy`
2. Plugin resources will first check for an application policy e.g
   `App\Policy\Bookmarks\BookmarkPolicy` for `Bookmarks\Model\Entity\Bookmark`.
3. If no application override policy can be found, a plugin policy will be
   checked. e.g. `Bookmarks\Policy\BookmarkPolicy`

For table objects the class name tranformation would result in
`App\Model\Table\ArticlesTable` mapping to `App\Policy\ArticlesTablePolicy`.
Query objects will have their `repository()` method called, and a policy will be
generated based on the resulting table class.

The OrmResolver supports customization through its constructor:

```php
use Phauthentic\Authorization\Policy\OrmResolver;

// Change when using a custom application namespace.
$appNamespace = 'App';

// Map policies in one namespace to another.
// Here we have mapped policies for classes in the `Blog` namespace to be 
// found in the `Cms` namespace.
$overrides = [
    'Blog' => 'Cms',
];
$resolver = new OrmResolver($appNamespace, $overrides)
```

## Using ResolverCollection

`ResolverCollection` allows you to aggregate multiple resolvers together:

```php
use Phauthentic\Authorization\Policy\ResolverCollection;
use Phauthentic\Authorization\Policy\MapResolver;
use Phauthentic\Authorization\Policy\OrmResolver;

$orm = new OrmResolver();
$map = new MapResolver();

// Check the map resolver, and fallback to the orm resolver if
// a resource is not explicitly mapped.
$resolver = new ResolverCollection([$map, $orm]);
```

## Creating a Resolver

You can implement your own resolver by implementing the
`Authorization\Policy\ResolverInterface` which requires defining the
`getPolicy($resource)` method.
