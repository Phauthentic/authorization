<?php
namespace TestApp\Model\Entity;

class Entity
{
    protected $properties;

    public function __construct(array $properties = []) {
        $this->properties = $properties;
    }

    public function get(string $name)
    {
        if (isset($this->properties[$name])) {
            return $this->properties[$name];
        }

        return null;
    }
}
