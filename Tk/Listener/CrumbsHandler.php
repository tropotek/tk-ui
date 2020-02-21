<?php
namespace Tk\Listener;

use Symfony\Component\HttpKernel\KernelEvents;
use Tk\ConfigTrait;
use Tk\Event\Subscriber;

/**
 * @author Michael Mifsud <info@tropotek.com>
 * @see http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class CrumbsHandler implements Subscriber
{
    use ConfigTrait;

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
     * @param \Symfony\Component\HttpKernel\Event\FilterControllerEvent $event
     * @throws \Tk\Exception
     */
    public function onController($event)
    {
        $crumbs = $this->getConfig()->getCrumbs();
        if (!$crumbs) throw new \Tk\Exception('Error creating crumb instance.');

        /** @var \Tk\Controller\Iface $controller */
        $this->controller = \Tk\Event\Event::findControllerObject($event);
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
        if (!$this->renderEnabled) return;
        $controller = \Tk\Event\Event::findControllerObject($event);
        /** @var \Tk\Controller\Page $page */
        $page = $controller->getPage();
        if ($page instanceof \Tk\Controller\Page) {
            $crumbs = $this->getConfig()->getCrumbs();
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
            if ($this->getConfig()->has('template.var.page.breadcrumbs'))
                $var = $this->getConfig()->get('template.var.page.breadcrumbs');
            if ($template->keyExists('var', $var)) {
                $template->replaceTemplate($var, $crumbs->show());
                $template->setVisible($var);
            }
        }
    }

    /**
     * @param \Symfony\Component\HttpKernel\Event\FinishRequestEvent $event
     */
    public function onFinishRequest(\Symfony\Component\HttpKernel\Event\FinishRequestEvent $event)
    {
        $crumbs = $this->getConfig()->getCrumbs();
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