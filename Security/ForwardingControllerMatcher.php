<?php
namespace Crocos\SecurityBundle\Security;

use Symfony\Bundle\FrameworkBundle\Controller\ControllerNameParser;

/**
 * ForwardingControllerMatcher.
 *
 * @author Katsuhiro Ogawa <ogawa@crocos.co.jp>
 */
class ForwardingControllerMatcher
{
    /**
     * @var ControllerNameParser
     */
    protected $parser;

    /**
     * Constructor.
     *
     * @param ControllerNameParser $parser
     */
    public function __construct(ControllerNameParser $parser)
    {
        $this->parser = $parser;
    }

    /**
     * Check given controller is a forwarding controller.
     *
     * @param SecurityContext   $context
     * @param \ReflectionClass  $class
     * @param \ReflectionMethod $method
     *
     * @return bool
     */
    public function isForwardingController(SecurityContext $context, \ReflectionClass $class, \ReflectionMethod $method)
    {
        $forwardingController = $context->getForwardingController();
        if (null === $forwardingController) {
            return false;
        }

        if (false === strpos($forwardingController, '::')) {
            $forwardingController = $this->parser->parse($forwardingController);
        }

        list($forwardingClass, $forwardingMethod) = explode('::', $forwardingController);

        if ($forwardingClass === $class->name && $forwardingMethod === $method->name) {
            return true;
        }

        return false;
    }
}
