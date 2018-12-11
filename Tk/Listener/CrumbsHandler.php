<?php
namespace Tk\Listener;

use Tk\Event\Subscriber;
use Tk\Kernel\KernelEvents;

/**
 * @author Michael Mifsud <info@tropotek.com>
 * @see http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class CrumbsHandler implements Subscriber
{
    /**
     * @var bool
     */
    protected $renderEnabled = true;

    /**
     * @var null|\Tk\Controller\Iface
     */
    protected $controller = null;

    /**
     * @param bool $renderEnabled Set this to false to create your own onShow() Handlers
     */
    public function __construct($renderEnabled = true)
    {
        $this->renderEnabled = $renderEnabled;
    }

    /**
     * @param \Tk\Event\ControllerEvent $event
     * @throws \Tk\Exception
     */
    public function onController(\Tk\Event\ControllerEvent $event)
    {
        $config = \Bs\Config::getInstance();
        $crumbs = $config->getCrumbs();
        if (!$crumbs) throw new \Tk\Exception('Error creating crumb instance.');

        /** @var \Tk\Controller\Iface $controller */
        $this->controller = $event->getControllerObject();
        if ($this->controller instanceof \Tk\Controller\Iface) {
            // ignore adding crumbs if param in request URL
            if ($this->controller->getRequest()->has(\Tk\Crumbs::CRUMB_IGNORE)) {
                return;
            }
            $title = $this->controller->getPageTitle();
            $crumbs->trimByTitle($title);
            $crumbs->addCrumb($title, \Tk\Uri::create());
        }
    }

    /**
     * @param \Tk\Event\Event $event
     */
    public function onShow(\Tk\Event\Event $event)
    {
        $config = \Bs\Config::getInstance();

        if (!$this->renderEnabled) return;
        $controller = $event->get('controller');
        /** @var \Tk\Controller\Page $page */
        $page = $controller->getPage();
        if ($page instanceof \Tk\Controller\Page) {
            $crumbs = $config->getCrumbs();
            if (!$crumbs || !$crumbs->isVisible()) return;

            $template = $page->getTemplate();
            $backUrl = $crumbs->getBackUrl();
            $js = <<<JS
config.backUrl = '$backUrl';
JS;
            $template->appendjs($js, array('data-jsl-priority' => '-999'));

            $js = <<<JS
jQuery(function($) {
  $('a.btn.back').attr('href', config.backUrl);
});
JS;
            $template->appendjs($js);
            $var = 'breadcrumb';
            if ($config->has('template.var.page.breadcrumbs'))
                $var = $config->get('template.var.page.breadcrumbs');
            if ($template->keyExists('var', $var)) {
                $template->replaceTemplate($var, $crumbs->show());
                $template->setChoice($var);
            }
        }
    }

    /**
     * @param \Tk\Event\RequestEvent $event
     */
    public function onFinishRequest(\Tk\Event\RequestEvent $event)
    {
        $config = \Bs\Config::getInstance();
        $crumbs = $config->getCrumbs();
        if ($crumbs)
            $crumbs->save();
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::CONTROLLER => array('onController', 11),
            \Tk\PageEvents::CONTROLLER_SHOW =>  array('onShow', 0),
            KernelEvents::FINISH_REQUEST => 'onFinishRequest'
        );
    }

}