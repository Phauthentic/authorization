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
namespace Phauthentic\Authorization;

use ArrayAccess;
use BadMethodCallException;
use InvalidArgumentException;

/**
 * An decorator implementing the IdentityInterface.
 *
 * This decorator is intended to wrap the application defined identity
 * object and proxy attributes/methods to and 'mixin' the can() method.
 *
 * The decorated identity must implement ArrayAccess or be an array.
 */
class IdentityDecorator implements IdentityInterface
{
    /**
     * Identity data
     *
     * @var array|\ArrayAccess
     */
    protected $identity;

    /**
     * Authorization Service
     *
     * @var \Phauthentic\Authorization\AuthorizationServiceInterface
     */
    protected $authorization;

    /**
     * Constructor
     *
     * @param \Phauthentic\Authorization\AuthorizationServiceInterface $service The authorization service.
     * @param array|\ArrayAccess $identity Identity data
     * @throws \InvalidArgumentException When invalid identity data is passed.
     */
    public function __construct(AuthorizationServiceInterface $service, $identity)
    {
        if (!is_array($identity) && !$identity instanceof ArrayAccess) {
            $type = is_object($identity) ? get_class($identity) : gettype($identity);
            $message = sprintf('Identity data must be an `array` or implement `ArrayAccess` interface, `%s` given.', $type);
            throw new InvalidArgumentException($message);
        }

        $this->authorization = $service;
        $this->identity = $identity;
    }

    /**
     * {@inheritDoc}
     */
    public function can($action, $resource): bool
    {
        return $this->authorization->can($this, $action, $resource);
    }

    /**
     * {@inheritDoc}
     */
    public function applyScope($action, $resource)
    {
        return $this->authorization->applyScope($this, $action, $resource);
    }

    /**
     * {@inheritdoc}
     */
    public function getOriginalData()
    {
        if ($this->identity && method_exists($this->identity, 'getOriginalData')) {
            return $this->identity->getOriginalData();
        }

        return $this->identity;
    }

    /**
     * Delegate unknown methods to decorated identity.
     *
     * @param string $method The method being invoked.
     * @param array $args The arguments for the method.
     * @return mixed
     */
    public function __call($method, $args)
    {
        if (!is_object($this->identity)) {
            throw new BadMethodCallException("Cannot call `{$method}`. Identity data is not an object.");
        }
        $call = [$this->identity, $method];

        return $call(...$args);
    }

    /**
     * Delegate property access to decorated identity.
     *
     * @param string $property The property to read.
     * @return mixed
     */
    public function __get($property)
    {
        return $this->identity->{$property};
    }

    /**
     * Delegate property isset to decorated identity.
     *
     * @param string $property The property to read.
     * @return mixed
     */
    public function __isset($property)
    {
        return isset($this->identity->{$property});
    }

    /**
     * Whether a offset exists
     *
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset Offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->identity[$offset]);
    }

    /**
     * Offset to retrieve
     *
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset Offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        if (isset($this->identity[$offset])) {
            return $this->identity[$offset];
        }

        return null;
    }

    /**
     * Offset to set
     *
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset The offset to assign the value to.
     * @param mixed $value Value
     * @return mixed
     */
    public function offsetSet($offset, $value)
    {
        return $this->identity[$offset] = $value;
    }

    /**
     * Offset to unset
     *
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset Offset
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->identity[$offset]);
    }
}
