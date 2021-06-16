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

namespace Phauthentic\Authorization\Policy;

use Phauthentic\Authorization\Policy\Exception\MissingPolicyException;
use InvalidArgumentException;

/**
 * Policy resolver that allows to map policy classes, objects or factories to
 * individual resource classes.
 */
class MapResolver implements ResolverInterface
{
    /**
     * Resource to policy class name map.
     *
     * @var array
     */
    protected $map = [];

    /**
     * Constructor.
     *
     * Takes a resource class name as a key and a policy as a value, for example:
     * ```
     * [
     *     \App\Service\Resource1::class => \App\Policy\ResourcePolicy::class,
     *     \App\Service\Resource2::class => $policyObject,
     *     \App\Service\Resource3::class => function() {},
     * ]
     * ```
     *
     * @param array $map Resource class name to policy map.
     */
    public function __construct(array $map = [])
    {
        foreach ($map as $resourceClass => $policy) {
            $this->map($resourceClass, $policy);
        }
    }

    /**
     * Maps a resource class to the policy class name.
     *
     * @param string $resourceClass A resource class name.
     * @param string|object|callable $policy A policy class name, an object or a callable factory.
     * @return $this
     * @throws \InvalidArgumentException When a resource class does not exist or policy is invalid.
     */
    public function map($resourceClass, $policy): ResolverInterface
    {
        $this->resourceClassExists($resourceClass);
        $this->validatePolicyObject($policy);
        $this->policyClassExists($policy);
        $this->map[$resourceClass] = $policy;

        return $this;
    }

    /**
     * Checks if the policy class exists
     *
     * @param string|object|callable $policy A policy class name, an object or a callable factory.
     * @return void
     */
    protected function policyClassExists($policy): void
    {
        if (is_string($policy) && !class_exists($policy)) {
            $message = sprintf('Policy class `%s` does not exist.', $policy);
            throw new InvalidArgumentException($message);
        }
    }

    /**
     * Checks if the resource class exists
     *
     * @param string $resourceClass A resource class name.
     * @return void
     */
    protected function resourceClassExists($resourceClass): void
    {
        if (!class_exists($resourceClass)) {
            $message = sprintf('Resource class `%s` does not exist.', $resourceClass);
            throw new InvalidArgumentException($message);
        }
    }

    /**
     * Checks if the policy variable is of a valid type
     *
     * @param string|object|callable $policy
     * @return void
     */
    protected function validatePolicyObject($policy): void
    {
        if (!is_string($policy) && !is_object($policy) && !is_callable($policy)) {
            $message = sprintf(
                'Policy must be a valid class name, an object or a callable, `%s` given.',
                gettype($policy)
            );
            throw new InvalidArgumentException($message);
        }
    }

    /**
     * {@inheritDoc}
     *
     * @throws \InvalidArgumentException When a resource is not an object.
     * @throws \Phauthentic\Authorization\Policy\Exception\MissingPolicyException When a policy for a resource has not been defined.
     */
    public function getPolicy($resource)
    {
        if (!is_object($resource)) {
            $message = sprintf('Resource must be an object, `%s` given.', gettype($resource));
            throw new InvalidArgumentException($message);
        }

        $class = get_class($resource);

        if (!isset($this->map[$class])) {
            throw new MissingPolicyException(sprintf('Policy for `%s` has not been defined.', $class));
        }

        $policy = $this->map[$class];

        if (is_callable($policy)) {
            return $policy($resource, $this);
        }

        if (is_object($policy)) {
            return $policy;
        }

        return new $policy();
    }
}
