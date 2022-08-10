<?php
namespace Tk\Ui;

use Tk\Callback;
use Tk\ConfigTrait;
use Tk\Dom\AttributesTrait;
use Tk\Dom\CssTrait;

/**
 * Use this interface for all button UI objects
 *
 * @author Michael Mifsud <http://www.tropotek.com/>
 * @see http://www.tropotek.com/
 * @license Copyright 2017 Michael Mifsud
 */
abstract class Element extends \Dom\Renderer\Renderer implements \Dom\Renderer\DisplayInterface
{
    use AttributesTrait;
    use CssTrait;
    use ConfigTrait;

    /**
     * @var int
     */
    protected static $idx = 0;

    /**
     * @var string
     */
    protected $id = '';

    /**
     * @var boolean
     */
    protected $visible = true;

    /**
     * @var Callback
     */
    protected $onShow = null;


    /**
     * Element constructor.
     */
    public function __construct()
    {
        $this->onShow = \Tk\Callback::create();
        $this->id = self::$idx++;
    }

    /**
     * @return \Dom\Template
     */
    public function show()
    {
        /** @var \Dom\Template $template */
        $template = $this->getTemplate();
        $this->getOnShow()->execute($this);
        if (!$this->isVisible())
            return $template->clear($template->getRootElement());

        return $template;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Callback
     */
    public function getOnShow()
    {
        return $this->onShow;
    }

    /**
     * function (\Tk\Ui\Element $el) {}
     *
     * @param callable|null $onShow
     * @return Element
     * @deprecated use $this->addOnShow($callable, $priority);
     */
    public function setOnShow($onShow)
    {
        $this->addOnShow($onShow);
        return $this;
    }

    /**
     * function (\Tk\Ui\Element $el) {}
     *
     * @param callable $callable
     * @param int $priority [optional]
     * @return Element
     */
    public function addOnShow($callable, $priority = Callback::DEFAULT_PRIORITY)
    {
        $this->getOnShow()->append($callable, $priority);
        return $this;
    }

    /**
     * @return bool
     * @deprecated No longer needed remove all call to this and use $this->getOnShow()->isCallable() if needed
     */
    public function hasOnShow()
    {
        return $this->getOnShow()->isCallable();
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