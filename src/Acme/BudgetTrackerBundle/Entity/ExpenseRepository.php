<?php

namespace Acme\BudgetTrackerBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * ExpenseRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ExpenseRepository extends EntityRepository
{
    public function findExpensesForDate($user, $fromDate, $toDate)
    {
        $q = $this
            ->createQueryBuilder('e')
            ->where('e.date >= :fromDate')
            ->andWhere('e.date < :toDate')
            ->andWhere('e.user = :user')
            ->setParameter('fromDate', $fromDate)
            ->setParameter('toDate', $toDate)
            ->setParameter('user', $user)
             ->getQuery();
        
        return $q->getResult();
    }

    public function findExpensesForMonthAndCat($user, $month, $category)
    {
        $q = $this
            ->createQueryBuilder('e')
            ->where('e.date LIKE :month')
            ->andWhere('e.user = :user')
            ->andWhere('e.category = :category')
            ->setParameter('month', "%$month")
            ->setParameter('user', $user)
            ->setParameter('category', $category)
             ->getQuery();
        
        return $q->getResult();
    } 
    
    //used
    public function findExpensesByMonth($user, $year, $month)
    {
        $emConfig = $this->getEntityManager()->getConfiguration();
        $emConfig->addCustomDatetimeFunction('YEAR', 'DoctrineExtensions\Query\Mysql\Year');
        $emConfig->addCustomDatetimeFunction('MONTH', 'DoctrineExtensions\Query\Mysql\Month');
        
        $q = $this
            ->createQueryBuilder('e')
            ->where('e.user = :user')
            ->andWhere('YEAR(e.date) = :year')
            ->andWhere('MONTH(e.date) = :month')                     
            ->orderBy('e.category', 'ASC')
            ->setParameter('user', $user)
            ->setParameter('year', $year)
            ->setParameter('month', $month)
             ->getQuery();
        
        return $q->getResult();
    } 
    
    //used
    public function getSumByMonth($user, $year, $month)
    {
        $emConfig = $this->getEntityManager()->getConfiguration();
        $emConfig->addCustomDatetimeFunction('YEAR', 'DoctrineExtensions\Query\Mysql\Year');
        $emConfig->addCustomDatetimeFunction('MONTH', 'DoctrineExtensions\Query\Mysql\Month');
        
        $q = $this->createQueryBuilder('e')
            ->add('select', 'SUM(e.price)')
            ->where('e.user = :user')
            ->andWhere('YEAR(e.date) = :year')
            ->andWhere('MONTH(e.date) = :month')
            ->setParameter('user', $user)
            ->setParameter('year', $year)
            ->setParameter('month', $month)
            ->getQuery();

        return $q->getSingleScalarResult();
    }
    
    public function findBetweenDates($user, $date1, $date2)
    {
        $q = $this
            ->createQueryBuilder('e')
            ->where('e.date >= :date1')
            ->andWhere('e.user = :user')
            ->andWhere('e.date <= :date2')
            ->setParameter('date1', $date1)
            ->setParameter('date2', $date2)
            ->setParameter('user', $user)
             ->getQuery();
        
        return $q->getResult();
    }
    
    public function findForCat($user, $q)
    {
         $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT e FROM AcmeBudgetTrackerBundle:Expense e WHERE e.user = ?1'.$q);
        $query->setParameter(1, $user);
        return $query->getResult();
    }
    
    public function findForCatsAndTimes($user, $start_date, $end_date, $q)
    {
         $em = $this->getEntityManager();
        $query = $em->createQuery('SELECT e FROM AcmeBudgetTrackerBundle:Expense e WHERE e.user = ?1 AND e.date >= ?2 AND e.date < ?3'.$q. 'ORDER BY e.category, e.date');
        $query->setParameter(1, $user);
        $query->setParameter(2, $start_date);
        $query->setParameter(3, $end_date);
        return $query->getResult();
    }
}