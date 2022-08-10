<?php
namespace Tk\Ui\Menu;

/**
 * @author Michael Mifsud <http://www.tropotek.com/>
 * @link http://www.tropotek.com/
 * @license Copyright 2018 Michael Mifsud
 */
abstract class RendererIface extends \Dom\Renderer\Renderer implements \Dom\Renderer\DisplayInterface
{

    /**
     * @var Menu
     */
    protected $menu = null;



    /**
     * @param null|Menu $menu
     */
    public function __construct($menu = null)
    {
        $this->setMenu($menu);
    }

    /**
     * @return Menu
     */
    public function getMenu()
    {
        return $this->menu;
    }

    /**
     * @param Menu $menu
     * @return RendererIface
     */
    public function setMenu($menu)
    {
        $this->menu = $menu;
        return $this;
    }


}
