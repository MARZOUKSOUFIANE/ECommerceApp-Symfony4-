<?php

namespace App\Form;

use App\Entity\Criteria;
use App\Entity\Property;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PropertyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('description')
            ->add('surface')
            ->add('rooms')
            ->add('bedrooms')
            ->add('floor')
            ->add('price',null,[
                'label' => 'Price'
            ])
            ->add('heat',ChoiceType::class,[
                'choices'=> $this->getChoices()
            ])
            ->add('criterias',EntityType::class,[
                'class' => Criteria::class,
                'label' => 'Options',
                'required' => false,
                'choice_label' => 'name',
                'multiple' => true
            ])
            ->add('imageFile',FileType::class,[
                'required'=>false
            ])
            ->add('city')
            ->add('address')
            ->add('postal_code',null,[
                'label'=> 'Postal'
            ])
            ->add('sold',null,[
                'label'=> 'vendu/loué'
            ])
            ->add('hire',null,[
                'label'=> 'Location'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Property::class,
            'translation_domain' => 'forms'
        ]);
    }

    private function getChoices()
    {
        $choices = Property::HEAT;
        $output=[];
        foreach ($choices as $k => $v){
            $output[$v]=$k;
        }
        return $output;
    }
}
