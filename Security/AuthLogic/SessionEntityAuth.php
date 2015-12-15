<?php
namespace Crocos\SecurityBundle\Security\AuthLogic;

use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * SessionEntityAuth.
 *
 * @author Katsuhiro Ogawa <ogawa@crocos.co.jp>
 */
class SessionEntityAuth extends SessionAuth
{
    const FORMAT_VERSION = 1;

    /**
     * @var ManagerRegistry
     */
    protected $managerRegistry;

    /**
     * Constructor.
     *
     * @param SessionInterface $session
     * @param ManagerRegistry  $managerRegistry
     */
    public function __construct(SessionInterface $session, ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;

        parent::__construct($session);
    }

    /**
     * {@inheritdoc}
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

        $data = [
            'v'     => self::FORMAT_VERSION,
            'class' => $class,
            'id'    => $user->getId(),
        ];

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    protected function awakeUser($data)
    {
        if (!is_array($data) || !isset($data['v']) || $data['v'] != self::FORMAT_VERSION) {
            return;
        }

        $class = $data['class'];
        $id = $data['id'];

        $manager = $this->managerRegistry->getManagerForClass($class);
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
