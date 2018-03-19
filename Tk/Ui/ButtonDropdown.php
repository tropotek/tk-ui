<?php
namespace Tk\Ui;

use Dom\Renderer\Renderer;
use Dom\Template;


/**
 * TODO: add the ability to create a seperator/divider `<li role="separator" class="divider"></li>`
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
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
     * @throws \Dom\Exception
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


        /** @var $btn Link */
        foreach($this->linkList as $link) {
            $tpl = $link->show();
            $template->appendHtml('dropdown', '<li>' . $tpl->toString() . '</li>');
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
<div class="btn-group" var="btn">
  <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    <i var="ico" choice="ico"></i><span var="text">Action</span> <span class="caret"></span>
  </button>
  <ul class="dropdown-menu" var="dropdown">
    <!--<li><a href="#">Action</a></li>-->
    <!--<li><a href="#">Another action</a></li>-->
    <!--<li><a href="#">Something else here</a></li>-->
    <!--<li role="separator" class="divider"></li>-->
    <!--<li><a href="#">Separated link</a></li>-->
  </ul>
</div>
HTML;
        return \Dom\Loader::load($html);
    }
}