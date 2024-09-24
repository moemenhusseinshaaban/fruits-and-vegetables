<?php declare(strict_types = 1);

namespace App\Repository;

use App\Entity\Food;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Food>
 *
 * @method Food|null find($id, $lockMode = null, $lockVersion = null)
 * @method Food|null findOneBy(array $criteria, array $orderBy = null)
 * @method Food[]    findAll()
 * @method Food[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FoodRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, string $entityClass = Food::class)
    {
        parent::__construct($registry, $entityClass);
    }

    public function findByCriteria(array $filters = [])
    {
        $qb = $this->createQueryBuilder('f');

        if (isset($filters['name'])) {
            $qb->andWhere('f.name LIKE :name')
            ->setParameter('name', '%' . $filters['name'] . '%');
        }

        if (isset($filters['minQuantityInGrams'])) {
            $qb->andWhere('f.quantityInGrams >= :minQuantityInGrams')
            ->setParameter('minQuantityInGrams', $filters['minQuantityInGrams']);
        }

        if (isset($filters['maxQuantityInGrams'])) {
            $qb->andWhere('f.quantityInGrams <= :maxQuantityInGrams')
            ->setParameter('maxQuantityInGrams', $filters['maxQuantityInGrams']);
        }

        return $qb->getQuery()->getResult();
    }
    
//    /**
//     * @return Food[] Returns an array of Food objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('f.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Food
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
