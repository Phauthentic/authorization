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

use Phauthentic\Authorization\IdentityInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * This interface should be implemented by your request policy class.
 */
interface RequestPolicyInterface
{
    /**
     * Method to check if the request can be accessed
     *
     * @param \Phauthentic\Authorization\IdentityInterface|null $identity Identity
     * @param \Psr\Http\Message\ServerRequestInterface $request Server Request
     * @return \Phauthentic\Authorization\Policy\ResultInterface
     */
    public function canAccess(?IdentityInterface $identity, ServerRequestInterface $request): ResultInterface;
}
