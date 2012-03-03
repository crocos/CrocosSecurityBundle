<?php

namespace Crocos\SecurityBundle\Security\HttpAuth;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Crocos\SecurityBundle\Exception\HttpAuthException;

/**
 * HttpAuthInterface
 *
 * @author Katsuhiro Ogawa <ogawa@crocos.co.jp>
 */
interface HttpAuthInterface
{
    /**
     * Authenticate request.
     *
     * @param Request $request
     * @return bool
     */
    function authenticate(Request $request);

    /**
     * Create 401 Unauthorized response.
     *
     * @param Request $request
     * @param HttpAuthException $exception
     * @return Response
     */
    function createUnauthorizedResponse(Request $request, HttpAuthException $exception);
}
