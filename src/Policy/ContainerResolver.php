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

use InvalidArgumentException;
use Phauthentic\Authorization\Policy\Exception\MissingPolicyException;
use Psr\Container\ContainerInterface;

/**
 * Container Resolver
 */
class ContainerResolver implements ResolverInterface
{
    /**
     * Container
     *
     * @var \Psr\Container\ContainerInterface
     */
    protected $container;

    /**
     * Constructor
     *
     * @param \Psr\Container\ContainerInterface $container PSR Container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     *
     * @throws \InvalidArgumentException When a resource is not a string.
     * @throws \Phauthentic\Authorization\Policy\Exception\MissingPolicyException When a policy for a resource has not been defined.
     */
    public function getPolicy($resource)
    {
        if (!is_string($resource)) {
            $message = sprintf('Resource must be a string, `%s` given.', gettype($resource));

            throw new InvalidArgumentException($message);
        }

        if (!$this->container->has($resource)) {
            throw new MissingPolicyException(sprintf('Policy for `%s` has not been defined.', $resource));
        }

        return $this->container->get($resource);
    }
}
