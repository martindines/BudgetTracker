<?php

namespace Acme\BudgetTrackerBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * DebtsLoansRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class DebtLoanRepository extends EntityRepository
{
    public function countNotReturned($user, $category, $returned = 0)
    {
        $q = $this
            ->createQueryBuilder('dl')
            ->select('COUNT(dl.id)') 
            ->where('dl.user = :user')
            ->andWhere('dl.category = :category')
            ->andWhere('dl.returned = :returned')
            ->setParameter('user', $user)
            ->setParameter('category', $category)
            ->setParameter('returned', $returned)
             ->getQuery();
       
        return $q->getSingleScalarResult();
        
    }
    
    public function findByCategory($user, $category, $returned = 0)
    {
        $q = $this
            ->createQueryBuilder('dl') 
            ->where('dl.user = :user')
            ->andWhere('dl.category = :category')
            ->andWhere('dl.returned = :returned')
            ->setParameter('user', $user)
            ->setParameter('category', $category)
            ->setParameter('returned', $returned)
             ->getQuery();
       
        return $q->getResult();
        
    }
    
    public function findByMonth($user, $category, $month, $year, $returned = 0)
    {
                $emConfig = $this->getEntityManager()->getConfiguration();
        $emConfig->addCustomDatetimeFunction('YEAR', 'DoctrineExtensions\Query\Mysql\Year');
        $emConfig->addCustomDatetimeFunction('MONTH', 'DoctrineExtensions\Query\Mysql\Month');
        
        $q = $this
            ->createQueryBuilder('dl')
            ->where('dl.user = :user')
            ->andWhere('dl.category = :category')
                ->andWhere('dl.returned = :returned')
                ->andWhere('MONTH(dl.date) = :month')
                ->andWhere('YEAR(dl.date) = :year')
                
            ->setParameter('user', $user)
            ->setParameter('category', $category)
            ->setParameter('returned', $returned)
            ->setParameter('month', $month)
            ->setParameter('year', $year)
             ->getQuery();
        
        return $q->getResult();
    } 
    
   public function getSumByCategoryAndMonth($user, $category, $month, $year, $returned = 0)
    {
        $emConfig = $this->getEntityManager()->getConfiguration();
        $emConfig->addCustomDatetimeFunction('YEAR', 'DoctrineExtensions\Query\Mysql\Year');
        $emConfig->addCustomDatetimeFunction('MONTH', 'DoctrineExtensions\Query\Mysql\Month');
        
        $q = $this->createQueryBuilder('dl')
            ->add('select', 'SUM(dl.price)')
            ->where('dl.user = :user')
            ->andWhere('dl.category = :category')
            ->andWhere('MONTH(dl.date) = :month')
            ->andWhere('YEAR(dl.date) = :year')
            ->andWhere('dl.returned = :returned')
                
            ->setParameter('user', $user)
            ->setParameter('category', $category)
            ->setParameter('month', $month)
            ->setParameter('year', $year)
            ->setParameter('returned', $returned)
            ->getQuery();

        return $q->getSingleScalarResult();
    } 
}