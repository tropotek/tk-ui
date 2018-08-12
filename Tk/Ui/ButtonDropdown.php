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
     * @var Icon
     */
    protected $icon = null;


    /**
     * @param $text
     * @param string $icon
     * @param array|Link[] $links
     * @return static
     */
    public static function createButtonDropdown($text, $icon = null, $links = array())
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
     * @return Icon
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * @param string|Icon $icon
     * @return $this
     */
    public function setIcon($icon)
    {
        $this->icon = Icon::create($icon);
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
        if (!$this->isVisible()) {
            return $template;
        }

        $space = '';
        if ($this->getIcon()) $space = ' ';
        if ($this->getText()) {
            $template->insertText('text', $space . $this->getText());
        }
        if ($this->getIcon()) {
            $template->replaceTemplate('ico', $this->getIcon()->show());
        } else {
            $template->hide('ico');
        }
        if (count($this->linkList) == 1) {
            /** @var \Tk\Ui\Link $link */
            $link = $this->linkList[0];
            $template->setAttr('btn', 'href', $link->getUrl());
            $template->addCss('btn', $this->getCssList());
            $template->setAttr('btn', $this->getAttrList());
            $template->removeClass('btn-group', 'btn-group');
            $template->setAttr('btn-group', 'style', 'display: inline-block;');
            $template->hide('dropdown');
        } else {
            /** @var $btn Link */
            foreach($this->linkList as $link) {
                //$tpl = $link->show();
                //$template->appendHtml('dropdown-menu', '<li>' . $tpl->toString() . '</li>');
                $item = $template->getRepeat('item');
                $item->appendTemplate('item', $link->show());
                $item->appendRepeat();
            }
            $template->addCss('dropdown', $this->getCssList());
            $template->setAttr('dropdown', $this->getAttrList());
            $template->hide('btn');
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
  <a href="#" class="btn btn-default" var="btn"><i var="ico"></i><span var="text">Action</span></a>
  <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" var="dropdown">
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