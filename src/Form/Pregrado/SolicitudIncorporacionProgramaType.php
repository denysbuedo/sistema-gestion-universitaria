<?php

namespace App\Form\Pregrado;

use App\Entity\Pregrado\SolicitudIncorporacionPrograma;
use App\Entity\Pregrado\SolicitudProgramaAcademico;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class SolicitudIncorporacionProgramaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('programa', EntityType::class, [
                'class' => SolicitudProgramaAcademico::class,
                'choice_label' => 'nombre',
                'placeholder' => 'Seleccione un programa del catálogo nacional',
                'query_builder' => static fn (EntityRepository $repository) => $repository->createQueryBuilder('p')
                    ->where('p.estadoProgramaAcademico IN (:estados)')->setParameter('estados', [2, 4, 6, 7])
                    ->orderBy('p.nombre', 'ASC'),
            ])
            ->add('fundamentacion', TextareaType::class, ['label' => 'Fundamentación de la solicitud'])
            ->add('modalidadDiurno', CheckboxType::class, ['label' => 'Curso diurno', 'required' => false])
            ->add('modalidadPorEncuentro', CheckboxType::class, ['label' => 'Curso por encuentro', 'required' => false])
            ->add('modalidadADistancia', CheckboxType::class, ['label' => 'Educación a distancia', 'required' => false]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => SolicitudIncorporacionPrograma::class]);
    }
}
