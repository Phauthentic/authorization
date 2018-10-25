<?php
namespace TestApp;

use Phauthentic\Authorization\AuthorizationServiceInterface;
use Phauthentic\Authorization\IdentityDecorator;

class Identity extends IdentityDecorator
{
    public function __construct($identity)
    {
        $this->identity = $identity;
    }

    public function setService(AuthorizationServiceInterface $service)
    {
        $this->authorization = $service;
    }

    public function getService()
    {
        return $this->authorization;
    }
}
