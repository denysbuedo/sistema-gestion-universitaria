<?php

namespace App\Form\Pregrado;

use App\Entity\Pregrado\SolicitudIncorporacionPrograma;
use App\Entity\Pregrado\SolicitudProgramaAcademico;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

final class SolicitudIncorporacionProgramaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $programaFijo = $options['programa_fijo'];
        $builder
            ->add('programa', EntityType::class, [
                'class' => SolicitudProgramaAcademico::class,
                'choice_label' => 'nombre',
                'placeholder' => 'Seleccione un programa del catálogo nacional',
                'query_builder' => static fn (EntityRepository $repository) => $repository->createQueryBuilder('p')
                    ->where('p.estadoProgramaAcademico IN (:estados)')->setParameter('estados', [2, 4, 6, 7])
                    ->orderBy('p.nombre', 'ASC'),
                'disabled' => $programaFijo !== null,
            ])
            ->add('fundamentacion', TextareaType::class, ['label' => 'Fundamentación de la solicitud'])
            ->add('documentoSolicitud', FileType::class, [
                'label' => 'Documento formal de solicitud',
                'mapped' => false,
                'required' => true,
                'help' => 'Adjunte el documento firmado que fundamenta la solicitud de apertura.',
                'constraints' => [new File(maxSize: '10M', mimeTypes: ['application/pdf'], mimeTypesMessage: 'Adjunte el documento en formato PDF.')],
            ]);
        if ($programaFijo === null || $programaFijo->getModalidadDiurno()) {
            $builder->add('modalidadDiurno', CheckboxType::class, ['label' => 'Curso diurno', 'required' => false]);
        }
        if ($programaFijo === null || $programaFijo->getModalidadPorEncuentro()) {
            $builder->add('modalidadPorEncuentro', CheckboxType::class, ['label' => 'Curso por encuentro', 'required' => false]);
        }
        if ($programaFijo === null || $programaFijo->getModalidadADistancia()) {
            $builder->add('modalidadADistancia', CheckboxType::class, ['label' => 'Educación a distancia', 'required' => false]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => SolicitudIncorporacionPrograma::class, 'programa_fijo' => null]);
        $resolver->setAllowedTypes('programa_fijo', ['null', SolicitudProgramaAcademico::class]);
    }
}
