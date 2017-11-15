<?php
namespace Tk\Ui;


/**
 * <code>
 *   \Tk\Ui\Button::create('Edit', \Tk\Uri::create('/dunno.html'), 'fa fa-edit)->addCss('btn-xs btn-success')->show();
 * </code>
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2017 Michael Mifsud
 */
class Link extends \Dom\Renderer\Renderer implements \Dom\Renderer\DisplayInterface
{
    use \Tk\Dom\AttributesTrait;
    use \Tk\Dom\CssTrait;
    use \Tk\CollectionTrait;

    /**
     * @var int
     */
    protected static $idx = 0;

    /**
     * @var int
     */
    protected $id = 0;

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
     * icon css. EG: 'fa fa-user'
     * @var string
     */
    protected $icon = '';

    /**
     * @var boolean
     */
    protected $visible = true;

    /**
     * @var null|callable
     */
    protected $onShow = null;


    /**
     * @param string $text
     * @param null|\Tk\Uri|string $url
     * @param string $icon
     */
    public function __construct($text, $url = null, $icon = '')
    {
        $this->id = self::$idx++;
        $this->text = $text;
        $this->url = $url;
        $this->icon = $icon;
    }

    /**
     * @param string $text
     * @param null|string|\Tk\Uri $url
     * @param string $icon
     * @return static
     */
    public static function create($text, $url = null, $icon = '')
    {
        $obj = new static($text, $url, $icon);
        return $obj;
    }

    /**
     * @return \Dom\Template
     */
    public function show()
    {
        /** @var \Dom\Template $template */
        $template = $this->getTemplate();

        // callback
        if ($this->hasOnShow()) {
            call_user_func_array($this->getOnShow(), array($this));
        }

        if (!$this->isVisible()) return $template;
        $template->setChoice('link');

        $space = '';
        if ($this->getIcon()) $space = ' ';

        if ($this->getText()) {
            $template->insertText('text', $space . $this->getText());
        }
        if ($this->getUrl()) {
            $template->setAttr('link', 'href', $this->getUrl());
        }
        if ($this->getIcon()) {
            $template->addCss('ico', $this->getIcon());
            $template->setChoice('ico');
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
<a href="#" var="link" choice="link"><i var="ico" choice="ico"></i><span var="text"></span></a>
HTML;
        return \Dom\Loader::load($html);
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
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
     * @return callable|null
     */
    public function getOnShow()
    {
        return $this->onShow;
    }

    /**
     * function (\Tk\Ui\Button $button) {}
     *
     * @param callable|null $onShow
     */
    public function setOnShow($onShow)
    {
        $this->onShow = $onShow;
    }

    /**
     * @return bool
     */
    public function hasOnShow()
    {
        return is_callable($this->getOnShow());
    }

    /**
     * @return bool
     */
    public function isVisible()
    {
        return $this->visible;
    }

    /**
     * @param bool $visible
     * @return $this
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;
        return $this;
    }

}