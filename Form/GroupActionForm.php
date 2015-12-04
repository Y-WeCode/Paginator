<?php

namespace YWC\PaginatorBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GroupActionForm extends AbstractType
{

    private $entities;

    private $actions;
    
    public function __construct(array $entities, array $actions)
    {
        $this->entities = array();
        foreach($entities as $entity) $this->entities[$entity->getId()] = $entity->getId();
        $this->actions = array();
        foreach($actions as $action) $this->actions[$action['title']] = $action['title'];        
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'required' => false,
        ));
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('entities', 'choice', array(
            'label' => false,
            'choices' => $this->entities,
            'multiple' => true,
            'expanded' => true,
        ));
        $builder->add('actions', 'choice', array(
            'choices' => $this->actions,
        ));
    }

    public function getName()
    {
        return 'ywc_groupaction_form';
    }
}