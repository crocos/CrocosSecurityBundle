<?php
namespace Crocos\SecurityBundle\Security\HttpAuth;

use Crocos\SecurityBundle\Exception\HttpAuthException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * HttpAuthInterface.
 *
 * @author Katsuhiro Ogawa <ogawa@crocos.co.jp>
 */
interface HttpAuthInterface
{
    /**
     * Authenticate request.
     *
     * @param Request $request
     *
     * @return bool
     */
    public function authenticate(Request $request);

    /**
     * Create 401 Unauthorized response.
     *
     * @param Request           $request
     * @param HttpAuthException $exception
     *
     * @return Response
     */
    public function createUnauthorizedResponse(Request $request, HttpAuthException $exception);
}
