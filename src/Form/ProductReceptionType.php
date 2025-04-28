<?php

namespace App\Form;


use App\Entity\ProductReception;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductReceptionType extends AbstractType
{
	public function getParent()
	{
		return MovementType::class;
	}
	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		parent::buildForm($builder, $options);
		$builder
			->add('invoiceRef')
			->add('parcelRef')
		;
	}
// last_qty
// new_qty
// product
// movement
	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver->setDefaults([
			'data_class' => ProductReception::class,
		]);
	}
}