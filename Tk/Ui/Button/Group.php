<?php
namespace Tk\Ui\Button;

/**
 * Class
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2016 Michael Mifsud
 */
class Group extends \Dom\Renderer\Renderer implements \Dom\Renderer\DisplayInterface
{

    /**
     * @var \Tk\Collection
     */
    protected $buttonList = null;
    
    /**
     * @var boolean
     */
    protected $visible = true;



    /**
     * Actions constructor.
     */
    public function __construct()
    {
        $this->buttonList = new \Tk\Collection();
    }


    /**
     * @param Iface $button
     * @return Iface
     */
    public function addButton($button) {
        $this->setVisible(true);
        $this->buttonList->set($button->getId(), $button);
        return $button;
    }

    /**
     * @param Iface $srcButton
     * @param Iface $button
     * @return Iface
     */
    public function addButtonBefore($srcButton, $button)
    {
        $newArr = array();
        if (!count($this->buttonList)) {
            $this->addButton($button);
            return $button;
        }
        foreach ($this->buttonList as $k => $v) {
            if ($k == $srcButton->getId()) {
                $newArr[$button->getId()] =  $button;
            }
            $newArr[$k] = $v;
        }
        $this->buttonList->clear()->replace($newArr);
        return $button;
    }

    /**
     * @param Iface $srcButton
     * @param Iface $button
     * @return Iface
     */
    public function addButtonAfter($srcButton, $button)
    {
        $newArr = array();
        if (!count($this->buttonList)) {
            $this->addButton($button);
            return $button;
        }
        foreach ($this->buttonList as $k => $v) {
            $newArr[$k] = $v;
            if ($k == $srcButton->getId()) {
                $newArr[$button->getId()] =  $button;
            }
        }
        $this->buttonList->clear()->replace($newArr);
        return $button;
    }

    /**
     * @param int $id
     * @return null|Iface
     */
    public function findButton($id)
    {
        return $this->buttonList->get($id);
    }

    /**
     * @param int|Iface $id
     * @return null|Iface Return null if no button removed
     */
    public function removeButton($id)
    {
        if ($id instanceof Iface) $id = $id->getId();

        if (!$this->buttonList->has($id)) return null;
        $button = $this->buttonList->get($id);
        $this->buttonList->remove($id);
        return $button;
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
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;
    }



    /**
     * @return \Dom\Template
     */
    public function show()
    {
        $template = $this->getTemplate();

        /** @var Iface $srcBtn */
        foreach ($this->buttonList as $srcBtn) {
            $btn = clone $srcBtn;
            if ($btn->hasOnShow()) {
                call_user_func_array($btn->getOnShow(), array($btn, $this));
            }
            if (!$btn->isVisible()) continue;
            $row = $template->getRepeat('btn');

            if ($btn->getUrl()) {
                $row->setAttr('btn', 'href', $btn->getUrl());
            } else {
                $row->setAttr('btn', 'href', '#');
            }
            $row->setAttr('btn', 'title', $btn->getTitle());
            $css = $btn->getCssString();
            if (!$css) {
                $css = 'btn-default';
            }
            $row->addCss('btn', $css);
            if ($btn->getIcon()) {
                $row->addCss('icon', $btn->getIcon());
            } else if ($btn->getTitle()) {
                $row->insertText('icon', $btn->getTitle());
            }
            if (count($btn->getAttrList())) {
                $row->setAttr('btn', $btn->getAttrList());
            }
            $row->appendRepeat();
        }

        return $template;
    }


    /**
     * makeTemplate
     *
     * @return \Dom\Template
     */
    public function __makeTemplate()
    {
        $html = <<<HTML
<div var="buttonGroup" class="tk-btn-group">
  <a href="#" class="btn" title="" var="btn" repeat="btn"><i var="icon" class=""></i></a>
</div>
HTML;
        return \Dom\Loader::load($html);
    }
    
}