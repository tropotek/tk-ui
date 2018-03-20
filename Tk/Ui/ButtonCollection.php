<?php
namespace Tk\Ui;

/**
 * TODO: We should rename this to a `LinkCollection` object
 *
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @see http://www.tropotek.com/
 * @license Copyright 2016 Michael Mifsud
 */
class ButtonCollection extends Element
{

    /**
     * @var \Tk\Collection|Link[]
     */
    protected $linkList = null;


    /**
     * constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->linkList = new \Tk\Collection();
    }

    /**
     * @param array $buttons
     * @return static
     */
    public static function create($buttons = array())
    {
        $obj = new static();
        foreach ($buttons as $b) {
            $obj->add($b);
        }
        return $obj;
    }

    /**
     * @param Element $button
     * @return Element
     */
    public function add($button) {
        $button->set('group', $this);
        $this->linkList->set($button->getId(), $button);
        return $button;
    }

    /**
     * @param Element $srcButton
     * @param Element $button
     * @return Element
     */
    public function addBefore($srcButton, $button)
    {
        $newArr = array();
        if (!count($this->linkList)) {
            $this->add($button);
            return $button;
        }
        foreach ($this->linkList as $k => $v) {
            if ($k == $srcButton->getId()) {
                $newArr[$button->getId()] =  $button;
            }
            $newArr[$k] = $v;
        }
        $this->linkList->clear()->replace($newArr);
        return $button;
    }

    /**
     * @param Element $srcButton
     * @param Element $button
     * @return Element
     */
    public function addAfter($srcButton, $button)
    {
        $newArr = array();
        if (!count($this->linkList)) {
            $this->add($button);
            return $button;
        }
        foreach ($this->linkList as $k => $v) {
            $newArr[$k] = $v;
            if ($k == $srcButton->getId()) {
                $newArr[$button->getId()] =  $button;
            }
        }
        $this->linkList->clear()->replace($newArr);
        return $button;
    }

    /**
     * @param int $id
     * @return null|Element
     */
    public function find($id)
    {
        return $this->linkList->get($id);
    }

    /**
     * @param string $title
     * @return null|Element
     */
    public function findByTitle($title)
    {
        /** @var Element $button */
        foreach ($this->linkList as $button) {
            if (method_exists($button, 'getText') && $button->getText() == $title)
                return $button;
        }
        return null;
    }

    /**
     * @param int|Element $id
     * @return null|Element Return null if no link removed
     */
    public function remove($id)
    {
        if ($id instanceof Link) $id = $id->getId();

        if (!$this->linkList->has($id)) return null;
        $button = $this->linkList->get($id);
        $this->linkList->remove($id);
        return $button;
    }

    /**
     * @param Element $button
     * @return Element
     * @deprecated
     */
    public function addButton($button) { return $this->add($button); }

    /**
     * @param Element $srcButton
     * @param Element $button
     * @return Element
     * @deprecated
     */
    public function addButtonBefore($srcButton, $button) { return $this->addBefore($srcButton, $button); }

    /**
     * @param Element $srcButton
     * @param Element $button
     * @return Element
     * @deprecated
     */
    public function addButtonAfter($srcButton, $button) { return $this->addAfter($srcButton, $button); }

    /**
     * @param $id
     * @return null|Element
     * @deprecated
     */
    public function findButton($id) { return $this->find($id); }

    /**
     * @param $title
     * @return null|Element
     * @deprecated
     */
    public function findButtonByTitle($title) { return $this->findByTitle($title); }

    /**
     * @param $id
     * @return null|Element
     * @deprecated
     */
    public function removeButton($id) { return $this->remove($id); }




    /**
     * @return \Dom\Template
     */
    public function show()
    {
        $template = $this->getTemplate();

        /** @var Link $srcBtn */
        foreach ($this->linkList as $srcBtn) {
            $btn = clone $srcBtn;
            $btnTemplate = $btn->show();
            if (!$btn->isVisible()) continue;
            $template->appendTemplate('body', $btnTemplate);
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
<div class="tk-btn-collection" var="body"></div>
HTML;
        return \Dom\Loader::load($html);
    }
    
}