<?php
namespace Tk\Ui\Dialog;


use Tk\Callback;

/**
 * To create the dialog:
 *
 *   $ajaxSelect = \Tk\Ui|AjaxSelect::create('Select Module For New Entry');
 *   $ajaxSelect->setNotes('Select the module to create the new assessment entry for.');
 *   $ajaxSelect->setOnAjax(function ($dialog) {
 *   $config = \App\Config::getInstance();
 *       $filter = array();
 *       return \App\Db\ObjectMap::create()->findFiltered($filter, \Tk\Db\Tool::create())->toArray();
 *   });
 *   $ajaxSelect->setOnSelect(function ($dialog) {
 *       $config = \App\Config::getInstance();
 *       $selectedId = (int)$request->get('selectedId');
 *       $obj = \App\Db\ObjectMap::create()->find($selectedId);
 *       if (!$obj) {
 *           \Tk\Alert::addWarning('Invalid module selected.');
 *           return \Tk\Uri::create();
 *       }
 *       \Tk\Alert::addSuccess('Some wiz bang message');
 *       return \Tk\Uri::create();
 *   });
 *   $ajaxSelect->execute($request);
 *   ...
 *   $template->appendBodyTemplate($ajaxSelect->show());
 *
 * To add a close button to the footer:
 *
 *    $dialog->getButtonList()->append(\Tk\Ui\Button::createButton('Close')->setAttr('data-dismiss', 'modal'));
 *
 * Launch Button:
 *
 *    <a href="#" data-toggle="modal" data-target="#{id}"><i class="fa fa-info-circle"></i> {title}</a>
 *
 *    $template->setAttr('modelBtn', 'data-toggle', 'modal');
 *    $template->setAttr('modelBtn', 'data-target', '#'.$ajaxSelect->getId());
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @see http://www.tropotek.com/
 * @license Copyright 2016 Michael Mifsud
 */
class AjaxSelect extends Dialog
{
    /**
     * @var Callback
     */
    protected $onSelect = null;

    /**
     * @var Callback
     */
    protected $onAjax = null;

    /**
     * @var \Tk\Uri
     */
    protected $ajaxUrl = null;

    /**
     * @var array
     */
    protected $ajaxParams = array();

    /**
     * @var array
     */
    protected $data = array();

    /**
     * @var string
     */
    protected $notes = '';


    /**
     * @param $title
     * @param \Tk\Uri|string|null $ajaxUrl
     * @param string $dialogId
     */
    public function __construct($title, $ajaxUrl = null, $dialogId = '')
    {
        $this->onSelect = Callback::create();
        $this->onAjax = Callback::create();
        parent::__construct($title, $dialogId);
        $this->ajaxUrl = \Tk\Uri::create()->set('ajaxSelect', $this->getId());
        if ($ajaxUrl)
            $this->setAjaxUrl(\Tk\Uri::create($ajaxUrl));
        $this->addCss('tk-dialog-ajax-select');
    }

    /**
     *
     * @param string $title
     * @return AjaxSelect|Dialog
     */
    public static function create($title)
    {
        return new self($title);
    }

    /**
     * @param $params
     * @return $this
     */
    public function setAjaxParams($params)
    {
        $this->ajaxParams = $params;
        return $this;
    }

    /**
     * @return \Tk\Uri
     */
    public function getAjaxUrl(): \Tk\Uri
    {
        return $this->ajaxUrl;
    }

    /**
     * @param \Tk\Uri|string $ajaxUrl
     * @return AjaxSelect
     */
    public function setAjaxUrl($ajaxUrl): AjaxSelect
    {
        $this->ajaxUrl = \Tk\Uri::create($ajaxUrl);
        return $this;
    }

    /**
     * @return Callback
     */
    public function getOnSelect()
    {
        return $this->onSelect;
    }

    /**
     * Callable: function($dialog) {}
     *
     * @param callable $onSelect
     * @return $this
     * @deprecated Use $this->addOnSelect($callable,$priority)
     */
    public function setOnSelect($onSelect)
    {
        $this->addOnSelect($onSelect);
        return $this;
    }

