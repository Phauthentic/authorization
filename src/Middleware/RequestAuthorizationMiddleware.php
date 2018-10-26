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
use Phauthentic\Authorization\Exception\ForbiddenException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;

/**
 * Request Authorization Middleware
 *
 * This MUST be added after the Authorization, Authentication and
 * RoutingMiddleware in the Middleware Queue!
 *
 * This middleware is useful when you want to authorize your requests, for example
 * each controller and action, against a role based access system or any other
 * kind of authorization process that controls access to certain actions.
 */
class RequestAuthorizationMiddleware
{
    /**
     * @var string
     */
    protected $authorizationAttribute = 'authorization';

    /**
     * @var string
     */
    protected $identityAttribute = 'identity';

    /**
     * @var string
     */
    protected $method = 'access';

    /**
     * Gets the authorization service from the request attribute
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request Server request.
     * @return \Phauthentic\Authorization\AuthorizationServiceInterface
     */
    protected function getServiceFromRequest(ServerRequestInterface $request)
    {
        $service = ($request->getAttribute($this->authorizationAttribute));

        if (!$service instanceof AuthorizationServiceInterface) {
            $errorMessage = __CLASS__ . ' could not find the authorization service in the request attribute. ' .
                'Make sure you added the AuthorizationMiddleware before this middleware or that you ' .
                'somehow else added the service to the requests `' . $this->authorizationAttribute . '` attribute.';

            throw new RuntimeException($errorMessage);
        }

        return $service;
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
        $service = $this->getServiceFromRequest($request);
        $identity = $request->getAttribute($this->identityAttribute);

        if (!$service->can($identity, $this->method, $request)) {
            throw new ForbiddenException();
        }

        return $next($request, $response);
    }

    public function setAuthorizationAttribute(string $attributeName): self
    {
        $this->authorizationAttribute = $attributeName;

        return $this;
    }

    public function setIdentityAttribute(string $attributeName): self
    {
        $this->identityAttribute = $attributeName;

        return $this;
    }

    public function setMethod(string $method): self
    {
        $this->method = $method;

        return $this;
    }
}
