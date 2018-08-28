<?php
namespace Tk\Ui;


/**
 * <code>
 *   \Tk\Ui\Button::create('Edit', \Tk\Uri::create('/dunno.html'), 'fa fa-edit)->addCss('btn-xs btn-success')->show();
 * </code>
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @see http://www.tropotek.com/
 * @license Copyright 2017 Michael Mifsud
 */
class Button extends Element
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
     * @var Icon
     */
    protected $rightIcon = null;



    /**
     * @param string $text
     * @param string|Icon $icon
     */
    public function __construct($text, $icon = null)
    {
        parent::__construct();
        $this->setText($text);
        if ($icon)
            $this->setIcon($icon);
        $this->setAttr('title', $text);
    }

    /**
     * @param string $text
     * @param null|string|\Tk\Uri $url
     * @param string|Icon $icon
     * @return Link
     * @deprecated use \Tk\Ui\Link::createBtn()
     * @remove 2.4.0
     */
    public static function create($text, $url = null, $icon = '')
    {
        $obj = Link::create($text, $url, $icon);
        $obj->addCss('btn btn-default');
        return $obj;
    }

    /**
     * @param $text
     * @param string $icon
     * @return Button
     */
    public static function createButton($text, $icon = null)
    {
        $obj = new static($text, $icon);
        $obj->addCss('btn btn-default');
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
     * @return Icon
     */
    public function getRightIcon()
    {
        return $this->rightIcon;
    }

    /**
     * @param Icon $rightIcon
     * @return $this
     */
    public function setRightIcon($rightIcon)
    {
        $this->rightIcon = $rightIcon;
        return $this;
    }

    /**
     * @return \Dom\Template
     */
    public function show()
    {
        $template = parent::show();
        if (!$this->isVisible()) {
            return $template;
        }

        $space = '';
        if ($this->getIcon()) $space = ' ';
        if ($this->getIcon()) {
            $template->appendTemplate('link', $this->getIcon()->show());
        }
        if ($this->getText()) {
            $template->appendHtml('link', $space . '<span>' . $this->getText() . '</span>');
        }
        if ($this->getRightIcon()) {
            $template->appendTemplate('link', $this->getRightIcon()->show());
        }
// Not getting here????? why??
//vd($this->getCssList(), $this->getAttrList());

        $template->addCss('link', $this->getCssList());
        $template->setAttr('link', $this->getAttrList());

        return $template;
    }

    /**
     * @return \Dom\Template
     */
    public function __makeTemplate()
    {
        $html = <<<HTML
<button var="link"></button>
HTML;
        return \Dom\Loader::load($html);
    }
}
