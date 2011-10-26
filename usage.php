<?php

/**
 * @Secure(disabled=true, domain="default", forward="MyBundle:Account:login")
 */
abstract class Controller
{
}

class ProductController extends Controller
{
    public function showAction($id)
    {
    }
}

/**
 * @Secure
 */
class AccountController extends Controller
{
    /**
     * @Route("/account")
     * @Template
     */
    public function indexAction()
    {
        $user = $this->get('crocos_security.context')->getUser();

        return array('user' => $user);
    }

    /**
     * @Route("/login")
     * @Secure(disabled=true)
     * @Template
     */
    public function loginAction(Request $request)
    {
        if ($request->getMethod() === 'POST') {
            $username = $request->request->get('username');
            $password = $request->request->get('password');
            $user = $this->findUser($username, $password);

            $this->get('crocos_security.context')->login($user);

            return $this->redirect('/');
        }

        return array();
    }
}

/**
 * @Secure(strategy=facebook, forward="MyBundle:Facebook:login")
 * @Route("/facebook")
 */
class FacebookController extends Controller
{
    /**
     * @Secure("/dispatch")
     */
    public function dispatchAction()
    {
    }
}
