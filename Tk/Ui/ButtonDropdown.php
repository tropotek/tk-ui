<?php
namespace Tk\Ui;

use Dom\Renderer\Renderer;
use Dom\Template;


/**
 * @author Michael Mifsud <info@tropotek.com>
 * @see http://www.tropotek.com/
 * @license Copyright 2017 Michael Mifsud
 */
class ButtonDropdown extends ButtonCollection
{

    /**
     * NOTE: This is not the attribute title use setAttr()
     * @var string
     */
    protected $text = '';

    /**
     * icon css. EG: 'fa fa-user'
     * @var string
     */
    protected $icon = '';


    /**
     * @param $text
     * @param string $icon
     * @param array|Link[] $links
     * @return static
     */
    public static function createButtonDropdown($text, $icon = '', $links = array())
    {
        $obj = new static();
        $obj->setText($text);
        $obj->setIcon($icon);
        foreach ($links as $l) {
            $obj->add($l);
        }
        return $obj;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param string $text
     * @return $this
     */
    public function setText($text)
    {
        $this->text = $text;
        return $this;
    }

    /**
     * @return string
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * @param string $icon
     * @return $this
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;
        return $this;
    }

    /**
     * Execute the renderer.
     * Return an object that your framework can interpret and display.
     *
     * @return null|Template|Renderer
     */
    public function show()
    {
        $template = parent::show();
        $space = '';
        if ($this->getIcon()) $space = ' ';
        if ($this->getText()) {
            $template->insertText('text', $space . $this->getText());
        }
        if ($this->getIcon()) {
            $template->addCss('ico', $this->getIcon());
            $template->setChoice('ico');
        }
        if (count($this->linkList) == 1) {
            /** @var \Tk\Ui\Link $link */
            $link = current($this->linkList);
            $template->setAttr('btn', 'href', $link->getUrl());
            $template->addCss('btn', $this->getCssList());
            $template->setAttr('btn', $this->getAttrList());
            $template->removeClass('btn-group', 'btn-group');
            $template->setAttr('btn-group', 'style', 'display: inline-block;');
            $template->setChoice('btn');
        } else {
            /** @var $btn Link */
            foreach($this->linkList as $link) {
                //$tpl = $link->show();
                //$template->appendHtml('dropdown-menu', '<li>' . $tpl->toString() . '</li>');
                $item = $template->getRepeat('item');
                $item->insertTemplate('item', $link->show());
                $item->appendRepeat();
            }
            $template->addCss('dropdown', $this->getCssList());
            $template->setAttr('dropdown', $this->getAttrList());
            $template->setChoice('dropdown');
        }

        return $template;
    }


    /**
     * @return \Dom\Template
     */
    public function __makeTemplate()
    {
        $html = <<<HTML
<div class="btn-group" var="btn-group">
  <a href="#" class="btn btn-default" var="btn" choice="btn"><i var="ico" choice="ico"></i><span var="text">Action</span></a>
  <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" choice="dropdown" var="dropdown">
    <i var="ico" choice="ico"></i><span var="text">Action</span> <span class="caret"></span>
  </button>
  <ul class="dropdown-menu" var="dropdown-menu" choice="dropdown">
    <li repeat="item" var="item"></li>
  </ul>
</div>
HTML;
        return \Dom\Loader::load($html);
    }
}