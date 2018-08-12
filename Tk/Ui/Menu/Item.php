<?php
namespace Tk\Ui\Menu;

use Tk\Uri;
use Tk\Ui\Icon;

/**
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2018 Michael Mifsud
 */
class Item
{
    use \Tk\Dom\AttributesTrait;
    use \Tk\Dom\CssTrait;

    /**
     * This is the Item text not the attribute
     * @var string
     */
    protected $title = '';

    /**
     * @var Uri
     */
    protected $url = null;
    
    /**
     * @var Icon
     */
    protected $icon = '';

    
    /**
     * @var array|Item[]
     */
    protected $children = array();

    /**
     * @var Menu
     */
    protected $menu = null;

    /**
     * @var Item
     */
    protected $parent = null;



    /**
     * @param string $title
     * @param Uri|string $url
     * @param string $icon
     */
    protected function __construct($title = '', $url = null, $icon = '')
    {
        $this->title = $title;
        $this->url = $url;
        $this->icon = $icon;
    }

    /**
     * @param string $title
     * @param Uri|string $url
     * @param string|Icon $icon
     * @return Item
     */
    static function create($title = '', $url = null, $icon = null)
    {
        $obj = new static($title, $url, $icon);
        return $obj;
    }

    /**
     * Set the item title text
     *
     * NOTE: This is not the title attribute, it is the item text
     *
     * @param string $str
     * @return Item
     */
    function setTitle($str)
    {
        $this->title = $str;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return Uri
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return Icon
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * @return Menu
     */
    public function getMenu()
    {
        return $this->menu;
    }

    /**
     * @return Item
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param Item|Item[] $item
     * @param null|Item $refItem If null the item will be added to the top of the list
     * @return Item
     */
    public function prepend($item, $refItem = null)
    {
        $it = $this->initChildren($item);
        if (!$refItem) { // prepend to the top of the child array
            $this->setChildren(array_merge($it, $this->getChildren()));
        } else {
            foreach ($this->getChildren() as $i => $child) {
                if ($child === $refItem) {
                    $p1 = array_slice($this->getChildren(), 0, $i);
                    $p2 = array_slice($this->getChildren(), $i+1);
                    $this->setChildren(array_merge($p1, $it, $p2));
                }
            }
        }
        return $item;
    }

    /**
     * @param Item|Item[] $item
     * @param null|Item $refItem If null the item will be added to the end of the list
     * @return Item
     */
    public function append($item, $refItem = null)
    {
        $it = $this->initChildren($item);
        if (!$refItem) {    // Append to the list as normal
            foreach ($it as $i) {
                $this->children[] = $i;
            }
        } else {
            foreach ($this->getChildren() as $i => $child) {
                if ($child === $refItem) {
                    $p1 = array_slice($this->getChildren(), 0, $i-1);
                    $p2 = array_slice($this->getChildren(), $i);
                    $this->setChildren(array_merge($p1, $it, $p2));
                }
            }
        }
        return $item;
    }

    /**
     * @param Item|Item[] $item
     * @return array|Item[]
     */
    protected function initChildren($item)
    {
        if (!is_array($item)) $item = array($item);
        foreach ($item as $i) {
            $i->setParent($this);
            $i->setMenu($this->getMenu());
        }
        return $item;
    }

    /**
     * Check if this is the menu tree's root item.
     *
     * @return bool
     */
    public function isRoot()
    {
        return ($this->getParent() == null && $this->getMenu() === $this);
    }

    /**
     * Test if this node has children nodes.
     *
     * @return bool
     */
    public function hasChildren()
    {
        if (count($this->getChildren())) {
            return true;
        }
        return false;
    }

    /**
     * Get this items children
     *
     * @return array|Item[]
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Get this items children
     *
     * @param array|Item[] $children
     * @return Item
     */
    public function setChildren(array $children)
    {
        $this->children = $children;
        return $this;
    }

    /**
     * Set the owner menu for all nested items
     *
     * @param Menu $menu
     * @return Item
     */
    public function setMenu($menu)
    {
        $this->menu = $menu;
        foreach ($this->getChildren() as $child) {
            $child->setMenu($menu);
        }
        return $this;
    }

    /**
     * Set the parent and its children parents
     *
     * @param Item $parent
     * @return Item
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
        foreach ($this->getChildren() as $child) {
            $child->setParent($parent);
        }
        return $this;
    }

    /**
     * find the first menu Item to contain the matching href
     * Set $full to false to only search for the url path and not include the query string portion.
     *
     * @param string $href
     * @param bool $full If true the full url and querystring will searched
     * @return Item|null
     */
    public function findByHref($href, $full = true)
    {
        $cmp1 = Uri::create($href);
        $cmp2 = Uri::create( $this->getUrl());
        if (!$full) {
            $cmp1->reset();
            $cmp2->reset();
        }
        if (($cmp1 && $cmp2) && $cmp1->toString() == $cmp2->toString()) {
            return $this;
        }
        foreach($this->getChildren() as $item) {
            $found = $item->findByHref($href, $full);
            if ($found) {
                return $found;
            }
        }
    }

    /**
     * Get a menu item by its text.
     *
     * @param string $title
     * @return Item|null
     */
    public function findByTitle($title)
    {
        if ($this->getTitle() == $title) {
            return $this;
        }
        foreach($this->getChildren() as $item) {
            $found = $item->findByTitle($title);
            if ($found) {
                return $found;
            }
        }
    }

    /**
     * Get a menu item by its index.
     *
     * @param int $idx
     * @return Item|null
     */
    public function findByIdx($idx = 0)
    {
        if (isset($this->children[$idx])) {
            return $this->children[$idx];
        }
    }

    /**
     * reset the children array
     *
     * @return Item
     */
    public function reset()
    {
        $this->children = array();
        return $this;
    }

    /**
     * Get the top most item
     *
     * @return Item
     */
    public function current()
    {
        return end($this->children);
    }

    /**
     * get the size of the crumbs array
     *
     * @param bool $deep
     * @return int
     */
    public function count($deep = false)
    {
        $tot = count($this->getChildren());
        if ($deep) {
            foreach ($this->getChildren() as $item) {
                $tot += $item->count(true);
            }
        }
        return $tot;
    }


    /**
     * @return string
     */
    public function __toString()
    {
        return $this->iterateStr($this->getChildren());
    }

    /**
     * @param array|Item[] $list
     * @param int $n
     * @return string
     */
    protected function iterateStr($list, $n = 0)
    {
        $str = '';

        foreach ($list as $item) {
            $s = implode('', array_fill(0, $n*2, ' ')) . ' - ' . $item->getTitle() . "\n";
            $str .= $s;
vd($item->getTitle(), $n);
            if ($item->hasChildren()) {
                $str .= $this->iterateStr($item->getChildren(), $n+1);
            }
        }

        return $str;
    }
}
