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
     * @var array|Menu[]
     */
    protected static $instance = array();


    /**
     * @var ListRenderer
     */
    protected $renderer = null;



    /**
     * @param string $name The name of the menu to retrieve
     * @return static
     */
    static function getInstance($name = null)
    {
        if (!isset(self::$instance[$name])) {
            self::$instance[$name] = static::create($name);
        }
        return self::$instance[$name];
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
        $this->renderer = $renderer;
        return $this;
    }

}

