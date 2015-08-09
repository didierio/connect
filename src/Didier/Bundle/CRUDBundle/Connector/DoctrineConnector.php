<?php

namespace Didier\Bundle\CRUDBundle\Connector;

use Symfony\Bridge\Doctrine\RegistryInterface;

class DoctrineConnector
{
    private $doctrine;

    public function __construct(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function save($object)
    {
        $em = $this->doctrine->getManager();
        $em->persist($object);
        $em->flush($object);

        return $object;
    }

    public function delete($object)
    {
        $em = $this->doctrine->getManager();
        $em->delete($object);
        $em->flush($object);
    }

    public function find($class, $id, $param = 'id')
    {
        return $this->buildQuery($class, [$param => $id])->getOneOrNullResult();
    }

    public function findAll($class)
    {
        return $this->buildQuery($class)->getResult();
    }

    private function buildQuery($class, array $params = array())
    {
        $queryBuilder = $this->doctrine->getManager()->createQueryBuilder()
            ->select('o')->from($class, 'o')
        ;

        foreach ($params as $name => $value) {
            $queryBuilder->orWhere(sprintf('e.%s LIKE :%s', $name, $name));
            $queryBuilder->setParameter($name, $value);
        }

        return $queryBuilder->getQuery();
    }
}
