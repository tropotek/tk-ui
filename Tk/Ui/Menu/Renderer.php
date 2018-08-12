<?php
namespace Tk\Ui\Menu;

/**
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2018 Michael Mifsud
 */
class Renderer extends \Dom\Renderer\Renderer implements \Dom\Renderer\DisplayInterface
{

    /**
     * @var Menu
     */
    protected $menu = null;



    /**
     * @param Menu $menu
     */
    public function __construct(Menu $menu)
    {
        $this->menu = $menu;
    }

    /**
     *
     * @return Menu
     */
    public function getMenu()
    {
        return $this->menu;
    }



    /**
     * show
     */
    public function show()
    {
        $template = $this->getTemplate();



        return $template;
    }

    /**
     *
     * @param array $list
     * @param int $n Used as an internal counter
     * @return \Dom\Template
     */
    protected function iterate($list, $n = 0)
    {
        // TODO: We need to take a copy of the template and append items and sub-menus as we go.
        // TODO: All classes and design stuff will have to be left to the theme javascript
        $ul = clone $this->getTemplate();

        foreach ($list as $item) {



            if ($item->hasChildren()) {
                $template = $this->iterate($item->getChildren(), $n+1);
            }
        }





//        if ($this->maxDepth > 0 && $nest >= $this->maxDepth) {
//            return;
//        }
//        $ul = $this->getTemplate()->getRepeat('ul');
//
//        if (isset($this->dropdown['ul'][$nest])) {
//            foreach($this->dropdown['ul'][$nest] as $attr => $val) {
//                $ul->setAttr('ul', $attr, $val);
//            }
//        }
//
//        /* @var $item Item */
//        foreach ($list as $item) {
//            $request = $this->getUri();
//            $url = \Tk\Url::create($item->url);
//
//            $li = $ul->getRepeat('li');
//            if ($item->findByHref($request, true)) {
//                $li->addClass('li', $this->activeClass);
//            }
//
//            if ($item->cssClass) {
//                $li->addClass('li', $item->cssClass);
//            }
//
//            if ($item instanceof \Mod\Menu\Divider) {
//                $li->insertText('li', '');
//            } else if ($item instanceof \Mod\Menu\Header) {
//                $li->insertText('li', $item->text);
//            } else {
//                if ($item->title) {
//                    $li->setAttr('a', 'title', $item->title);
//                }
//                if ($item->target) {
//                    $li->setAttr('a', 'target', $item->target);
//                }
//                if ($item->rel) {
//                    $li->setAttr('a', 'rel', $item->rel);
//                }
//
//                if ($item->icon) {
//                    $li->addClass('icon', $item->icon);
//                    $li->setChoice('icon');
//                }
//
//                $li->insertText('a-text', $item->text);
//                $li->setAttr('a', 'href', $url->toString());
//            }
//
//            if ($item->hasChildren()) {
//                if (isset($this->dropdown['li'][$nest])) {
//                    foreach($this->dropdown['li'][$nest] as $attr => $val) {
//                        $li->setAttr('li', $attr, $val);
//                    }
//                }
//
//                if (isset($this->dropdown['a'][$nest])) {
//                    if (isset($this->dropdown['a'][$nest]['_icon'])) {
//                        $li->insertHtml('a-text', $item->text . $this->dropdown['a'][$nest]['_icon']);
//                    }
//                    foreach($this->dropdown['a'][$nest] as $attr => $val) {
//                        if ($attr == '_icon') continue;
//                        $li->setAttr('a', $attr, $val);
//                    }
//                }
//                $ul2 = $this->iterate($item->children, $nest+1);
//                //$ul2->addClass('ul', $this->subMenuClass);
//                if (isset($this->dropdown['ul'][$nest+1])) {
//                    foreach($this->dropdown['ul'][$nest+1] as $attr => $val) {
//                        $ul2->setAttr('ul', $attr, $val);
//                    }
//                }
//                $li->appendTemplate('li', $ul2);
//            }
//            $li->appendRepeat();
//        }
//        return $ul;

    }



    /**
     * @return string
     */
    public function __makeTemplate()
    {
        $xhtml = <<<HTML
<ul var="ul" repeat="ul">
  <li var="li" repeat="li"><a href="#" var="link"></a></li>
</ul>
HTML;
        return \Dom\Loader::load($xhtml);
    }


}
