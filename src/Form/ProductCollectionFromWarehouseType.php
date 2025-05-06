<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\ProductMovement;
use App\Entity\Warehouse;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductCollectionFromWarehouseType extends AbstractType
{

	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		$builder
			->add('products', CollectionType::class, [
				'entry_type' => ProductNewQtyType::class,
				'entry_options' => ['label' =>false],
				])
			->add('testSubmit', SubmitType::class)
		;
	}

	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver->setDefaults([
			'data_class' => Warehouse::class,
		]);
	}
}