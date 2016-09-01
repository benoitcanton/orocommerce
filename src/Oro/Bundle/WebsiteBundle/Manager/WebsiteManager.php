<?php

namespace Oro\Bundle\WebsiteBundle\Manager;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManager;

use Oro\Bundle\WebsiteBundle\Entity\Website;

class WebsiteManager
{
    /**
     * @var ManagerRegistry
     */
    protected $managerRegistry;

    /**
     * @var Website
     */
    protected $currentWebsite;

    /**
     * @param ManagerRegistry $managerRegistry
     */
    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
    }

    /**
     * @return Website
     */
    public function getCurrentWebsite()
    {
        if (!$this->currentWebsite) {
            $this->currentWebsite = $this->getEntityManager()
                ->getRepository(Website::class)
                ->getDefaultWebsite();
        }

        return $this->currentWebsite;
    }

    /**
     * @return EntityManager
     */
    protected function getEntityManager()
    {
        return $this->managerRegistry->getManagerForClass(Website::class);
    }
}