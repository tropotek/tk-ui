<?php
namespace Tk\Ui;

use Dom\Renderer\Renderer;
use Dom\Template;
use Tk\Ui\Icon;

/**
 * @author Michael Mifsud <http://www.tropotek.com/>
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
     * @var bool
     */
    protected $forceList = false;


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
            $obj->append($l);
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
     * @return bool
     */
    public function isForceList(): bool
    {
        return $this->forceList;
    }

    /**
     * @param bool $forceList
     * @return ButtonDropdown
     */
    public function setForceList(bool $forceList): ButtonDropdown
    {
        $this->forceList = $forceList;
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
            $template->setVisible('ico', false);
        }

        if (count($this->elementList) <= 0) {
            $template->setAttr('btn', 'href', '#');
            $this->addCss('disabled');
            $template->addCss('btn', $this->getCssList());
            $template->setAttr('btn', $this->getAttrList());
            $template->setVisible('dropdown', false);
        } else if (!$this->isForceList() && count($this->elementList) == 1) {
            /** @var \Tk\Ui\Link $link */
            $link = $this->elementList[0];
            $template->setAttr('btn', 'href', $link->getUrl());
            $template->addCss('btn', $this->getCssList());
            $template->setAttr('btn', $this->getAttrList());
            $template->setVisible('dropdown', false);
        } else {

            if ($this->hasAttr('data-confirm')) {
                /** @var $btn Link */
                foreach ($this->elementList as $link) {
                    $link->setAttr('data-confirm', $this->getAttr('data-confirm'));
                }
                $this->removeAttr('data-confirm');
            }

            /** @var $btn Link */
            foreach ($this->elementList as $link) {
                $item = $template->getRepeat('item');
                $item->appendTemplate('item', $link->show());
                $item->appendRepeat();
            }
            $this->setAttr('id', 'dropdown-' . $this->getId());
            $template->setAttr('dropdown-menu', 'aria-labelledby', 'dropdown-' . $this->getId());

            $template->addCss('dropdown-toggle', $this->getCssList());
            $template->setAttr('dropdown-toggle', $this->getAttrList());
            $template->setVisible('btn-group', false);
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
      <li class="dropdown-item" repeat="item" var="item"></li>
    </ul>
  </div>
</div>
HTML;
        return \Dom\Loader::load($html);
    }
}
