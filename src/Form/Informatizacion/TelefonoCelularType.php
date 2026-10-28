<?php

namespace App\Form\Informatizacion;

use App\Entity\Estructura\Entidad;
use App\Entity\Estructura\Estructura;
use App\Entity\Informatizacion\LineaCelular;
use App\Entity\Informatizacion\Marca;
use App\Entity\Informatizacion\Modelo;
use App\Entity\Informatizacion\SistemaOperativo;
use App\Entity\Informatizacion\TelefonoCelular;
use App\Entity\Informatizacion\TipoConectividad;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class TelefonoCelularType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('marca', EntityType::class, [
                'class' => Marca::class,
                'choice_label' => 'nombre',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')->orderBy('u.nombre', 'ASC');
                },
                'placeholder' => 'Seleccione',
                'empty_data' => null
            ])
            ->add('modelo', EntityType::class, [
                'class' => Modelo::class,
                'choice_label' => 'nombre',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')->orderBy('u.nombre', 'ASC');
                },
                'placeholder' => 'Seleccione',
                'empty_data' => null
            ])
            ->add('sistemaOperativo', EntityType::class, [
                'class' => SistemaOperativo::class,
                'choice_label' => 'nombre',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')->orderBy('u.nombre', 'ASC');
                },
                'placeholder' => 'Seleccione',
                'empty_data' => null
            ])
            ->add('estructura', EntityType::class, [
                'class' => Estructura::class,
                'label' => 'Institución',
                'choice_label' => 'nombre',
                'query_builder' => function (EntityRepository $er) use ($options) {
                    $qb = $er->createQueryBuilder('e')->where('e.esEntidad = true');
                    if ($options['institution_id'] !== null) {
                        $qb->andWhere('e.id = :institution')->setParameter('institution', $options['institution_id']);
                    }
                    return $qb->orderBy('e.nombre', 'ASC');
                },
                'placeholder' => 'Seleccione',
                'empty_data' => null,
                'disabled' => $options['institution_id'] !== null,
            ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TelefonoCelular::class,
            'institution_id' => null,
        ]);
        $resolver->setAllowedTypes('institution_id', ['null', 'int']);
    }
}
