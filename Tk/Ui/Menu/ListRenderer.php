<?php
namespace Tk\Ui\Menu;

/**
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2018 Michael Mifsud
 */
class ListRenderer extends RendererIface
{

    /**
     * @param Menu $menu
     * @return ListRenderer
     */
    static function create(Menu $menu)
    {
        $obj = new static($menu);
        return $obj;
    }

    /**
     * show
     */
    public function show()
    {
        $template = $this->getTemplate();
        $menu = $this->getMenu();

        $menu->addCss($menu->getLink()->getText());
        if (!$menu->hasAttr('id')) {
            $menu->setAttr('id', preg_replace('/[^a-z0-9_-]/i', '-', $menu->getLink()->getText()).'-'.$menu->getId());
        }

        $ul = $this->iterate($this->getMenu()->getChildren());
        $ul->addCss('list', $this->getMenu()->getCssList());
        $ul->setAttr('list', $this->getMenu()->getAttrList());

        $template->replaceTemplate('menu', $ul);

        return $template;
    }

    /**
     * @param array|Item[] $list
     * @param int $n Used as an internal counter
     * @return \Dom\Template
     */
    protected function iterate($list, $n = 0)
    {
        $ul = $this->getTemplate()->getRepeat('list');
        foreach ($list as $item) {
            $li = $this->getTemplate()->getRepeat('item');
            if ($item->getLink()) {
                if ($item->getLink()->getUrl()) {
                    $li->appendTemplate('item', $item->getLink()->show());
                } else {
                    if ($item->getLink()->getIcon()) {
                        $li->appendTemplate('item', $item->getLink()->getIcon()->show());
                    }
                    if ($item->getLink()->getText()) {
                        $li->appendHtml('item', '<span>' . $item->getLink()->getText() . '</span>');
                    }
                    if ($item->getLink()->getRightIcon()) {
                        $li->appendTemplate('item', $item->getLink()->getRightIcon()->show());
                    }
                }
            }

            if ($item->hasChildren()) {
                $item->addCss('submenu');
                $ulSub = $this->iterate($item->getChildren(), $n+1);
                //$ulSub->addCss('list', 'submenu');
                $li->appendTemplate('item', $ulSub);
            }

            $li->addCss('item', $item->getCssList());
            $li->setAttr('item', $item->getAttrList());

            $ul->appendTemplate('list', $li);
        }
        return $ul;
    }


    /**
     * @return string
     */
    public function __makeTemplate()
    {
        $xhtml = <<<HTML
<div var="menu">
  <li var="item" repeat="item"></li>
  <ul var="list" repeat="list"></ul>
</div>
HTML;
        return \Dom\Loader::load($xhtml);
    }


}
