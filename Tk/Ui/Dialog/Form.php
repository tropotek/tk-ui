<?php
namespace Tk\Ui\Dialog;

/**
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2016 Michael Mifsud
 */
class Form extends Dialog
{

    /**
     * @var \Tk\Form
     */
    protected $form = null;

    /**
     * Should submit the form via javascript
     * @var bool
     */
    protected $jsSubmit = true;

    /**
     * If true the form is reset once the dialog is closed
     * @var bool
     */
    protected $resetOnHide = true;


    /**.
     * @param \Tk\Form $form
     * @param string $title
     * @param string $dialogId
     */
    public function __construct($form, $title, $dialogId = '')
    {
        parent::__construct($title, $dialogId);
        $this->setButtonList(\Tk\Ui\ButtonCollection::create());
        $this->setLarge(true);
        $this->setForm($form);
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
    public function getForm()
    {
        return $this->form;
    }

    /**
     * @return bool
     */
    public function isJsSubmit()
    {
        return $this->jsSubmit;
    }

    /**
     * @param bool $jsSubmit
     * @return Form
     */
    public function setJsSubmit(bool $jsSubmit)
    {
        $this->jsSubmit = $jsSubmit;
        return $this;
    }

    /**
     * @return bool
     */
    public function isResetOnHide()
    {
        return $this->resetOnHide;
    }

    /**
     * @param bool $resetOnHide
     * @return $this
     */
    public function setResetOnHide(bool $resetOnHide)
    {
        $this->resetOnHide = $resetOnHide;
        return $this;
    }

    /**
     * @param \Tk\Request $request
     */
    public function execute()
    {
        parent::execute();
        if (!$this->getForm()->hasErrors()) {
            $this->getForm()->execute($this->getRequest());
        }
    }

    /**
     * @param \Tk\Form $form
     * @return $this
     */
    public function setForm(\Tk\Form $form)
    {
        $this->form = $form;
        if ($this->form->getField('cancel') && $this->form->getField('cancel')->hasAttr('data-dismiss') ) {
            // NOTE: if there is no attribute it is ASSUMED that the form submit buttons need to be updated
            //       for the form to be submitted within a dialog.
            if ($this->form->getField('cancel')) {
                $this->form->getField('cancel')->addCss('float-right')->setAttr('data-dismiss', 'modal');
            }
            if ($this->form->getField('save')) {
                $this->form->getField('save')->addCss('float-right')->setIconLeft('')->setIconRight('fa fa-arrow-right');
            }
            if ($this->form->getField('update')) {
                $this->form->removeField('update');
            }
        }
        return $this;
    }

    /**
     * @return \Dom\Template
     */
    public function show()
    {
        $template = parent::show();
        if ($this->isResetOnHide()) {
            $template->setAttr('dialog', 'data-reset-on-hide', 'true');
        }

        $js = <<<JS
jQuery(function ($) {
  
  function init() {
    var form = $(this);
    var dialog = form.closest('.modal');
    
    form.on('submit', function (e) {
      e.preventDefault();  // prevent form from submitting
      var f = $(this);
      var input = f.append('<input type="hidden" name="'+f.attr('id')+'-save" value="'+f.attr('id')+'-save" />');
      $.post(f.attr('action'), f.serialize(), function (html) {
        var newEl = $(html).find('#'+f.attr('id'));
        if (!newEl.length) {
          console.error('Error: Form not submitted. Invalid response from server.');
          return false;
        }
        f.empty().append(newEl.find('> div'));
        f.trigger('init');

        if (!f.find('.tk-is-invalid, .is-invalid, .alert-danger').length) {
          // if success then we need to close the dialog and reload the page.
          if (newEl.data('redirect')) {
            document.location = newEl.data('redirect');            
          } else {
            document.location = f.attr('action');
          }
        }
      }, 'html');
      return false;
    });
    
    if (form.find('.has-error, .tk-is-invalid, .alert-danger').length > 0) {
      dialog.modal({show: true});
    }
    if (dialog.data('resetOnHide')) {
      dialog.on('hidden.bs.modal', function () {
        form.trigger('reset'); // Note does not reset file fields
      });
    }
    
  }
  
  $('.modal-body form').on('init', '.modal-dialog', init).each(init);
});
JS;
        if ($this->isJsSubmit())
            $template->appendJs($js);

        $template->appendTemplate('content', $this->getForm()->getRenderer()->show());

        return $template;
    }

}
