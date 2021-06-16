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

use Phauthentic\Authorization\Policy\Exception\MissingPolicyException;
use InvalidArgumentException;

/**
 * A resolver that will use a string resource to instantiate the policy based on it
 */
class StringResourceResolver implements ResolverInterface
{
    /**
     * {@inheritDoc}
     *
     * @throws \InvalidArgumentException When a resource is not an object.
     * @throws \Phauthentic\Authorization\Policy\Exception\MissingPolicyException When a policy for a resource has not been defined.
     */
    public function getPolicy($resource)
    {
        if (!is_string($resource)) {
            $message = sprintf('Resource must be a string, `%s` given.', gettype($resource));
            throw new InvalidArgumentException($message);
        }

        if (!class_exists($resource)) {
            throw new MissingPolicyException(sprintf('Policy for `%s` has not been defined.', $resource));
        }

        return new $resource();
    }
}
