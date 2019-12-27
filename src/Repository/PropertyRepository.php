<?php

namespace App\Repository;

use App\Entity\Property;
use App\Entity\PropertySearch;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Property|null find($id, $lockMode = null, $lockVersion = null)
 * @method Property|null findOneBy(array $criteria, array $orderBy = null)
 * @method Property[]    findAll()
 * @method Property[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PropertyRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Property::class);
    }

    /**
     * @param PropertySearch $search
     * @param Boolean $location
     * @return Query
     */
    public function findAllVisibleQuery(PropertySearch $search, $location):Query{
        $query =  $this->QueryVisible($location);

            if($search->getMaxPrice()){
                $query =  $query->andWhere('p.price <= :maxPrice')
                    ->setParameter('maxPrice',$search->getMaxPrice());
            }

            if($search->getMinSurface()){
                    $query = $query->andWhere('p.surface >= :minSurface')
                        ->setParameter('minSurface',$search->getMinSurface());
            }

            if($search->getCriterias()->count()>0){
                $k=0;
                foreach ($search->getCriterias() as $option){
                    $k++;
                    $query=$query
                        ->andWhere(":criteria$k MEMBER OF p.criterias")
                        ->setParameter("criteria$k",$option);
                }
            }
           return $query ->getQuery();
    }

    /**
     * @return Array
     */
    public function findLatest():Array{
        return $this->QueryVisibleLatest()
            ->setMaxResults(4)
            ->getQuery()
            ->getResult();
    }

    private function QueryVisible($location):QueryBuilder{
        if($location==true){
        return $this->createQueryBuilder('p')
            ->where('p.sold = false')
            ->andWhere('p.hire = true')
            ->orderBy('p.created_at','DESC');}
        else if($location==false){
            return $this->createQueryBuilder('p')
                ->where('p.sold = false')
                ->andWhere('p.hire = false')
                ->orderBy('p.created_at','DESC');}
    }

    private function QueryVisibleLatest():QueryBuilder{
            return $this->createQueryBuilder('p')
                ->where('p.sold = false')
                ->orderBy('p.created_at','DESC');
    }
    // /**
    //  * @return Property[] Returns an array of Property objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Property
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
