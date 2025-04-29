<?php

namespace App\Form;

use App\Entity\Movement;
use App\Entity\ProductReception;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductReceptionDetailType extends AbstractType
{
	// public function getParent()
	// {
	// 	return MovementType::class;
	// }
	
	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		// parent::buildForm($builder, $options);
		$builder
			->add('productMovements', CollectionType::class, [
				'entry_type' => ProductMovementType::class,
				'entry_options' => ['label' =>false],
				])
			->add('submit', SubmitType::class)
		;
	}

	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver->setDefaults([
			// 'data_class' => ProductReception::class,
			'data_class' => Movement::class,
		]);
	}
}