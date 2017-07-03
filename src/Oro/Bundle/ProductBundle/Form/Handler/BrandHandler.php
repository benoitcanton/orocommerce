<?php

namespace Oro\Bundle\ProductBundle\Form\Handler;

use Doctrine\Common\Persistence\ObjectManager;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Translation\TranslatorInterface;

use Oro\Bundle\ProductBundle\Entity\Brand;

class BrandHandler
{
    /** @var FormInterface */
    protected $form;

    /** @var Request */
    protected $request;

    /** @var ObjectManager */
    protected $manager;

    /** @var TranslatorInterface */
    protected $translator;

    /** @var string */
    protected $defaultLocale = 'en';

    /**
     * @param FormInterface       $form
     * @param Request             $request
     * @param ObjectManager       $manager
     * @param TranslatorInterface $translator
     */
    public function __construct(
        FormInterface $form,
        Request $request,
        ObjectManager $manager,
        TranslatorInterface $translator
    ) {
        $this->form       = $form;
        $this->request    = $request;
        $this->manager    = $manager;
        $this->translator = $translator;
    }

    /**
     * @param Brand $entity
     *
     * @return bool True on successful processing, false otherwise
     */
    public function process(Brand $entity)
    {
        if ($entity->getId()) {
            $this->manager->refresh($entity);
        }

        $this->form->setData($entity);

        if (in_array($this->request->getMethod(), ['POST', 'PUT'])) {
            $this->form->submit($this->request);

            if ($this->form->isValid()) {
                $this->manager->persist($entity);
                $this->manager->flush();

                return true;
            }
        }

        return false;
    }
}