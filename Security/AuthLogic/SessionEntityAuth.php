<?php

namespace Crocos\SecurityBundle\Security\AuthLogic;

use Symfony\Component\HttpFoundation\Session;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * SessionEntityAuth.
 *
 * @author Katsuhiro Ogawa <ogawa@crocos.co.jp>
 */
class SessionEntityAuth extends SessionAuth
{
    const FORMAT_VERSION = 1;

    /**
     * @var RegistryInterface
     */
    protected $managerRegistry;

    /**
     * Constructor.
     *
     * @param Session $session
     * @param RegistryInterface $managerRegistry
     */
    public function __construct(Session $session, RegistryInterface $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;

        parent::__construct($session);
    }

    /**
     * {@inheritDoc}
     */
    protected function sleepUser($user)
    {
        if (!is_object($user)) {
            throw new \InvalidArgumentException('You must provide an object');
        }

        $class = get_class($user);

        if (!method_exists($user, 'getId')) {
            throw new \InvalidArgumentException(sprintf('User must implement getId() method'));
        }

        $data = array(
            'v'     => self::FORMAT_VERSION,
            'class' => $class,
            'id'    => $user->getId(),
        );

        return $data;
    }

    /**
     * {@inheritDoc}
     */
    protected function awakeUser($data)
    {
        if (!is_array($data) || !isset($data['v']) || $data['v'] != self::FORMAT_VERSION) {
            return;
        }

        $class = $data['class'];
        $id = $data['id'];

        // todo compat-2.0
        $manager = $this->managerRegistry->getEntityManagerForClass($class);
        $repository = $manager->getRepository($class);

        $user = $repository->find($id);

        if (!$user) {
            throw new \UnexpectedValueException(sprintf('Account "%s" does not exist', $id));
        }

        // check enabled
        if (method_exists($user, 'isEnabled')) {
            if (!$user->isEnabled()) {
                throw new \UnexpectedValueException(sprintf('Account "%s" is not enabled', $id));
            }
        }

        return $user;
    }
}
