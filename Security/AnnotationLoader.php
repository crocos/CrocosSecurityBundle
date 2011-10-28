<?php

namespace Crocos\SecurityBundle\Security;

use Doctrine\Common\Annotations\Reader;
use Crocos\SecurityBundle\Annotation\Annotation;
use Crocos\SecurityBundle\Annotation\Secure;
use Crocos\SecurityBundle\Annotation\SecureConfig;
use Crocos\SecurityBundle\Security\AuthStrategy\AuthStrategyResolver;

/**
 * AnnotationLoader.
 *
 * @author Katsuhiro Ogawa <ogawa@crocos.co.jp>
 */
class AnnotationLoader
{
    /**
     * @var Reader
     */
    protected $reader;

    /**
     * Constructor.
     *
     * @param Reader $reader Annotation reader
     * @param AuthStrategyResolver $resolver
     */
    public function __construct(Reader $reader, AuthStrategyResolver $resolver)
    {
        $this->reader = $reader;
        $this->resolver = $resolver;
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
        $klass = $class;
        $classes = array($klass);
        while ($klass = $klass->getParentClass()) {
            $classes[] = $klass;
        }

        $classes = array_reverse($classes);
        foreach ($classes as $class) {
            foreach ($this->reader->getClassAnnotations($class) as $annotation) {
                if ($annotation instanceof Annotation) {
                    $this->loadAnnotation($context, $annotation);
                }
            }
        }

        foreach ($this->reader->getMethodAnnotations($method) as $annotation) {
            if ($annotation instanceof Annotation) {
                $this->loadAnnotation($context, $annotation);
            }
        }

        $this->resolveAuthStrategy($context);
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

        if (null !== $annotation->roles()) {
            $context->setRequiredRoles($annotation->roles());
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

        if (null !== $annotation->strategy()) {
            $context->setStrategy($annotation->strategy());
        }

        if (null !== $annotation->forward()) {
            $context->setForwardingController($annotation->forward());
        }
    }

    /**
     * Resolve auth strategy.
     *
     * @param SecurityContext $context
     */
    protected function resolveAuthStrategy(SecurityContext $context)
    {
        $strategy = $this->resolver->resolveAuthStrategy($context->getStrategy());

        $strategy->setDomain($context->getDomain());

        $context->setStrategy($strategy);
    }
}
