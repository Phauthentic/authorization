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

namespace Phauthentic\Authorization;

use Phauthentic\Authorization\Policy\ResultInterface;

/**
 * Interface for Authorization service
 */
interface AuthorizationServiceInterface
{
    /**
     * Check whether the provided user can perform an action on a resource.
     *
     * This method is intended to allow your application to build
     * conditional logic around authorization checks.
     *
     * @param \Phauthentic\Authorization\IdentityInterface|null $user The user to check permissions for.
     * @param string $action The action/operation being performed.
     * @param mixed $resource The resource being operated on.
     * @return \Phauthentic\Authorization\Policy\ResultInterface
     */
    public function can(?IdentityInterface $user, string $action, $resource): ResultInterface;

    /**
     * Apply authorization scope conditions/restrictions.
     *
     * This method is intended for applying authorization to objects that
     * are then used to access authorized collections of objects. The typical
     * use case for scopes are restricting a query to only return records
     * visible to the current user.
     *
     * @param \Phauthentic\Authorization\IdentityInterface|null $user The user to check permissions for.
     * @param string $action The action/operation being performed.
     * @param mixed $resource The resource being operated on.
     * @return mixed The modified resource.
     */
    public function applyScope(?IdentityInterface $user, string $action, $resource);
/**
     * Return a boolean based on whether or not this object
     * has had an authorization operation performed.
     *
     * @return bool
     */
    public function authorizationChecked(): bool;
/**
     * Allow for authorization to be skipped for this object.
     *
     * After calling this method the value of `authorizationChecked()` should
     * return `true` regardless of whether authorization has been performed or not.
     *
     * @return $this
     */
    public function skipAuthorization(): AuthorizationServiceInterface;
}
