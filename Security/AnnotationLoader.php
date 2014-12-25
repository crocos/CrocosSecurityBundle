<?php

namespace Crocos\SecurityBundle\Security;

use Doctrine\Common\Annotations\Reader;
use Crocos\SecurityBundle\Annotation\Annotation;
use Crocos\SecurityBundle\Annotation\Secure;
use Crocos\SecurityBundle\Annotation\SecureConfig;
use Crocos\SecurityBundle\Security\AuthLogic\AuthLogicResolver;
use Crocos\SecurityBundle\Security\Role\RoleManagerResolver;
use Crocos\SecurityBundle\Security\HttpAuth\HttpAuthFactoryInterface;

/**
 * AnnotationLoader.
 *
 * @author Katsuhiro Ogawa <ogawa@crocos.co.jp>
 */
class AnnotationLoader
{
    const DEFAULT_AUTH_LOGIC = 'session';
    const DEFAULT_ROLE_MANAGER = 'session';

    /**
     * @var Reader
     */
    protected $reader;

    /**
     * @var AuthLogicResolver
     */
    protected $resolver;

    /**
     * @var RoleManagerResolver
     */
    protected $roleManagerResolver;

    /**
     * @var HttpAuthFactoryInterface
     */
    protected $httpAuthFactory;

    /**
     * @var ParameterResolverInterface
     */
    protected $parameterResolver;

    /**
     * Constructor.
     *
     * @param Reader                   $reader              Annotation reader
     * @param AuthLogicResolver        $resolver
     * @param RoleManagerResolver      $roleManagerResolver
     * @param HttpAuthFactoryInterface $httpAuthFactory
     */
    public function __construct(Reader $reader, AuthLogicResolver $resolver, RoleManagerResolver $roleManagerResolver)
    {
        $this->reader = $reader;
        $this->resolver = $resolver;
        $this->roleManagerResolver = $roleManagerResolver;
    }

    /**
     * @param HttpAuthFactoryInterface $httpAuthFactory
     */
    public function setHttpAuthFactory(HttpAuthFactoryInterface $httpAuthFactory = null)
    {
        $this->httpAuthFactory = $httpAuthFactory;
    }

    /**
     * @param ParameterResolverInterface $parameterResolver
     */
    public function setParameterResolver(ParameterResolverInterface $parameterResolver)
    {
        $this->parameterResolver = $parameterResolver;
    }

    /**
     * Read security annotation.
     *
     * @param SecurityContext   $context
     * @param \ReflectionClass  $class
     * @param \ReflectionMethod $method
     */
    public function load(SecurityContext $context, \ReflectionClass $class, \ReflectionMethod $method)
    {
        // Retrieve all ancestors (parent first)
        $klass = $class;
        $classes = [$klass];
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
     * @param Annotation      $annotation
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
     * @param Secure          $annotation
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
     * @param SecureConfig    $annotation
     */
    protected function loadSecureConfigAnnotation(SecurityContext $context, SecureConfig $annotation)
    {
        if (null !== $annotation->domain()) {
            $context->setDomain($annotation->domain());
        }

        if (null !== $annotation->httpsRequired()) {
            $context->setHttpsRequired($this->resolveParameter($annotation->httpsRequired()));
        }

        if (null !== $annotation->options()) {
            $context->setOptions($this->resolveParameter($annotation->options()));
        }

        if (null !== $annotation->auth()) {
            $context->setAuthLogic($this->resolveParameter($this->resolver->resolveAuthLogic($annotation->auth())));
        }

        if (null !== $annotation->roleManager()) {
            $context->setRoleManager($this->roleManagerResolver->resolveRoleManager($this->resolveParameter($annotation->roleManager())));
        }

        if (null !== $annotation->forward()) {
            $context->setForwardingController($this->resolveParameter($annotation->forward()));
        }

        if (null !== $annotation->basic()) {
            $this->loadHttpAuth($context, 'basic', $this->resolveParameter($annotation->basic()));
        }
    }

    /**
     * Fix security context.
     *
     * @param SecurityContext $context
     */
    protected function fixContext(SecurityContext $context)
    {
        if (null === $context->getAuthLogic()) {
            $context->setAuthLogic($this->resolver->resolveAuthLogic(self::DEFAULT_AUTH_LOGIC));
        }

        if (null === $context->getRoleManager()) {
            $context->setRoleManager($this->roleManagerResolver->resolveRoleManager(self::DEFAULT_ROLE_MANAGER));
        }

        if ($context->getAuthLogic() instanceof SecureOptionsAcceptableInterface) {
            $context->getAuthLogic()->setOptions($context->getOptions());
        }

        $context->fixDomain();
    }

    /**
     * Load http auth.
     *
     * @param SecurityContext $context
     * @param string          $type
     * @param string          $value
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

    /**
     * Parse parameter.
     *
     * @param  string $value
     * @return string
     */
    protected function resolveParameter($value)
    {
        if ($this->parameterResolver === null) {
            return $value;
        }

        if (!is_string($value)) {
            return $value;
        }

        return $this->parameterResolver->resolveValue($value);
    }
}
