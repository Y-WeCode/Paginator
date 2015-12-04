<?php

namespace YWC\PaginatorBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FilterForm extends AbstractType
{

    private $fields;
    
    public function __construct($fields)
    {
        $this->fields = $fields;
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'required' => false,
        ));
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        foreach($this->fields as $field => $fieldOptions) {
            $builder->add($field, 'choice', $fieldOptions);               
        }
    }

    public function getName()
    {
        return 'ywc_filter_form';
    }
}