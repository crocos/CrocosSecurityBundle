<?php

namespace Crocos\SecurityBundle\Security\AuthStrategy\UserHibernator;

use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * EntityHibernator.
 *
 * @author Katsuhiro Ogawa <ogawa@crocos.co.jp>
 */
class EntityHibernator implements UserHibernatorInterface
{
    protected $doctrine;

    /**
     * Constructor.
     *
     * @param RegistryInterface $doctrine
     */
    public function __construct(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * {@inheritDoc}
     */
    public function hibernate($user)
    {
        $data = array('id' => $user->getId(), 'class' => get_class($user))

        return $data;
    }

    /**
     * {@inheritDoc}
     */
    public function awake($data)
    {
        $em = $this->doctrine->getEntityManagerForClass($data['class']);

        $user = $this->findUser($em->getRepository($data['class']), $data['id']);

        return $user;
    }

    protected function findUser($repository, $id)
    {
        return $repository->find($id);
    }
}
