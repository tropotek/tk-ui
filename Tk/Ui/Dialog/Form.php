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
     * Should we try to submit the form via javascript
     * @var bool
     */
    protected $jsSubmit = true;


    /**.
     * @param \Tk\Form $form
     * @param string $title
     */
    public function __construct($form, $title = '')
    {
        parent::__construct($title);
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
    public function isJsSubmit(): bool
    {
        return $this->jsSubmit;
    }

    /**
     * @param bool $jsSubmit
     * @return Form
     */
    public function setJsSubmit(bool $jsSubmit): Form
    {
        $this->jsSubmit = $jsSubmit;
        return $this;
    }

    /**
     * @param \Tk\Form $form
     * @return $this
     */
    public function setForm(\Tk\Form $form)
    {
        $this->form = $form;
        if ($this->form->getField('cancel')) {
            $this->form->getField('cancel')->addCss('float-right')->setAttr('data-dismiss', 'modal');
        }
        if ($this->form->getField('save')) {
            $this->form->getField('save')->addCss('float-right')->setIconLeft('')->setIconRight('fa fa-arrow-right');
        }
        if ($this->form->getField('update')) {
            $this->form->removeField('update');
        }
        return $this;
    }

    /**
     * @return \Dom\Template
     */
    public function show()
    {
        $template = parent::show();

        $js = <<<JS
jQuery(function ($) {
  
  function init() {
    var form = $(this);
    form.on('submit', function (e) {
      e.preventDefault();  // prevent form from submitting
      var f = $(this);
      var input = f.append('<input type="hidden" name="'+f.attr('id')+'-save" value="'+f.attr('id')+'-save" />');
      $.post(f.attr('action'), f.serialize(), function (html) {
        var newEl = $(html).find('#'+f.attr('id'));
          console.log(newEl);
        if (!newEl.length) {
          console.error('Error: From not submitted. Invalid response from server.');
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
      input.remove();
      return false;
    });
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
