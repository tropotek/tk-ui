<?php
namespace Tk\Ui\Menu;


/**
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2018 Michael Mifsud
 */
class Menu extends Item
{

    /**
     * @var ListRenderer
     */
    protected $renderer = null;


    /**
     * @return ListRenderer
     */
    public function getRenderer()
    {
        return $this->renderer;
    }

    /**
     * Set the renderer object
     *
     * @param ListRenderer $renderer
     * @return Menu
     */
    public function setRenderer(ListRenderer $renderer)
    {
        $this->renderer = $renderer;
        return $this;
    }


}

