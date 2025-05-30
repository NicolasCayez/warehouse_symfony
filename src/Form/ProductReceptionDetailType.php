<?php

namespace App\Form;

use App\Entity\ProductReception;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductReceptionDetailType extends MovementType
{
	// public function getParent()
	// {
	// 	return MovementType::class;
	// }
	
	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		// parent::buildForm($builder, $options);
		// $builder
		// 	->remove('mvmtDate');
		$builder
			->add('Movements', CollectionType::class, [
				'entry_type' => MovementType::class,
				'entry_options' => ['label' =>false],
				])
		;
	}

	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver->setDefaults([
			'data_class' => ProductReception::class,
		]);
	}
}