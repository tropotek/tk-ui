<?php
namespace Tk\Ui\Dialog;

use Tk\Form\Field\Hidden;

/**
 * This uses javascript to handle the errors and events to
 *   use the returned data and errors.
 *
 * Over time see if this is better than Dialog\Form and rename if that is what we want to use.
 *
 * It converts the submit to a page to a returned JSON object for javascript processing
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2016 Michael Mifsud
 */
class JsonForm extends Dialog
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
        $form->addCss('json-form');
        $this->addCss('tk-json-form');
    }

    /**
     * @param \Tk\Form $form
     * @param string $title
     * @param string $dialogId
     * @return static
     */
    public static function createJsonForm($form, $title, $dialogId = '')
    {
        $obj = new static($form, $title, $dialogId);
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
     * @return $this
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
        if (!$this->form->getField('id')) {
            $this->form->appendField(new Hidden('id'));
        }
        if ($this->form->getField('cancel') && !$this->form->getField('cancel')->hasAttr('data-dismiss')) {
            $this->form->getField('cancel')->addCss('float-right')->setAttr('data-dismiss', 'modal');
            if ($this->form->getField('save')) {
                $this->form->getField('save')->addCss('float-right')->setIconLeft('')->setIconRight('fa fa-arrow-right');
            }
            if ($this->form->getField('update')) {
                $this->form->removeField('update');
            }
        }

        if ($this->form->getField('save')) {
            $this->form->getField('save')
                ->appendCallback(function (\Tk\Form $form, \Tk\Form\Event\Iface $event) {
                    $res = \Tk\ResponseJson::createJson($form->getValues());
                    if ($this->form instanceof \Bs\FormIface)
                        $res = \Tk\ResponseJson::createJson($form->getModel());
                    if ($form->hasErrors()) {
                        $errors = json_encode($form->getAllErrors(), \JSON_FORCE_OBJECT);
                        $res = \Tk\ResponseJson::createJson($errors, \Tk\Response::HTTP_INTERNAL_SERVER_ERROR);
                    }
                    \Tk\Alert::clear();
                    $res->send();
                    exit;
                });
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
        $template->appendTemplate('content', $this->getForm()->getRenderer()->show());

        $js = <<<JS
jQuery(function ($) {
  
  function init() {
    var form = $(this);
    var dialog = form.closest('.modal');
    var fields = dialog.find('input, select, textarea');
    
    var clearErrors = function() {
      dialog.removeClass('has-error');
      dialog.find('.has-error').removeClass('has-error');
      dialog.find('.foot-error').remove();
      dialog.find('.dlg-error').remove();
    };
    
    var showAlert = function() {
      dialog.find('.foot-error').remove();
      if (dialog.find('.has-error').length) {
        // Show error text
        var alert = $('<div class="foot-error text-danger pull-left">' +
            'Invalid required fields please try again.' +
          '</div>');
        dialog.find('.modal-footer').prepend(alert);
        alert.delay(5000).fadeOut('slow');
      }
    };
    
    function isObject(A) {  return A === null || (A && A.toString() === "[object Object]"); };
    
    form.on('submit', function (e) {
      e.preventDefault();  // prevent form from submitting
      var f = $(this);
      var saveBtn = f.find('#'+f.attr('id')+'-save');
      
      // Add save event to request if not already there
      if (saveBtn.length > 0 && f.find('input[type="hidden"][name="'+saveBtn.attr('name')+'"]').length === 0) {
        f.append('<input type="hidden" name="'+saveBtn.attr('name')+'" value="'+saveBtn.attr('value')+'" />');
      }
      // clear any errors
      clearErrors();
      
      // validate fields
      fields.each(function() {
        if ($(this).is('[data-required]') && !$(this).val()) {
          $(this).closest('.form-group').addClass('has-error');
        }
      });
      showAlert();
      
      if (!dialog.find('.has-error').length) {
        $.post(f.attr('action'), f.serialize(), function (data) {
          dialog.trigger('DialogForm:submit', [data]);
          dialog.modal('hide');
        }).fail(function(xhr) {     // NOTE do not use the specific data type here like 'json' or 'html' or else it wont work
          // post any errors
          clearErrors();
          var errHtml = '<p>Errors:</p><ul>';
          var errMsg = 'Errors:\\n';
          $.each(xhr.responseJSON, function(k, v) {            
            dialog.find('[name='+k+']').closest('.form-group').addClass('has-error');
            var errField = '';
            if (isObject(v)) {
              $.each(v,  function (i, j) { 
                errHtml += '<li>'+j+'</li>';
                errMsg += '\\t'+j+'\\n';
                if (i == 0)
                  errField += j+'';
              });
            } else {
              errHtml += '<li>'+v+'</li>';
              errMsg += '\\t'+v+'\\n';
              errField += v+'';
            }
            dialog.find('[name='+k+']').closest('.form-group').append('<small class="text-error dlg-error">'+errField+'</small>');
          });
          errHtml += '</ul>';
          errMsg += '\\n';
          //console.log(xhr);
          dialog.trigger('DialogForm:error', [xhr, errMsg, errHtml]);
          showAlert();
        });
      }
      
      return false;
    });
        
    dialog.on('hidden.bs.modal', function () {
      clearErrors();  // clear any errors
    });
    
    dialog.on('shown.bs.modal', function () {
      fields.first().focus();
    });
    
    if (dialog.data('resetOnHide') !== undefined && dialog.data('resetOnHide')) {
       // Clear the form here, as it causes unwanted issues anywhere else
      dialog.on('hidden.bs.modal', function () {
        //fields.val('');
        $(fields.get(0).form).trigger('reset');
        $('button').blur();
      });
    }
    
  }
  
  $('.modal-body form.json-form').on('init', '.modal-dialog', init).each(init);
  //$('.modal-body form').on('init', '.modal.tk-json-form', init).each(init);
});
JS;
        if ($this->isJsSubmit())
            $template->appendJs($js);

        return $template;
    }

}
