<?php
namespace Crocos\SecurityBundle\Security;

use Crocos\SecurityBundle\Annotation\Annotation;
use Crocos\SecurityBundle\Annotation\Secure;
use Crocos\SecurityBundle\Annotation\SecureConfig;
use Crocos\SecurityBundle\Security\AuthLogic\AuthLogicResolver;
use Crocos\SecurityBundle\Security\HttpAuth\HttpAuthFactoryInterface;
use Crocos\SecurityBundle\Security\Role\RoleManagerResolver;
use Doctrine\Common\Annotations\Reader;
use SplPriorityQueue;

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
     * @var SplPriorityQueue
     */
    protected $httpAuthFactories;

    /**
     * @var ParameterResolverInterface
     */
    protected $parameterResolver;

    /**
     * @param Reader              $reader              Annotation reader
     * @param AuthLogicResolver   $resolver
     * @param RoleManagerResolver $roleManagerResolver
     */
    public function __construct(Reader $reader, AuthLogicResolver $resolver, RoleManagerResolver $roleManagerResolver)
    {
        $this->reader = $reader;
        $this->resolver = $resolver;
        $this->roleManagerResolver = $roleManagerResolver;
        $this->httpAuthFactories = new SplPriorityQueue();
    }

    /**
     * @param HttpAuthFactoryInterface $httpAuthFactory
     */
    public function addHttpAuthFactory(HttpAuthFactoryInterface $httpAuthFactory)
    {
        $this->httpAuthFactories->insert($httpAuthFactory, $httpAuthFactory->getPriority());
    }

    /**
     * @param ParameterResolverInterface $parameterResolver
     */
    public function setParameterResolver(ParameterResolverInterface $parameterResolver)
    {
        $this->parameterResolver = $parameterResolver;
    }

    /**
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

        // Read class annotations
        $classes = array_reverse($classes);
        foreach ($classes as $class) {
            $classAnnotations = $this->reader->getClassAnnotations($class);
            if (count($classAnnotations) > 0) {
                foreach ($classAnnotations as $annotation) {
                    if ($annotation instanceof Annotation) {
                        $this->loadAnnotation($context, $annotation);
                    }
                }
            }
        }

        // Read method annotations
        $methodAnnotations = $this->reader->getMethodAnnotations($method);
        if (count($methodAnnotations) > 0) {
            foreach ($methodAnnotations as $annotation) {
                if ($annotation instanceof Annotation) {
                    $this->loadAnnotation($context, $annotation);
                }
            }
        }

        $this->fixContext($context);
    }

    /**
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
            $context->setHttpsRequired($annotation->httpsRequired());
        }

        if (null !== $annotation->options()) {
            $context->setOptions($this->resolveParameter($annotation->options()));
        }

        if (null !== $annotation->auth()) {
            $context->setAuthLogic($this->resolver->resolveAuthLogic($this->resolveParameter($annotation->auth())));
        }

        if (null !== $annotation->roleManager()) {
            $context->setRoleManager($this->roleManagerResolver->resolveRoleManager($this->resolveParameter($annotation->roleManager())));
        }

        if (null !== $annotation->forward()) {
            $context->setForwardingController($this->resolveParameter($annotation->forward()));
        }

        $this->loadHttpAuth($context, $annotation);
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
     * @param SecurityContext $context
     * @param SecureConfig    $annotation
     */
    protected function loadHttpAuth(SecurityContext $context, SecureConfig $annotation)
    {
        if (count($this->httpAuthFactories) === 0) {
            return;
        }

        foreach ($this->httpAuthFactories as $httpAuthFactory) {
            $name = $httpAuthFactory->getName();
            $value = $annotation->{$name}();
            if ($value === null) {
                continue;
            }

            $value = $this->resolveParameter($value);
            if (in_array($value, [null, false, '', []], true)) {
                continue;
            }

            $httpAuth = $httpAuthFactory->create($value, $context->getDomain());
            if ($httpAuth) {
                $context->enableHttpAuth($name, $httpAuth);
            }
        }
    }

    /**
     * @param mixed $value
     *
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
