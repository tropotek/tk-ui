<?php
namespace Tk\Ui;


/**
 * @author Michael Mifsud <http://www.tropotek.com/>
 * @link http://www.tropotek.com/
 * @license Copyright 2016 Michael Mifsud
 * @deprecated Use the new \Tk\Ui\Dialog\Dialog object
 */
class FormDialog extends Dialog
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
        throw new \Tk\Exception('Deprecated Class, use "\Tk\Ui\Dialog\Form".');

        $this->setLarge(true);
        $this->setForm($form);
        $this->id = $this->form->getId().'-dialog';
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
            $this->form->getField('save')->addCss('float-right')->setIconLeft('')->setIconRight('fa fa-arrow-right');
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
    public function getForm()
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

        $js = <<<JS
jQuery(function ($) {
  
  function init() {
    var form = $(this);
    form.on('submit', function (e) {
      e.preventDefault();  // prevent form from submitting
      var f = $(this);
      f.append('<input type="hidden" name="'+f.attr('id')+'-save" value="'+f.attr('id')+'-save" />');
      $.post(f.attr('action'), f.serialize(), function (html) {
        var newEl = $(html).find('#'+f.attr('id'));
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
      return false;
    });
  }
  
  $('.modal-body form').on('init', '.modal-dialog', init).each(init);
});
JS;
        $dialogTemplate->appendJs($js);

        // TODO: Check this does not interfere with anything
        $dialogTemplate->appendTemplate('content', $this->getForm()->getRenderer()->show());

        return $dialogTemplate;
    }


    /**
     * Override this for your own dialogs not the show method
     * @return \Dom\Template|\DOMDocument|string
     * @todo NOTE it would be nice to find a way to use show but that does not seem to be an elegant option.
     */
    public function doShow()
    {

    }
}
