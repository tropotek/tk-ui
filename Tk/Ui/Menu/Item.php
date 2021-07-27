<?php
namespace Tk\Ui\Menu;

use Tk\Uri;
use Tk\Ui\Icon;
use Tk\Ui\Link;

/**
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2018 Michael Mifsud
 */
class Item extends \Tk\Ui\Element
{
    /**
     * @var string
     */
    protected $htmlTemplate = '<li var="item"></li>';

    /**
     * @var string
     */
    protected $var = 'item';

    /**
     * @var string
     */
    protected $name = '';

    /**
     * @var Link
     */
    protected $link = null;


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
     * @param string $name
     * @param string|\Tk\Uri $url
     * @param string $icon
     */
    public function __construct($name = '', $url = null, $icon = null)
    {
        parent::__construct();
        $this->setName($name);
        if ($url)
            $this->setLink(Link::create($name, $url, $icon));
    }

    /**
     * @param string $name
     * @param string|Uri $url
     * @param string|Icon $icon
     * @return static
     */
    static function create($name = '', $url = null, $icon = null)
    {
        $obj = new static($name, $url, $icon);
        return $obj;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return null|Link
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * @param null|Link $link
     * @return $this
     */
    public function setLink($link)
    {
        $this->link = $link;
        return $this;
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
     * @return string
     */
    public function getVar()
    {
        return $this->var;
    }

    /**
     * @param string $var
     * @return Item
     */
    public function setVar($var)
    {
        $this->var = $var;
        return $this;
    }

    /**
     * @param Item|Item[] $item
     * @param null|string|Item $refItem If null the item will be added to the top of the list
     * @return Item
     */
    public function prepend($item, $refItem = null)
    {
        if (is_string($refItem))
            $refItem = $this->findByName($refItem);

        $it = $this->initChildren($item);
        if (!$refItem) { // prepend to the top of the child array
            $this->setChildren(array_merge($it, $this->getChildren()));
        } else {
            $newArr = array();
            foreach ($this->getChildren() as $c) {
                if ($c === $refItem) $newArr[] = $item;
                $newArr[] = $c;
            }
            $this->setChildren($newArr);
        }
        return $item;
    }

    /**
     * @param Item|Item[] $item
     * @param null|string|Item $refItem If null the item will be added to the end of the list
     * @return Item
     */
    public function append($item, $refItem = null)
    {
        if (is_string($refItem))
            $refItem = $this->findByName($refItem);

        $it = $this->initChildren($item);
        if (!$refItem) {    // Append to the list as normal
            foreach ($it as $i) {
                $this->children[] = $i;
            }
        } else {
            $newArr = array();
            foreach ($this->getChildren() as $c) {
                $newArr[] = $c;
                if ($c === $refItem) $newArr[] = $item;
            }
            $this->setChildren($newArr);
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
     * Find the first menu Item to contain the matching href
     * Set $full to false to only search for the url path and not include the query string portion.
     *
     * @param string $href
     * @param bool $full If true the full url and querystring will searched
     * @return Item|null
     */
    public function findByHref($href, $full = true)
    {
        if (!$this->getLink()) return null;

        $cmp1 = Uri::create($href);
        $cmp2 = Uri::create( $this->getLink());
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
     * @param string $name
     * @return Item|null
     */
    public function findByName($name)
    {
        if ($this->getLink() && $this->getLink()->getText() == $name) {
            return $this;
        }
        foreach($this->getChildren() as $item) {
            $found = $item->findByName($name);
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
     * Reset the children array
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
     * Get the size of the crumbs array
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
     * @return \Dom\Template
     */
    public function show()
    {
        $template = $this->getTemplate();
        if (!$this->isVisible()) {
            return $template;
        }

        if ($this->getLink()) {
            if ($this->getLink()->getUrl()) {
                $template->appendTemplate($this->getVar(), $this->getLink()->show());
            } else {
                if ($this->getLink()->getIcon()) {
                    $template->appendTemplate($this->getVar(), $this->getLink()->getIcon()->show());
                }
                if ($this->getLink()->getText()) {
                    $template->appendHtml($this->getVar(), '<span>' . $this->getLink()->getText() . '</span>');
                }
                if ($this->getLink()->getRightIcon()) {
                    $template->appendTemplate($this->getVar(), $this->getLink()->getRightIcon()->show());
                }
            }
        } else {
            if ($this->getName()) {
                $template->appendHtml($this->getVar(), '<span>' . $this->getName() . '</span>');
            }
        }

        $template = parent::show();

        $template->addCss($this->getVar(), $this->getCssList());
        $template->setAttr($this->getVar(), $this->getAttrList());

        return $template;
    }

    /**
     * @return string
     */
    public function getHtmlTemplate()
    {
        return $this->htmlTemplate;
    }

    /**
     * @param string $htmlTemplate
     * @return Item
     */
    public function setHtmlTemplate($htmlTemplate)
    {
        $this->htmlTemplate = $htmlTemplate;
        return $this;
    }


    /**
     * @return \Dom\Template
     */
    public function __makeTemplate()
    {
        return \Dom\Loader::load($this->getHtmlTemplate());
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
            $s = '';
            if ($item->getLink()) {
                if ($item->getLink()->getText()) {
                    $s .= $item->getLink()->getText();
                }
                if ($item->getLink()->getUrl()) {
                    $s .= ' ['.$item->getLink()->getUrl()->toString().']';
                }
            }
            $s = implode('', array_fill(0, $n*2, ' ')) . ' - ' . $s . "\n";
            $str .= $s;
            if ($item->hasChildren()) {
                $str .= $this->iterateStr($item->getChildren(), $n+1);
            }
        }
        return $str;
    }

}
