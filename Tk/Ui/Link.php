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
     * @param string $text
     * @param null|string|\Tk\Uri $url
     * @param string|Icon $icon
     * @return static
     */
    public static function create($text, $url = null, $icon = '')
    {
        $obj = new static();
        $obj->text = $text;
        $obj->url = $url;
        $obj->setIcon($icon);
        return $obj;
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
        if ($this->getUrl()) {
            $template->setAttr('link', 'href', $this->getUrl());
        }
        if ($this->getIcon()) {
            $template->appendTemplate('link', $this->getIcon()->show());
        }

        if ($this->getText()) {
            $template->appendText('link', $space . $this->getText());
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

}