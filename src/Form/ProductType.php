<?php

namespace App\Form;

use App\Entity\Movement;
use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		$builder
			->add('product_name', TextType::class)
			->add('product_serial_number', TextType::class)
			->add('product_ref', TextType::class)
			->add('product_ref2', TextType::class)
			->add('product_value', NumberType::class)
			// ->add('qty', IntegerType::class, ['mapped' => false])
			// ->add('submit', SubmitType::class, ['label' => 'Create',])
		;
	}
	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver->setDefaults([
			'data_class' => Product::class,
		]);
	}
}