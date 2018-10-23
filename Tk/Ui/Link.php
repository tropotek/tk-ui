<?php
namespace Tk\Ui;


/**
 * <code>
 *   \Tk\Ui\Link::create('Edit', \Tk\Uri::create('/dunno.html'), 'fa fa-edit)->addCss('btn-xs btn-success')->show();
 * </code>
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @see http://www.tropotek.com/
 * @license Copyright 2017 Michael Mifsud
 */
class Link extends Element
{

    /**
     * NOTE: This is not the attribute title use setAttr()
     * @var string
     */
    protected $text = '';

    /**
     * @var null|\Tk\Uri
     */
    protected $url = null;

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
     * @param null|string|\Tk\Uri $url
     * @param string|Icon $icon
     */
    public function __construct($text, $url = null, $icon = null)
    {
        parent::__construct();
        $this->setText($text);
        if ($url)
            $this->setUrl($url);
        if ($icon)
            $this->setIcon($icon);
        $this->setAttr('title', $text);
    }


    /**
     * @param string $text
     * @param null|string|\Tk\Uri $url
     * @param string|Icon $icon
     * @return static
     */
    public static function create($text, $url = null, $icon = null)
    {
        $obj = new static($text, $url, $icon);
        return $obj;
    }

    /**
     * @param $text
     * @param null $url
     * @param string $icon
     * @return Link
     * @note this is a helper class to remove the Z
     */
    public static function createBtn($text, $url = null, $icon = '')
    {
        $obj = self::create($text, $url, $icon);
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
     * @return null|\Tk\Uri
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param \Tk\Uri|string $url
     * @return $this
     */
    public function setUrl($url)
    {
        if ($url !== null) {
            $this->url = \Tk\Uri::create($url);
        }
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
        $template = $this->getTemplate();

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

        parent::show();
        if (!$this->isVisible()) {
            return $template;
        }

        if ($this->getUrl()) {
            $template->setAttr('link', 'href', $this->getUrl());
        }
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
<a href="#" var="link"></a>
HTML;
        return \Dom\Loader::load($html);
    }

}