<?php

/*
 * @copyright   2014 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\LeadBundle\Form\Type;

use Doctrine\ORM\EntityManager;
use Mautic\CoreBundle\Form\DataTransformer\IdToEntityModelTransformer;
use Mautic\CoreBundle\Helper\CoreParametersHelper;
use Mautic\CoreBundle\Security\Permissions\CorePermissions;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\File;

/**
 * Class CompanyType.
 */
class CompanyType extends AbstractType
{
    use EntityFieldsBuildFormTrait;
    /**
     * @var \Mautic\CoreBundle\Security\Permissions\CorePermissions
     */
    private $security;

    private $em;

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var CoreParametersHelper
     */
    protected $coreParametersHelper;

    /**
     * CompanyType constructor.
     *
     * @param EntityManager   $entityManager
     * @param CorePermissions $security
     */
    public function __construct(EntityManager $entityManager, CorePermissions $security, RouterInterface $router, TranslatorInterface $translator, CoreParametersHelper $coreParametersHelper)
    {
        $this->em                   = $entityManager;
        $this->security             = $security;
        $this->router               = $router;
        $this->translator           = $translator;
        $this->coreParametersHelper = $coreParametersHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->getFormFields($builder, $options, 'company');

        $transformer = new IdToEntityModelTransformer(
            $this->em,
            'MauticUserBundle:User'
        );

        $builder->add(
            $builder->create(
                'owner',
                'user_list',
                [
                    'label'      => 'mautic.lead.company.field.owner',
                    'label_attr' => ['class' => 'control-label'],
                    'attr'       => [
                        'class' => 'form-control',
                    ],
                    'required' => false,
                    'multiple' => false,
                ]
            )
                ->addModelTransformer($transformer)
        );

        $builder->add('score',
            'number',
            [
                'label'      => 'mautic.company.score',
                'attr'       => ['class' => 'form-control'],
                'label_attr' => ['class' => 'control-label'],
                'precision'  => 0,
                'required'   => false,
            ]
        );

        $builder->add(
            'companyFiles',
            'file',
            [
                'label'      => 'mautic.lead.company.field.companyfiles',
                'label_attr' => ['class' => 'control-label'],
                'attr'       => [
                    'class'    => 'form-control',
                ],
                'multiple'    => true,
                'required'    => false,
                'mapped'      => false,
                'constraints' => [
                    new All([
                        new File([
                            'maxSize'        => $this->coreParametersHelper->getParameter('mautic.max_size').'M',
                            'maxSizeMessage' => $this->translator->trans('mautic.lead.company.file.maxsizemsg', ['%size%' => $this->coreParametersHelper->getParameter('mautic.max_size')]),
                        ]),
                    ]),
                ],
            ]
        );

        if (!empty($options['update_select'])) {
            $builder->add(
                'buttons',
                'form_buttons',
                [
                    'apply_text' => false,
                ]
            );

            $builder->add(
                'updateSelect',
                'hidden',
                [
                    'data'   => $options['update_select'],
                    'mapped' => false,
                ]
            );
        } else {
            $builder->add(
                'buttons',
                'form_buttons'
            );
        }
        $builder->add('buttons', 'form_buttons', [
            'post_extra_buttons' => [
                [
                    'name'  => 'merge',
                    'label' => 'mautic.lead.merge',
                    'attr'  => [
                        'class'       => 'btn btn-default btn-dnd',
                        'icon'        => 'fa fa-building',
                        'data-toggle' => 'ajaxmodal',
                        'data-target' => '#MauticSharedModal',
                        'data-header' => $this->translator->trans('mautic.lead.company.header.merge'),
                        'href'        => $this->router->generate(
                            'mautic_company_action',
                            [
                                'objectId'     => $options['data']->getId(),
                                'objectAction' => 'merge',
                            ]
                        ),
                    ],
                ],
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class'    => 'Mautic\LeadBundle\Entity\Company',
                'isShortForm'   => false,
                'update_select' => false,
            ]
        );

        $resolver->setRequired(['fields']);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'company';
    }
}
