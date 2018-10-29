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
namespace Authorization\Test\TestCase\Policy;

use Phauthentic\Authorization\Policy\Exception\MissingPolicyException;
use Phauthentic\Authorization\Policy\MapResolver;
use Phauthentic\Authorization\Policy\ResolverCollection;
use Phauthentic\Authorization\Policy\ResolverInterface;
use PHPUnit\Framework\TestCase;
use TestApp\Model\Entity\Article;
use TestApp\Policy\ArticlePolicy;

/**
 * ResolverCollectionTest
 */
class ResolverCollectionTest extends TestCase
{
    /**
     * testCount
     *
     * @return void
     */
    public function testCount(): void
    {
        $resolver = new ResolverCollection([
            new MapResolver()
        ]);

        $this->assertCount(1, $resolver);
    }
}
