<?php

namespace App\Repository;

use App\Entity\News;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<News>
 *
 * @method News|null find($id, $lockMode = null, $lockVersion = null)
 * @method News|null findOneBy(array $criteria, array $orderBy = null)
 * @method News[]    findAll()
 * @method News[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NewsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, News::class);
    }
    /**
     * @param string|null $term
     */
    public function getNewsData()
    {
        $qb = $this->createQueryBuilder("n");
        $qb->orderBy("n.id", "DESC");
        return $qb;
    }
    public function deleteNews($id): void
    {
        $queryBuilder = $this->createQueryBuilder('n');
        $query = $queryBuilder->delete(News::class, 'n')
            ->where('n.id = :Id')
            ->setParameter('Id', $id)
            ->getQuery();
        $result = $query->execute();
    }
}
