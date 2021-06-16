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

use Phauthentic\Authorization\Policy\Result;
use PHPUnit\Framework\TestCase;

/**
 * Result TEst
 */
class ResultTest extends TestCase
{
    /**
     * testGetPolicy
     *
     * @return void
     */
    public function testGetPolicy(): void
    {
        $result = new Result(false, 'Failed');
        $this->assertFalse($result->getStatus());
        $this->assertEquals('Failed', $result->getReason());
    }
}
