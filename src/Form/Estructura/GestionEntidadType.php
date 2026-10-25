<?php

namespace App\Form\Estructura;

use App\Entity\Estructura\CategoriaEstructura;
use App\Entity\Estructura\Estructura;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class GestionEntidadType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('categoriaEstructura', EntityType::class, [
                'class' => CategoriaEstructura::class,
                'label' => 'Tipo o categoría de entidad',
                'choice_label' => 'nombre',
                'query_builder' => static fn (EntityRepository $repository) => $repository
                    ->createQueryBuilder('categoria')
                    ->where('categoria.activo = true')
                    ->orderBy('categoria.nombre', 'ASC'),
                'placeholder' => 'Seleccione',
                'required' => true,
            ])
            ->add('clasificacionPresupuestaria', ChoiceType::class, [
                'label' => 'Clasificación presupuestaria',
                'choices' => [
                    'Sin clasificación especial' => null,
                    'Unidad Presupuestada (UP)' => 'UP',
                    'Unidad Presupuestada con Tratamiento Especial (UPTE)' => 'UPTE',
                ],
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Estructura::class]);
    }
}
