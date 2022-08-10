<?php
namespace Tk\Listener;

use Tk\Event\Subscriber;
use Tk\Kernel\KernelEvents;

/**
 * @author Michael Mifsud <http://www.tropotek.com/>
 * @see http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class ActionPanelHandler implements Subscriber
{


    /**
     * @param \Tk\Event\Event $event
     * @throws \Dom\Exception
     */
    public function onShowController(\Tk\Event\Event $event)
    {
        /** @var \Bs\Controller\Iface $controller */
        $controller = \Tk\Event\Event::findControllerObject($event);
        if (!$controller instanceof \Tk\Controller\Iface) return;
        if (!method_exists($controller, 'getActionPanel')) return;

        $pageTemplate = $controller->getPage()->getTemplate();
        $contentVar = $controller->getPage()->getContentVar();
        if (!$contentVar)
            $contentVar = 'content';

        /** @var \Tk\Ui\Admin\ActionPanel $actionPanel */
        $actionPanel = $controller->getActionPanel();
        if ($actionPanel->isEnabled() && $actionPanel->isVisible()) {
            $tpl = $actionPanel->show();
            $pageTemplate->prependTemplate($contentVar, $tpl);
        }

    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            \Tk\PageEvents::CONTROLLER_SHOW => array('onShowController', 0)
        );
    }
}