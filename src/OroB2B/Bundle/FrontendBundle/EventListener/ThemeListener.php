<?php

namespace OroB2B\Bundle\FrontendBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

use Oro\Bundle\ConfigBundle\Config\ConfigManager;
use Oro\Bundle\ThemeBundle\Model\ThemeRegistry;

use OroB2B\Bundle\FrontendBundle\Request\FrontendHelper;

class ThemeListener
{
    const FRONTEND_THEME = 'demo';
    const DEFAULT_LAYOUT_THEME_CONFIG_VALUE_KEY = 'oro_b2b_frontend.frontend_theme';

    /**
     * @var ThemeRegistry
     */
    protected $themeRegistry;

    /**
     * @var FrontendHelper
     */
    protected $helper;

    /**
     * @var bool
     */
    protected $installed;

    /**
     * @var ConfigManager
     */
    protected $configManager;

    /**
     * @param ThemeRegistry $themeRegistry
     * @param FrontendHelper $helper
     * @param ConfigManager $configManager
     * @param boolean $installed
     */
    public function __construct(
        ThemeRegistry $themeRegistry,
        FrontendHelper $helper,
        ConfigManager $configManager,
        $installed
    ) {
        $this->themeRegistry = $themeRegistry;
        $this->helper = $helper;
        $this->configManager = $configManager;
        $this->installed = $installed;
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$this->installed) {
            return;
        }

        if ($event->getRequestType() !== HttpKernelInterface::MASTER_REQUEST) {
            return;
        }

        if ($this->helper->isFrontendRequest($event->getRequest())) {
            // set oro theme
            $this->themeRegistry->setActiveTheme(self::FRONTEND_THEME);
            // set layout theme
            $request = $event->getRequest();
            $layoutTheme = $this->configManager->get(self::DEFAULT_LAYOUT_THEME_CONFIG_VALUE_KEY);
            $request->attributes->set('_theme', $layoutTheme);
        }
    }

    /**
     * @param GetResponseForControllerResultEvent $event
     */
    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        $request = $event->getRequest();

        if ($event->getRequestType() !== HttpKernelInterface::MASTER_REQUEST) {
            return;
        }

        if (!$this->helper->isFrontendRequest($request)) {
            return;
        }

        if ($request->attributes->get('_theme')) {
            $request->attributes->remove('_template');
        } else {
            $request->attributes->remove('_layout');
        }
    }
}
