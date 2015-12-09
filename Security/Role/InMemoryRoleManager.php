<?php
namespace Crocos\SecurityBundle\Security\Role;

/**
 * InMemoryRoleManager.
 *
 * @author Toshiyuki Fujita <tofujiit@crocos.co.jp>
 */
class InMemoryRoleManager extends AbstractRoleManager
{
    /**
     * @var array
     */
    protected $attributes = [];

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->attributes = [];
    }

    /**
     * {@inheritdoc}
     */
    protected function setAttribute($key, $value)
    {
        $this->attributes[$this->domain][$key] = $value;
    }

    /**
     * {@inheritdoc}
     */
    protected function getAttribute($key, $default = null)
    {
        return isset($this->attributes[$this->domain][$key]) ?
            $this->attributes[$this->domain][$key] : $default;
    }
}
