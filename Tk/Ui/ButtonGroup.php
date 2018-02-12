<?php
namespace Tk\Ui;

/**
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2016 Michael Mifsud
 */
class ButtonGroup extends \Dom\Renderer\Renderer implements \Dom\Renderer\DisplayInterface
{

    /**
     * @var \Tk\Collection
     */
    protected $buttonList = null;


    /**
     * constructor.
     * @param array $buttons
     */
    public function __construct($buttons = array())
    {
        $this->buttonList = new \Tk\Collection();
        foreach ($buttons as $b) {
            $this->addButton($b);
        }
    }

    /**
     * @param array $buttons
     * @return static
     */
    public static function create($buttons = array())
    {
        $obj = new static($buttons);
        return $obj;
    }

    /**
     * @param Button $button
     * @return Button
     */
    public function addButton($button) {
        $button->set('group', $this);
        $this->buttonList->set($button->getId(), $button);
        return $button;
    }

    /**
     * @param Button $srcButton
     * @param Button $button
     * @return Button
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
     * @param Button $srcButton
     * @param Button $button
     * @return Button
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
     * @return null|Button
     */
    public function findButton($id)
    {
        return $this->buttonList->get($id);
    }

    /**
     * @param string $title
     * @return null|Button
     */
    public function findButtonByTitle($title)
    {
        /** @var Button $button */
        foreach ($this->buttonList as $button) {
            if ($button->getText() == $title)
                return $button;
        }
    }

    /**
     * @param int|Button $id
     * @return null|Button Return null if no button removed
     */
    public function removeButton($id)
    {
        if ($id instanceof Button) $id = $id->getId();

        if (!$this->buttonList->has($id)) return null;
        $button = $this->buttonList->get($id);
        $this->buttonList->remove($id);
        return $button;
    }

    /**
     * @return \Dom\Template
     */
    public function show()
    {
        $template = $this->getTemplate();

        /** @var Button $srcBtn */
        foreach ($this->buttonList as $srcBtn) {
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
<div class="tk-btn-group" var="body"></div>
HTML;
        return \Dom\Loader::load($html);
    }
    
}