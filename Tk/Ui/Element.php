<?php
namespace Tk\Ui;


/**
 * Use this interface for all button UI objects
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @see http://www.tropotek.com/
 * @license Copyright 2017 Michael Mifsud
 */
abstract class Element extends \Dom\Renderer\Renderer implements \Dom\Renderer\DisplayInterface
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
     * @var boolean
     */
    protected $visible = true;

    /**
     * @var null|callable
     */
    protected $onShow = null;


    /**
     * Element constructor.
     */
    public function __construct()
    {
        $this->id = self::$idx++;
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

        return $template;
    }

    /**
     * @return \Dom\Template
     */
    public function __makeTemplate()
    {
        $html = <<<HTML
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