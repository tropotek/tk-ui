<?php
namespace Tk\Ui;



/**
 * This class uses the bootstrap dialog box model
 * @link http://getbootstrap.com/javascript/#modals
 *
 * To add a close button to the footer:
 *
 *    $dialog->getButtonList()->append(\Tk\Ui\Button::createButton('Close')->setAttr('data-dismiss', 'modal'));
 *
 * Launch Button:
 *
 *    <a href="#" data-toggle="modal" data-target="#{id}"><i class="fa fa-info-circle"></i> {title}</a>
 *
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2016 Michael Mifsud
 */
abstract class FormDialog extends Dialog
{


    /**
     * @var \Tk\Form
     */
    protected $form = null;


    /**.
     * @param \Tk\Form $form
     * @param string $title
     */
    public function __construct($form, $title = '')
    {
        $this->setForm($form);
        $this->id = $this->form->getId();
        if (!$title)    // TODO: Not sure if this is correct???
            $title = ucwords(preg_replace('/[A-Z_-]/', ' $0', $title));
        $this->setTitle($title);
        $this->setButtonList(\Tk\Ui\ButtonCollection::create());

        $this->setAttr('id', $this->getId());
        $this->setAttr('aria-labelledby', $this->getId().'-Label');

        if ($this->form->getField('cancel')) {
            $this->form->getField('cancel')->addCss('float-right')->setAttr('data-dismiss', 'modal');
        }
        if ($this->form->getField('save')) {
            $this->form->getField('save')->addCss('float-right')->setIconLeft('')
                ->setIconRight('fa fa-arrow-right')->appendCallback(array($this, 'doSubmit'));
        }
        if ($this->form->getField('update')) {
            $this->form->removeField('update');
        }

        $this->init();
    }

    /**
     * @param \Tk\Form $form
     * @param string $title
     * @return static
     */
    public static function createFormDialog($form, $title = '')
    {
        $obj = new static($form, $title);
        return $obj;
    }

    /**
     * @return \Tk\Form
     */
    public function getForm(): \Tk\Form
    {
        return $this->form;
    }

    /**
     * @param \Tk\Form $form
     * @return $this
     */
    public function setForm(\Tk\Form $form)
    {
        $this->form = $form;
        return $this;
    }

    /**
     * @return \Dom\Template
     */
    public function show()
    {
        $dialogTemplate = parent::show();


        return $dialogTemplate;
    }
}
