<?php

namespace Crocos\SecurityBundle\Security;

use Doctrine\Common\Annotations\Reader;
use Crocos\SecurityBundle\Annotation\Annotation;
use Crocos\SecurityBundle\Annotation\Secure;
use Crocos\SecurityBundle\Annotation\SecureConfig;
use Crocos\SecurityBundle\Security\AuthLogic\AuthLogicResolver;
use Crocos\SecurityBundle\Security\HttpAuth\HttpAuthFactoryInterface;

/**
 * AnnotationLoader.
 *
 * @author Katsuhiro Ogawa <ogawa@crocos.co.jp>
 */
class AnnotationLoader
{
    const DEFAULT_AUTH_LOGIC = 'session';

    /**
     * @var Reader
     */
    protected $reader;

    /**
     * @var AuthLogicResolver
     */
    protected $resolver;

    /**
     * @var HttpAuthFactoryInterface
     */
    protected $httpAuthFactory;

    /**
     * Constructor.
     *
     * @param Reader $reader Annotation reader
     * @param AuthLogicResolver $resolver
     */
    public function __construct(Reader $reader, AuthLogicResolver $resolver, HttpAuthFactoryInterface $httpAuthFactory = null)
    {
        $this->reader = $reader;
        $this->resolver = $resolver;
        $this->httpAuthFactory = $httpAuthFactory;
    }

    /**
     * Read security annotation.
     *
     * @param SecurityContext $context
     * @param \ReflectionClass $class
     * @param \ReflectionMethod $method
     */
    public function load(SecurityContext $context, \ReflectionClass $class, \ReflectionMethod $method)
    {
        // Retrieve all ancestors (parent first)
        $klass = $class;
        $classes = array($klass);
        while ($klass = $klass->getParentClass()) {
            $classes[] = $klass;
        }

        // Read class annotations.
        $classes = array_reverse($classes);
        foreach ($classes as $class) {
            $annotations = $this->reader->getClassAnnotations($class);
            if (count($annotations) > 0) {
                foreach ($annotations as $annotation) {
                    if ($annotation instanceof Annotation) {
                        $this->loadAnnotation($context, $annotation);
                    }
                }
            }
        }

        // Read method annotations.
        $annotations = $this->reader->getMethodAnnotations($method);
        if (count($annotations) > 0) {
            foreach ($annotations as $annotation) {
                if ($annotation instanceof Annotation) {
                    $this->loadAnnotation($context, $annotation);
                }
            }
        }

        $this->fixContext($context);
    }

    /**
     * Load security annotation.
     *
     * @param SecurityContext $context
     * @param Annotation $annotation
     */
    protected function loadAnnotation(SecurityContext $context, Annotation $annotation)
    {
        if ($annotation instanceof Secure) {
            $this->loadSecureAnnotation($context, $annotation);
        } elseif ($annotation instanceof SecureConfig) {
            $this->loadSecureConfigAnnotation($context, $annotation);
        }
    }

    /**
     * Load @Secure annotation.
     *
     * @param SecurityContext $context
     * @param Secure $annotation
     */
    protected function loadSecureAnnotation(SecurityContext $context, Secure $annotation)
    {
        $context->setSecure(!$annotation->disabled());

        if (null !== $annotation->allow()) {
            $context->setAllowedRoles($annotation->allow());
        }
    }

    /**
     * Load @SecureConfig annotation.
     *
     * @param SecurityContext $context
     * @param SecureConfig $annotation
     */
    protected function loadSecureConfigAnnotation(SecurityContext $context, SecureConfig $annotation)
    {
        if (null !== $annotation->domain()) {
            $context->setDomain($annotation->domain());
        }

        if (null !== $annotation->auth()) {
            $context->setAuthLogic($this->resolver->resolveAuthLogic($annotation->auth()));
        }

        if (null !== $annotation->forward()) {
            $context->setForwardingController($annotation->forward());
        }

        if (null !== $annotation->basic()) {
            $this->loadHttpAuth($context, 'basic', $annotation->basic());
        }
    }

    /**
     * Fix security context.
     *
     * @param SecurityContext $context
     */
    protected function fixContext(SecurityContext $context)
    {
        $context->getPreviousUrlHolder()->setup($context->getDomain());

        if (null === $context->getAuthLogic()) {
            $context->setAuthLogic($this->resolver->resolveAuthLogic(self::DEFAULT_AUTH_LOGIC));
        }

        $context->getAuthLogic()->setDomain($context->getDomain());
    }

    /**
     * Load http auth.
     *
     * @param SecurityContext $context
     * @param string $type
     * @param string $value
     *
     * @see HttpAuthFactoryInterface
     */
    protected function loadHttpAuth(SecurityContext $context, $type, $value)
    {
        if (null === $this->httpAuthFactory) {
            return;
        }

        $httpAuth = $this->httpAuthFactory->create($type, $value, $context->getDomain());

        $context->setHttpAuth($httpAuth);
    }
}
