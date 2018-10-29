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
namespace Phauthentic\Authorization\Policy;

use IteratorAggregate;

/**
 * ResolverCollectionInterface
 */
interface ResolverCollectionInterface extends IteratorAggregate
{
    /**
     * Adds a resolver to the collection.
     *
     * @param \Phauthentic\Authorization\Policy\ResolverInterface $resolver Resolver instance.
     * @return $this
     */
    public function add(ResolverInterface $resolver): ResolverCollectionInterface;

    /**
     * Gets the count of resolvers in the collection
     *
     * @return int
     */
    public function count(): int;
}