    /**
     * Callable: function($dialog) {}
     *
     * @param callable $callable
     * @param int $priority
     * @return $this
     */
    public function addOnSelect($callable, $priority = Callback::DEFAULT_PRIORITY)
    {
        $this->getOnSelect()->append($callable, $priority);
        return $this;
    }

    /**
     * @return null|string
     */
    public function getSelectedId()
    {
        if ($this->getRequest()->has('selectedId'))
            return $this->getRequest()->get('selectedId');
        return '';
    }

    /**
     * @return Callback
     */
    public function getOnAjax()
    {
        return $this->onAjax;
    }

    /**
     * Callable: function(\Tk\Request $request) : array|object {}
     * Return the list of items in the form of:
     *   array(
     *     'id' => {int},
     *     'name' => {string}
     *   )
     *
     * @param callable $onAjax
     * @return $this
     * @deprecated use $this->addOnAjax($callable, $priority)
     */
    public function setOnAjax($onAjax)
    {
        $this->addOnAjax($onAjax);
        return $this;
    }

    /**
     * Callable: function($dialog) {}
     *
     * @param callable $callable
     * @param int $priority
     * @return $this
     */
    public function addOnAjax($callable, $priority = Callback::DEFAULT_PRIORITY)
    {
        $this->getOnAjax()->append($callable, $priority);
        return $this;
    }

    /**
     * @return string
     */
    public function getSelectButtonId()
    {
        return $this->getId().'-select';
    }

    /**
     * @param $notes
     * @return $this
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;
        return $this;
    }

    /**
     * @return array
     */
    public function getData()
    {
        $this->data = $this->getRequest()->all();
        return $this->data;
    }

    /**
     * @return array
     */
    public function getAjaxParams()
    {
        return $this->ajaxParams;
    }

    /**
     * @return string
     */
    public function getNotes()
    {
        return $this->notes;
    }


    /**
     *
     */
    public function execute()
    {
        parent::execute();

        if ($this->getRequest()->get('ajaxSelect') == $this->getId()) {
            $data = $this->getOnAjax()->execute($this);
            \Tk\ResponseJson::createJson($data)->send();
            exit();
        }

        // Fire the callback if set
        if ($this->getRequest()->has($this->getSelectButtonId())) {
            $redirect = \Tk\Uri::create();
            $url = $this->getOnSelect()->execute($this);
            if ($url instanceof \Tk\Uri) {
                $redirect = $url;
            }
            $redirect->remove($this->getSelectButtonId())->remove($this->getSelectButtonId())->remove('selectedId')->redirect();
        }
    }

