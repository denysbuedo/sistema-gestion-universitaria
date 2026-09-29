<?php

namespace App\Form\Pregrado;

use App\Entity\Estructura\Entidad;
use App\Entity\Institucion\CategoriaAcreditacion;
use App\Entity\Institucion\Institucion;
use App\Entity\Institucion\NivelAcreditacion;
use App\Entity\Pregrado\SolicitudProgramaAcademicoInstitucion;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SolicitudProgramaInstitucionType extends AbstractType
{
    private $em;

    // Inyectamos el EntityManager en el constructor
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Extraemos las opciones
        $idEntidad = $options['idEntidad'];
        $isAdmin = in_array('ROLE_ADMIN', $options['user_roles']);

        $builder
            ->add('institucion', EntityType::class, [
                'label' => 'Universidad',
                'class' => Institucion::class,
                'choice_label' => 'nombre',
                'query_builder' => function (EntityRepository $er) use ($isAdmin, $idEntidad) {
                    $qb = $er->createQueryBuilder('u')
                        ->where("u.activo = true");

                    if (!$isAdmin && $idEntidad !== null) {
                        $qb->join('u.estructura', 'e')
                            ->andWhere('e.id = :idEntidad')
                            ->setParameter('idEntidad', $idEntidad);
                    }
                    return $qb->orderBy('u.nombre', 'ASC');
                },
                'placeholder' => 'Seleccione',
                'empty_data' => null
            ])
            ->add('categoriaAcreditacion', EntityType::class, [
                'label' => 'Categoría de acreditación',
                'class' => CategoriaAcreditacion::class,
                'choice_label' => 'nombre',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')->where('u.activo = true')->orderBy('u.nombre', 'ASC');
                },
                'placeholder' => 'Seleccione',
                'empty_data' => null,
                'required' => false
            ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SolicitudProgramaAcademicoInstitucion::class,
            'idEntidad' => null, // Definimos el valor por defecto
            'user_roles' => [],  // Definimos los roles por defecto
        ]);

        // Opcional: Validar que sean los tipos correctos
        $resolver->setAllowedTypes('idEntidad', ['int', 'null']);
        $resolver->setAllowedTypes('user_roles', 'array');
    }
}
