<?php
namespace Tk\Ui\Menu;

use Tk\Uri;
use Tk\Ui\Icon;

/**
 * @author Michael Mifsud <http://www.tropotek.com/>
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
     * @param string $name
     * @param string|Uri $url
     * @param string|Icon $icon
     * @return Menu
     */
    static function create($name = '', $url = null, $icon = null)
    {
        $obj = new static($name, $url, $icon);
        $obj->setRenderer(\Tk\Ui\Menu\ListRenderer::create());
        return $obj;
    }

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
        $renderer->setMenu($this);
        $this->renderer = $renderer;
        return $this;
    }


}

