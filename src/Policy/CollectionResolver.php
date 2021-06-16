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

declare(strict_types=1);

namespace Phauthentic\Authorization\Policy;

use InvalidArgumentException;
use Phauthentic\Authorization\Policy\Exception\MissingPolicyException;

/**
 * `ResolverCollection` is used for aggregating multiple resolvers when more than
 * one resolver is necessary. The collection will iterate over configured resolvers
 * and try to resolve a policy on each one. The first successfully resolved policy
 * will be returned.
 *
 * Configured resolvers must throw `Authorization\Policy\Exception\MissingPolicyException`
 * if a policy cannot be resolved.
 *
 * Example configuration:
 *
 * ```
 * $collection = new ResolverCollection([
 *     new OrmResolver(),
 *     new MapResolver([
 *         Service::class => ServicePolicy::class
 *     ])
 * ]);
 *
 * $service = new AuthenticationService($collection);
 * ```
 */
class CollectionResolver implements ResolverInterface
{
    /**
     * Policy resolver instances.
     *
     * @var \Phauthentic\Authorization\Policy\ResolverCollectionInterface
     */
    protected $collection;

    /**
     * Constructor. Takes an array of policy resolver instances.
     *
     * @param \Phauthentic\Authorization\Policy\ResolverCollectionInterface $collection
     */
    public function __construct(ResolverCollectionInterface $collection)
    {
        $this->collection = $collection;
    }

    /**
     * {@inheritDoc}
     */
    public function getPolicy($resource)
    {
        foreach ($this->collection as $resolver) {
            try {
                return $resolver->getPolicy($resource);
            } catch (MissingPolicyException $e) {
                // Ignore this case because we'll try the next resolver
            } catch (InvalidArgumentException $e) {
            }
        }

        $class = get_class($resource);
        $message = sprintf('Policy for `%s` has not been defined.', $class);

        throw new MissingPolicyException($message);
    }
}
