<?php

namespace Core\Bundle\CoreBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;


/**
 * Class NewsletterShippingRepository
 */
class NewsletterShippingRepository extends EntityRepository
{
    /**
     * Count the total of rows
     *
     * @return int
     */
    public function countTotal()
    {
        $qb = $this->getQueryBuilder()
            ->select('COUNT(m)');

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
    public function findAllForDataTables($search, $sortColumn, $sortDirection)
    {
        // select
        $qb = $this->getQueryBuilder()
            ->select('m.id, n.title as newsletter, m.totalSent, m.type, m.created');

        $qb->join('m.newsletter', 'n');
        // search
        if (!empty($search)) {
            $qb->where('m.title LIKE :search')
                ->setParameter('search', '%'.$search.'%');
        }

        // sort by column
        switch($sortColumn) {
            case 0:
                $qb->orderBy('m.id', $sortDirection);
                break;
            case 1:
                $qb->orderBy('n.title', $sortDirection);
                break;
            case 2:
                $qb->orderBy('m.totalSent', $sortDirection);
                break;
            case 3:
                $qb->orderBy('m.created', $sortDirection);
                break;
             case 4:
                $qb->orderBy('m.type', $sortDirection);
                break;
        }

        return $qb->getQuery();
    }


    private function getQueryBuilder()
    {
        $em = $this->getEntityManager();

        $qb = $em->getRepository('CoreBundle:NewsletterShipping')
            ->createQueryBuilder('m');

        return $qb;
    }
}