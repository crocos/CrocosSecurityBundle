<?php

namespace Crocos\SecurityBundle\Tests\Fixtures;

class AdvancedUser extends User
{
    public function isEnabled()
    {
        return true;
    }
}
