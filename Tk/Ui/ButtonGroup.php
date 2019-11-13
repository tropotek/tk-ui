<?php
namespace Tk\Ui;
use Dom\Renderer\Renderer;
use Dom\Template;


/**
 * TODO: Test this object
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @see http://www.tropotek.com/
 * @license Copyright 2017 Michael Mifsud
 */
class ButtonGroup extends ButtonCollection
{

    /**
     * Execute the renderer.
     * Return an object that your framework can interpret and display.
     *
     * @return null|Template|Renderer
     */
    public function show()
    {
        $template = parent::show();
        if (!$this->isVisible()) {
            return $template;
        }

        /** @var $btn Button */
        foreach($this->elementList as $btn) {
            $tpl = $btn->show();
            $template->appendHtml('btn', '<div class="btn-group" role="group">' . $tpl->toString() . '</div>');
        }

        $template->addCss('btn', $this->getCssList());
        $template->setAttr('btn', $this->getAttrList());

        return $template;
    }


    /**
     * @return \Dom\Template
     */
    public function __makeTemplate()
    {
        $html = <<<HTML
<div class="btn-group btn-group-justified" role="group" var="btn"></div>
HTML;
        return \Dom\Loader::load($html);
    }
}