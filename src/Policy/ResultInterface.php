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
 * @since         1.1.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

declare(strict_types=1);

namespace Phauthentic\Authorization\Policy;

/**
 * Policy check result interface
 */
interface ResultInterface
{
    /**
     * Returns policy check status.
     *
     * @return bool
     */
    public function getStatus(): bool;
/**
     * Optional reason why policy check has failed.
     *
     * @return string|null
     */
    public function getReason(): ?string;
}
