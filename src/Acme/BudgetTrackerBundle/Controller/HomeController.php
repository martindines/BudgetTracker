<?php

namespace Acme\BudgetTrackerBundle\Controller;

use Acme\BudgetTrackerBundle\Controller\Controller as Controller;

/*
 * Takes care of the index page
 */
class HomeController extends Controller
{
    /*
     * Finds if the user is brand new or experienced. In the second case finds
     * all his expenses for the current month, the sum, spent for them and the
     * budget for the month (if set) and gives them to the template.
     */
    public function indexAction()
    {
        //user, expense_repo, category_repo, month_repo
        
        $this->setUser();               
        $category_repository = $this->setRepository('Category');    
        $number_of_categories = $category_repository->countByUser($this->user);
        
        $template = 'AcmeBudgetTrackerBundle:Home:index.html.twig';
        
        if($number_of_categories == 0){
            return $this->render($template, array(
                'newcommer' => true
            ));
        } else {
            $today = new \DateTime;
            
            $expense_repository = $this->setRepository('Expense');
            $expenses_for_current_month = $expense_repository->
                    findExpensesByMonth($this->user, $today->format('Y'), $today->format('m')); 
            
            if(!$expenses_for_current_month){
                return $this->render($template, array(
                    'newcommer' => false,
                    'expenses_for_current_month' => null
                ));      
            } else {
                $first_category = $expenses_for_current_month[0]->getCategory()->getName();
            
                $sum_for_current_month = $expense_repository->
                        getSumByMonth($this->user, $today->format('Y'), $today->format('m'));
                
                $current_month = $this->setRepository('Month')
                        ->findMonth($today->format('m'), $today->format('Y'), $this->user); 
                
                if($current_month){
                    $budget_for_current_month = $current_month[0]->getBudget();
                    $remaining = number_format($budget_for_current_month - $sum_for_current_month, 2, '.', ''); 
                } else {
                    $budget_for_current_month = null;
                    $remaining = null;
                }   
            }
            
            return $this->render($template, array(
                'newcommer' => false,
                'expenses_for_current_month' => $expenses_for_current_month,
                'first_category' => $first_category,
                'sum_for_current_month' => $sum_for_current_month,
                'budget_for_current_month' => $budget_for_current_month,
                'remaining' => $remaining
            ));
        }
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