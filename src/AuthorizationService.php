<?php
declare(strict_types = 1);
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
namespace Phauthentic\Authorization;

use Phauthentic\Authorization\Policy\BeforePolicyInterface;
use Phauthentic\Authorization\Policy\Exception\MissingMethodException;
use Phauthentic\Authorization\Policy\ResolverInterface;
use Phauthentic\Authorization\Policy\Result;
use Phauthentic\Authorization\Policy\ResultInterface;
use RuntimeException;

/**
 * Authorization Service
 */
class AuthorizationService implements AuthorizationServiceInterface
{
    /**
     * Authorization policy resolver.
     *
     * @var \Phauthentic\Authorization\Policy\ResolverInterface
     */
    protected $resolver;

    /**
     * Track whether or not authorization was checked.
     *
     * @var bool
     */
    protected $authorizationChecked = false;

    /**
     * Constructor
     *
     * @param \Phauthentic\Authorization\Policy\ResolverInterface $resolver Authorization policy resolver.
     */
    public function __construct(ResolverInterface $resolver)
    {
        $this->resolver = $resolver;
    }

    /**
     * {@inheritDoc}
     */
    public function can(?IdentityInterface $user, string $action, $resource): ResultInterface
    {
        $this->authorizationChecked = true;
        $policy = $this->resolver->getPolicy($resource);

        if ($policy instanceof BeforePolicyInterface) {
            $result = $policy->before($user, $resource, $action);

            if (is_bool($result)) {
                return new Result($result);
            }

            if ($result instanceof ResultInterface) {
                return $result;
            }

            if ($result !== null) {
                throw new RuntimeException(sprintf(
                    'Pre-authorization check must return instance of `%s`, `bool` or `null`.',
                    ResultInterface::class
                ));
            }
        }

        $handler = $this->getCanHandler($policy, $action);
        $result = $handler($user, $resource);

        if (is_bool($result)) {
            return new Result($result);
        }

        if ($result instanceof ResultInterface) {
            return $result;
        }

        throw new RuntimeException(sprintf(
            'Policy action handler must return instance of `%s` or `bool`.',
            ResultInterface::class
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function applyScope(?IdentityInterface $user, string $action, $resource)
    {
        $this->authorizationChecked = true;
        $policy = $this->resolver->getPolicy($resource);
        $handler = $this->getScopeHandler($policy, $action);

        return $handler($user, $resource);
    }

    /**
     * Returns a policy action handler.
     *
     * @param mixed $policy Policy object.
     * @param string $action Action name.
     * @return callable
     * @throws \Phauthentic\Authorization\Policy\Exception\MissingMethodException
     */
    protected function getCanHandler($policy, $action): callable
    {
        $method = 'can' . ucfirst($action);

        if (!method_exists($policy, $method) && !method_exists($policy, '__call')) {
            throw (new MissingMethodException())->setMessageVars([$method, $action, get_class($policy)]);
        }

        return [$policy, $method];
    }

    /**
     * Returns a policy scope action handler.
     *
     * @param mixed $policy Policy object.
     * @param string $action Action name.
     * @return callable
     * @throws \Phauthentic\Authorization\Policy\Exception\MissingMethodException
     */
    protected function getScopeHandler($policy, $action): callable
    {
        $method = 'scope' . ucfirst($action);

        if (!method_exists($policy, $method)) {
            throw (new MissingMethodException())->setMessageVars([$method, $action, get_class($policy)]);
        }

        return [$policy, $method];
    }

    /**
     * {@inheritDoc}
     */
    public function authorizationChecked(): bool
    {
        return $this->authorizationChecked;
    }

    /**
     * {@inheritDoc}
     */
    public function skipAuthorization(): AuthorizationServiceInterface
    {
        $this->authorizationChecked = true;

        return $this;
    }
}
