<?php

namespace Acme\BudgetTrackerBundle\Controller;

use Acme\BudgetTrackerBundle\Controller\Controller as Controller;
use Acme\BudgetTrackerBundle\Entity\Category;

/*
 * Takes care of the index page
 */
class HomeController extends Controller
{
    /*
     * Finds if the user is brand new or experienced. In the second case finds
     * all his expenses for the current month, the sum, spent for them, the
     * budget for the month (if set) and remaining money and gives them to the 
     * template.
     */
    
    //user, expense_repo, category_repo, month_repo
    public function indexAction()
    {
        $this->setUser();               
        $category_repository = $this->setRepository('Category');
        
        //Create Debts and Loans Categories if they don't exist
        $this->em = $this->getEM();
        $loans_count = $category_repository->countByNameAndUser('Loans', $this->user);
        if($loans_count == 0){
            $loans = new Category();
            $loans->setUser($this->user);
            $loans->setName('Loans');
            $this->em->persist($loans);
            $this->em->flush();
        }
        $debts_count = $category_repository->countByNameAndUser('Debts', $this->user);
        if($debts_count == 0){
            $debts = new Category();
            $debts->setUser($this->user);
            $debts->setName('Debts');
            $this->em->persist($debts); 
            $this->em->flush();
        }
        
        $number_of_user_categories = $category_repository->countByUser($this->user);
        
        $template = 'AcmeBudgetTrackerBundle:Home:index.html.twig';
        
        if($number_of_user_categories == 0){    
            
            return $this->render($template, array(
                'newcommer' => true
            ));
        } else {
            $today = new \DateTime;
            
            $this->setDebtsLoansIds();
            
            $expense_repository = $this->setRepository('Expense');
            $expenses_for_current_month = $expense_repository->
                findExpensesByMonth($today->format('m'), $today->format('Y'), $this->user, $this->debts_id); 
//            $dl_repository = $this->setRepository('DebtLoan');
//            $active_loans = $dl_repository->findByMonth($this->user, 1, $today->format('m'), $today->format('Y'), 0);
            
        
            if(!$expenses_for_current_month){
                return $this->render($template, array(
                    'newcommer' => false,
                    'expenses_for_current_month' => null
                ));      
            } else {
                
                if($expenses_for_current_month){
                    $first_category = $expenses_for_current_month[0]->getCategory()->getName();
            
                    $spent_for_expenses = $expense_repository->
                    getSumByMonth($today->format('m'), $today->format('Y'), $this->user);
                
                    $current_month = $this->setRepository('Month')
                    ->findMonth($today->format('m'), $today->format('Y'), $this->user); 
                
                    if($current_month){
                    $budget_for_current_month = $current_month[0]->getBudget();
                    $remaining = number_format($budget_for_current_month - $spent_for_expenses, 2, '.', ''); 
                    } else {
                        $budget_for_current_month = null;
                        $remaining = null;
                   }
                }
//                if($active_loans){
//                    $sum_of_active_loans = $dl_repository->getSumByCategoryAndMonth($this->user, 1, $today->format('m'), $today->format('Y'), 0);
//                    if(!$expenses_for_current_month){
//                        $first_category = null;
//                        $spent_for_expenses = 0;
//                    }
                    
                }
                
                $spent_for_current_month = $spent_for_expenses;
                
                   
            }
            
            return $this->render($template, array(
                'newcommer' => false,
               // 'active_loans' => $active_loans,
               // 'sum_of_active_loans' => $sum_of_active_loans,
                'expenses_for_current_month' => $expenses_for_current_month,
                'first_category' => $first_category,
                'spent_for_current_month' => $spent_for_current_month,
                'budget_for_current_month' => $budget_for_current_month,
                'remaining' => $remaining
            ));
        }
    }
 
//------------------------------------------------------------------------------

//$all_categories = $cat_repo->findByUser($this->user);

// Another decision with Array of Arrays
// 
//        $all = array();
//        
//        foreach ($all_categories as $cat)
//        {
//            //For each category creates array with name $category with expenses 
//            //for this category for the given month and pushes all this arrays into $all
//            
//            $var = strtolower($cat->getName());
//            array_push($all, $$var = $this->repository->
//                    findExpensesForMonthAndCat($this->user, $date, $cat->getId()));
//        }  
//            
//        $total_sum = 0;
//        
//        foreach ($all as $al) {
//            foreach($al as $a) {
//                $total_sum += $a->getPrice();
//            }
//        }
//        
//        echo $total_sum;