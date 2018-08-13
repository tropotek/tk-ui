<?php
namespace Tk\Ui;

use Dom\Renderer\Renderer;
use Dom\Template;
use Tk\Ui\Icon;

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
     * @param string|Icon $icon
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
            $template->hide('dropdown');
        } else {
            /** @var $btn Link */
            foreach($this->linkList as $link) {
                $item = $template->getRepeat('item');
                $item->appendTemplate('item', $link->show());
                $item->appendRepeat();
            }
            $this->setAttr('id', 'dropdown-'.$this->getId());
            $template->setAttr('dropdown-menu', 'aria-labelledby', 'dropdown-'.$this->getId());

            $template->addCss('dropdown-toggle', $this->getCssList());
            $template->setAttr('dropdown-toggle', $this->getAttrList());
            $template->hide('btn-group');
        }

        return $template;
    }


    /**
     * @return \Dom\Template
     */
    public function __makeTemplate()
    {
        $html = <<<HTML
<div style="display: inline-block;">
  <div class="btn-group" var="btn-group">
    <a href="#" class="btn btn-default" var="btn"><i var="ico"></i><span var="text">Action</span></a>
  </div>
  <div class="dropdown" var="dropdown">
    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" var="dropdown-toggle">
      <i var="ico" choice="ico"></i><span var="text">Action</span> <span class="caret"></span>
    </button>
    <ul class="dropdown-menu" var="dropdown-menu">
      <li repeat="item" var="item"></li>
    </ul>
  </div>
</div>
HTML;
        return \Dom\Loader::load($html);
    }
}