    /**
     * @return \Dom\Template
     */
    public function show()
    {
        /** @var \Dom\Template $selectTemplate */
        $selectTemplate = $this->__makeSelectTemplate();

        if ($this->getNotes()) {
            $selectTemplate->insertHtml('notes', $this->getNotes());
            $selectTemplate->setVisible('notes');
        }
        $ajaxParams = json_encode($this->getAjaxParams(),\JSON_FORCE_OBJECT);
        $this->setAttr('data-ajax-params', $ajaxParams);

        // deprecated use onAjax callback
        $ajaxUrl = $this->getAjaxUrl()->toString();
        $actionUrl = \Tk\Uri::create()->set($this->getSelectButtonId())->toString();
        $this->setAttr('data-ajax-url', $ajaxUrl);
        $this->setAttr('data-action-url', $actionUrl);

        $js = <<<JS
jQuery(function($) {

  $('.tk-dialog-ajax-select').each(function () {
    var dialog = $(this);
    var settings = $.extend({}, {
        selectParam : 'id'
      }, dialog.data());

    var launchBtn = null;
    var launchData = {};
    processing(false);

    dialog.find('.btn-search').click(function(e) {
      processing(true);
      //if (dialog.find('.input-search').val())
        settings.ajaxParams.keywords = dialog.find('.input-search').val();
      $.get(settings.ajaxUrl, settings.ajaxParams, function (data) {
        var panel = dialog.find('.dialog-table').empty();
        var table = buildTable(data);
        panel.append(table);
        processing(false);
      });
    });

    function buildTable(data) {
      if (data.length === 0) {
        return $('<p class="text-center" style="margin-top: 10px;font-weight: bold;font-style: italic;">No Data Found!</p>');
      }
      //let table = $('<table class="table" style="margin-top: 10px;"><tr><th>ID</th><th>Name</th></tr> <tr class="data-tpl"><td class="cell-id"></td><td class="cell-name"><a href="javascript:;" class="cell-name-url"></a></td></tr> </table>');
      var table = $('<table class="table" style="margin-top: 10px;"><tr><th>Name</th></tr> <tr class="data-tpl"><td class="cell-name"><a href="javascript:;" class="cell-name-url"></a></td></tr> </table>');
      $.each(data, function (i, obj) {
        var row = table.find('tr.data-tpl').clone();
        row.removeClass('data-tpl').addClass('data');
        //vd(obj);
        var href = settings.actionUrl+'&selectedId=' + obj[settings.selectParam];
        if (!$.isEmptyObject(launchData)) {
          href += '&' + $.param(launchData);
        }
        row.find('.cell-name-url').text(obj.name).attr('href', href).on('click', function (e) {
          if (dialog.data('itemOnClick')) {
            return dialog.data('itemOnClick').apply(this, dialog);
          }
          //$(this).on('click', function() {return false;});
        });
        //row.find('.cell-id').text(obj.id);
        table.find('tr.data-tpl').after(row);
      });
      table.find('tr.data-tpl').remove();

      return table;
    }

    function processing(bool) {
      if (bool) {
        dialog.find('.form-control-feedback').show();
        dialog.find('.input-search').attr('disabled', 'disabled');
        dialog.find('.btn-search').attr('disabled', 'disabled');
        dialog.find('.cell-name-url').addClass('disabled');
      } else {
        dialog.find('.form-control-feedback').hide();
        dialog.find('.input-search').removeAttr('disabled');
        dialog.find('.btn-search').removeAttr('disabled');
        dialog.find('.cell-name-url').removeClass('disabled');
      }
    }

    // Some focus and key logic
    dialog.on('shown.bs.modal', function (e) {
      dialog.find('.input-search').val('').focus();
      launchBtn = $(e.relatedTarget);
      launchData = {};
      $.each(launchBtn.data(), function (k, v) {
        if (k === 'toggle' || k === 'target' || k === 'trigger') return;
        if (typeof v === 'string' || typeof v === 'number')
          launchData[k] = v;
      });
      dialog.find('.btn-search').click();
    });

    dialog.find('.input-search').on('keyup', function(e) {
      var code = (e.keyCode ? e.keyCode : e.which);
      if(code === 13) { //Enter keycode
          dialog.find('.btn-search').click();
      }
    });

  });

});
JS;
        $selectTemplate->appendJs($js);

        $template = parent::show();
        $template->appendTemplate('content', $selectTemplate);

        return $template;
    }

    /**
     * @return \Dom\template
     */
    public function __makeSelectTemplate()
    {
        $xhtml = <<<HTML
<div class="row">
  <div class="col-md-12">
    <p var="notes" choice="notes"></p>
    <div class="input-group">
      <input type="text" placeholder="Search by keyword ..." name="keywords" class="form-control input-sm input-search"/>
      <div class="form-control-feedback" style="">
        <i class="fa fa-spinner fa-spin"></i>
      </div>
      <span class="input-group-btn">
        <button type="button" class="btn btn-default btn-sm btn-search">Go!</button>
      </span>
    </div><!-- /input-group -->
  </div>

  <div class="col-md-12" >
    <div class="dialog-table" style="min-height: 100px;"></div>
  </div>
</div>
HTML;
        return \Dom\Loader::load($xhtml, get_class($this).'2');
    }
}
