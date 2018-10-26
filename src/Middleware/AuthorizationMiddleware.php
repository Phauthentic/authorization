<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         1.0.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */
namespace Phauthentic\Authorization\Middleware;

use Phauthentic\Authorization\AuthorizationServiceInterface;
use Phauthentic\Authorization\AuthorizationServiceProviderInterface;
use Phauthentic\Authorization\Exception\AuthorizationRequiredException;
use Phauthentic\Authorization\Exception\Exception;
use Phauthentic\Authorization\IdentityDecorator;
use Phauthentic\Authorization\IdentityInterface;
use Phauthentic\Authorization\Middleware\UnauthorizedHandler\HandlerFactory;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;

/**
 * Authorization Middleware.
 *
 * Injects the authorization service and decorated identity objects into the request object as attributes.
 */
class AuthorizationMiddleware
{
    use InstanceConfigTrait;

    /**
     * Default config.
     *
     * - `identityDecorator` Identity decorator class name or a callable.
     *   Defaults to IdentityDecorator
     * - `identityAttribute` Attribute name the identity is stored under.
     *   Defaults to 'identity'
     * - `requireAuthorizationCheck` When true the middleware will raise an exception
     *   if no authorization checks were done. This aids in ensuring that all actions
     *   check authorization. It is intended as a development aid and not to be relied upon
     *   in production. Defaults to `true`.
     *
     * @var array
     */
    protected $_defaultConfig = [
        'identityDecorator' => IdentityDecorator::class,
        'identityAttribute' => 'identity',
        'requireAuthorizationCheck' => true,
        'unauthorizedHandler' => 'Authorization.Exception',
    ];

    /**
     * Authorization service or application instance.
     *
     * @var \Phauthentic\Authorization\AuthorizationServiceInterface|\Phauthentic\Authorization\AuthorizationServiceProviderInterface
     */
    protected $subject;

    /**
     * Constructor.
     *
     * @param \Phauthentic\Authorization\AuthorizationServiceInterface|\Phauthentic\Authorization\AuthorizationServiceProviderInterface $subject Authorization service or provider instance.
     * @param array $config Config array.
     * @throws \InvalidArgumentException
     */
    public function __construct($subject, array $config = [])
    {
        if (!$subject instanceof AuthorizationServiceInterface && !$subject instanceof AuthorizationServiceProviderInterface) {
            $expected = implode('` or `', [
                AuthorizationServiceInterface::class,
                AuthorizationServiceProviderInterface::class
            ]);
            $type = is_object($subject) ? get_class($subject) : gettype($subject);
            $message = sprintf('Subject must be an instance of `%s`, `%s` given.', $expected, $type);

            throw new InvalidArgumentException($message);
        }

        $this->subject = $subject;
        $this->setConfig($config);
    }

    /**
     * Callable implementation for the middleware stack.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request Server request.
     * @param \Psr\Http\Message\ResponseInterface $response Response.
     * @param callable $next The next middleware to call.
     * @return ResponseInterface A response.
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, $next)
    {
        $service = $this->getAuthorizationService($request, $response);
        $request = $request->withAttribute('authorization', $service);

        $attribute = $this->getConfig('identityAttribute');
        $identity = $request->getAttribute($attribute);

        if ($identity !== null) {
            $identity = $this->buildIdentity($service, $identity);
            $request = $request->withAttribute($attribute, $identity);
        }

        try {
            $response = $next($request, $response);
            if ($this->getConfig('requireAuthorizationCheck') && !$service->authorizationChecked()) {
                throw new AuthorizationRequiredException(['url' => $request->getRequestTarget()]);
            }
        } catch (Exception $exception) {
            $handler = $this->getHandler();
            $response = $handler->handle($exception, $request, $response, (array)$this->getConfig('unauthorizedHandler'));
        }

        return $response;
    }

    /**
     * Returns unauthorized handler.
     *
     * @return \Phauthentic\Authorization\Middleware\UnauthorizedHandler\HandlerInterface
     */
    protected function getHandler()
    {
        $handler = $this->getConfig('unauthorizedHandler');
        if (!is_array($handler)) {
            $handler = [
                'className' => $handler,
            ];
        }
        if (!isset($handler['className'])) {
            throw new RuntimeException('Missing `className` key from handler config.');
        }

        return HandlerFactory::create($handler['className']);
    }

    /**
     * Returns AuthorizationServiceInterface instance.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request Server request.
     * @param \Psr\Http\Message\ResponseInterface $response Response.
     * @return \Phauthentic\Authorization\AuthorizationServiceInterface
     * @throws \RuntimeException When authorization method has not been defined.
     */
    protected function getAuthorizationService($request, $response)
    {
        $service = $this->subject;
        if ($this->subject instanceof AuthorizationServiceProviderInterface) {
            $service = $this->subject->getAuthorizationService($request, $response);
        }

        if (!$service instanceof AuthorizationServiceInterface) {
            throw new RuntimeException(sprintf(
                'Invalid service returned from the provider. `%s` does not implement `%s`.',
                is_object($service) ? get_class($service) : gettype($service),
                AuthorizationServiceInterface::class
            ));
        }

        return $service;
    }

    /**
     * Builds the identity object.
     *
     * @param \Phauthentic\Authorization\AuthorizationServiceInterface $service Authorization service.
     * @param \ArrayAccess|array $identity Identity data
     * @return \Phauthentic\Authorization\IdentityInterface
     */
    protected function buildIdentity(AuthorizationServiceInterface $service, $identity)
    {
        $class = $this->getConfig('identityDecorator');

        if (is_callable($class)) {
            $identity = $class($service, $identity);
        } else {
            if (!$identity instanceof IdentityInterface) {
                $identity = new $class($service, $identity);
            }
        }

        if (!$identity instanceof IdentityInterface) {
            throw new RuntimeException(sprintf(
                'Invalid identity returned by decorator. `%s` does not implement `%s`.',
                is_object($identity) ? get_class($identity) : gettype($identity),
                IdentityInterface::class
            ));
        }

        return $identity;
    }
}
