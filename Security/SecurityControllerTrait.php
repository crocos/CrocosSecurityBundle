<?php
namespace Crocos\SecurityBundle\Security;

trait SecurityControllerTrait
{
    /**
     * @see SecurityContext
     */
    protected function isAuthenticated()
    {
        return $this->get('crocos_security.context')->isAuthenticated();
    }

    /**
     * @see SecurityContext
     */
    protected function login($user)
    {
        $this->get('crocos_security.context')->login($user);
    }

    /**
     * @see SecurityContext
     */
    protected function logout()
    {
        $this->get('crocos_security.context')->logout();
    }

    /**
     * @see SecurityContext
     */
    protected function getUser()
    {
        return $this->get('crocos_security.context')->getUser();
    }
}
