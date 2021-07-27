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
    static function create($menu = null)
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
        if (!$menu) return $template;

        if ($menu->getLink()) {
            $menu->addCss($menu->getLink()->getText());
            if (!$menu->hasAttr('id')) {
                $menu->setAttr('id', preg_replace('/[^a-z0-9_-]/i', '-', $menu->getLink()->getText()) . '-' . $menu->getId());
            }
        } else {
            $menu->addCss($menu->getName());
            if (!$menu->hasAttr('id')) {
                $menu->setAttr('id', preg_replace('/[^a-z0-9_-]/i', '-', $menu->getName()) . '-' . $menu->getId());
            }
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
            $li = null;
            if ($item->hasChildren()) {
                $item->addCss('submenu');
                $ulSub = $this->iterate($item->getChildren(), $n+1);
                $li = $item->show();
                $li->appendTemplate($item->getVar(), $ulSub);
            } else {
                $li = $item->show();
            }
            if ($li)
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
  <ul var="list" repeat="list"></ul>
</div>
HTML;
        return \Dom\Loader::load($xhtml);
    }


}
