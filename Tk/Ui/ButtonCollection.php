<?php
namespace Tk\Ui;

/**
 * TODO: We should rename this to a `ElementCollection` object
 *
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @see http://www.tropotek.com/
 * @license Copyright 2016 Michael Mifsud
 */
class ButtonCollection extends Element
{
    use ElementCollectionTrait;


    /**
     * constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param array $buttons
     * @return static
     */
    public static function create($buttons = array())
    {
        $obj = new static();
        foreach ($buttons as $b) {
            $obj->append($b);
        }
        return $obj;
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

        /** @var Element $srcBtn */
        foreach ($this->getElementList() as $srcBtn) {
            $btn = clone $srcBtn;
            $btnTemplate = $btn->show();
            if (!$btn->isVisible()) continue;
            $template->appendTemplate('body', $btnTemplate);
        }

        $template->addCss('body', $this->getCssList());
        $template->setAttr('body', $this->getAttrList());

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



    /**
     * @param Element $button
     * @return Element
     * @deprecated Use append($button)
     * @remove 2.4.0
     */
    public function add($button) { return $this->append($button); }

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
     * @deprecated Use append($button, $refButton)
     * @remove 2.4.0
     */
    public function addAfter($srcButton, $button) { return $this->append($button, $srcButton); }

    /**
     * @param Element $srcButton
     * @param Element $button
     * @return Element
     * @deprecated Use prepend($button, $refButton)
     * @remove 2.4.0
     */
    public function addBefore($srcButton, $button) { return $this->prepend($button, $srcButton); }

    /**
     * @param Element $srcButton
     * @param Element $button
     * @return Element
     * @deprecated Use prepend($button, $refButton)
     * @remove 2.4.0
     */
    public function addButtonBefore($srcButton, $button) { return $this->addBefore($srcButton, $button); }

    /**
     * @param Element $srcButton
     * @param Element $button
     * @return Element
     * @deprecated Use append($button, $refButton)
     * @remove 2.4.0
     */
    public function addButtonAfter($srcButton, $button) { return $this->addAfter($srcButton, $button); }

    /**
     * @param $id
     * @return null|Element
     * @deprecated Use find($title)
     * @remove 2.4.0
     */
    public function findButton($id) { return $this->find($id); }

    /**
     * @param $title
     * @return null|Element
     * @deprecated Use find($title)
     * @remove 2.4.0
     */
    public function findButtonByTitle($title) { return $this->find($title); }

    /**
     * @param $id
     * @return null|Element
     * @deprecated Use remove($button)
     * @remove 2.4.0
     */
    public function removeButton($id) { return $this->remove($id); }
    
}