<?php

namespace Core\Bundle\CoreBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;


/**
 * Class ActorRepository
 */
class ActorRepository extends EntityRepository
{
    /**
     * Count the total of rows
     *
     * @return int
     */
    public function countTotal()
    {
        $qb = $this->getQueryBuilder()
            ->select('COUNT(u)');

        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * Find all rows filtered for DataTables
     *
     * @param string $search        The search string
     * @param int    $sortColumn    The column to sort by
     * @param string $sortDirection The direction to sort the column
     *
     * @return \Doctrine\ORM\Query
     */
    public function findNewsletterSubscription($search, $sortColumn, $sortDirection)
    {
        // select
        $qb = $this->getQueryBuilder()
            ->select('u.id, u.email, u.name, u.surnames, i.path actorImage');

        // join
        $qb->join('u.roles', 'r');
        $qb->leftJoin('u.image', 'i');
                
        $qb->where('u.newsletter = true ');
                
        // search
        if (!empty($search)) {
            // where('u.email LIKE :search')
            $qb->andWhere('u.name LIKE :search')
                ->orWhere('u.surnames LIKE :search')
                ->setParameter('search', '%'.$search.'%');
        }

        // sort by column
        switch($sortColumn) {
            case 0:
                $qb->orderBy('u.id', $sortDirection);
                break;
            case 2:
                $qb->orderBy('u.email', $sortDirection);
                break;
            case 3:
                $qb->orderBy('u.name', $sortDirection);
                break;
            case 4:
                $qb->orderBy('u.surnames', $sortDirection);
                break;
        }

        return $qb->getQuery();
    }
    
    /**
     * Find all rows filtered for DataTables
     *
     * @param string $search        The search string
     * @param int    $sortColumn    The column to sort by
     * @param string $sortDirection The direction to sort the column
     *
     * @return \Doctrine\ORM\Query
     */
    public function findAllForDataTables($search, $sortColumn, $sortDirection)
    {
        // select
        $qb = $this->getQueryBuilder()
            ->select('u.id, u.email, u.name, u.surnames, i.path actorImage');

        // join
        $qb->join('u.roles', 'r');
        $qb->leftJoin('u.image', 'i');
                
        // search
        if (!empty($search)) {
            // where('u.email LIKE :search')
            $qb->where('r.role = :role')
                ->andWhere('u.name LIKE :search')
                ->orWhere('u.surnames LIKE :search')
                ->setParameter('role', 'ROLE_USER')
                ->setParameter('search', '%'.$search.'%');
        }

        // sort by column
        switch($sortColumn) {
            case 0:
                $qb->orderBy('u.id', $sortDirection);
                break;
            case 2:
                $qb->orderBy('u.email', $sortDirection);
                break;
            case 3:
                $qb->orderBy('u.name', $sortDirection);
                break;
            case 4:
                $qb->orderBy('u.surnames', $sortDirection);
                break;
        }

        return $qb->getQuery();
    }

    private function getQueryBuilder()
    {
        $em = $this->getEntityManager();

        $qb = $em->getRepository('CoreBundle:Actor')
            ->createQueryBuilder('u');

        return $qb;
    }
}