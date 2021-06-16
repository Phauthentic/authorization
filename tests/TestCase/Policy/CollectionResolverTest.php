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

namespace Authorization\Test\TestCase\Policy;

use Phauthentic\Authorization\Policy\CollectionResolver;
use Phauthentic\Authorization\Policy\MapResolver;
use Phauthentic\Authorization\Policy\ResolverCollection;
use PHPUnit\Framework\TestCase;
use TestApp\Model\Entity\Article;
use TestApp\Policy\ArticlePolicy;

/**
 * Collection Resolver Test
 */
class CollectionResolverTest extends TestCase
{
    /**
     * testGetPolicy
     *
     * @return void
     */
    public function testGetPolicy(): void
    {
        $resource = new Article();
        $policy = new ArticlePolicy();
        $resolver1 = new MapResolver();
        $resolver2 = new MapResolver([
            Article::class => $policy
        ]);
        $collection = new ResolverCollection([
            $resolver1,
            $resolver2
        ]);
        $collectionResolver = new CollectionResolver($collection);
        $result = $collectionResolver->getPolicy($resource);
        $this->assertSame($policy, $result);
    }
}
