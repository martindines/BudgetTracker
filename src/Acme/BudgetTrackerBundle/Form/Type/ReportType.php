<?php

namespace Acme\BudgetTrackerBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;

class ReportType extends AbstractType
{   
    protected $user;
    
    function __construct($user) 
    {
        $this->user = $user;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        $user = $this->user;
        $builder->add('categories', 'entity', array(
            'class' => 'AcmeBudgetTrackerBundle:Category',
            'property' => 'name',
            'label' => ' ',
            'required' => true,
            'multiple' =>true,
            'expanded' => true,
            'attr'     => array('checked'   => 'checked'),
            'query_builder' => function ($repository) use ($user)
                { return $repository->createQueryBuilder('cat')
                                    ->select('cat')
                                    ->where('cat.user = :user')
                                    ->andWhere('cat.name <> :debts')
                                    //->orderBy('cat.name', 'ASC')
                                    ->setParameter('user', $user)
                                     ->setParameter('debts', 'Debts');
                } ));    
        
        
        $builder->add('from_date', 'text',array(
            'label'  => 'From'
        ));
        
        $builder->add('to_date','text', array(
            'label' => 'to',
            'required' => true
        )); 
       
    }

    public function getName()
    {
        return 'month';
    }
}
