<?php
namespace Tk\Ui\Dialog;

use Tk\Callback;
use Tk\ConfigTrait;
use Tk\Dom\AttributesTrait;
use Tk\Dom\CssTrait;
use Tk\Ui\ButtonCollection;

/**
 * This class uses the bootstrap dialog box model
 * @link http://getbootstrap.com/javascript/#modals
 *
 * To create the dialog:
 *
 *   $dialog = Dialog::create('myDialog', 'My Dialog Title');
 *   $dialog->setOnInit(function ($dialog) { ... });
 *   $dialog->setOnShow(function ($dialog) { $template = $dialog->getTemplate(); });
 *   ...
 *   $dialog->init();                   // Optional
 *   ...
 *   $dialog->execute($request);        // Optional
 *   ...
 *   $template->appendBodyTemplate($dialog->show());
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
 *    $template->setAttr('modelBtn', 'data-target', '#'.$this->dialog->getId());
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2016 Michael Mifsud
 */
class Dialog extends \Dom\Renderer\Renderer implements \Dom\Renderer\DisplayInterface
{
    use AttributesTrait;
    use CssTrait;
    use ConfigTrait;

    /**
     * @var string
     */
    protected $id = '';

    /**
     * @var string
     */
    protected $title = '';

    /**
     * @var string
     */
    protected $sizeCss = '';

    /**
     * @var bool
     */
    protected $large = false;

    /**
     * @var \Tk\Ui\ButtonCollection
     */
    protected $buttonList = null;

    /**
     * @var string|\Dom\Template|\DOMDocument
     */
    protected $content = '';

    /**
     * @var Callback
     */
    protected $onInit = null;

    /**
     * @var Callback
     */
    protected $onExecute = null;

    /**
     * @var Callback
     */
    protected $onShow = null;

    /**
     * @var int
     */
    public static $instanceId = 0;


    /**
     * @param string $title
     * @param string $dialogId
     */
    public function __construct($title, $dialogId = '')
    {
        $this->onInit = Callback::create();
        $this->onExecute = Callback::create();
        $this->onShow = Callback::create();
        $this->id = $dialogId;
        if (!$this->id)
            $this->id = $this->makeIdHash($title.'-'.self::$instanceId);
        if (!$title)
            $title = ucwords(preg_replace('/[A-Z_-]/', ' $0', $title));

        $this->setTitle($title);
        $this->setButtonList(\Tk\Ui\ButtonCollection::create());

        $this->setAttr('id', $this->getId());
        $this->setAttr('aria-labelledby', $this->getId().'-Label');
        $this->getButtonList()->append(\Tk\Ui\Button::createButton('Close')->setAttr('data-dismiss', 'modal'));
        self::$instanceId++;
    }

    /**
     * @param string $title
     * @return static
     */
    public static function create($title)
    {
        $obj = new static($title);
        return $obj;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return ButtonCollection
     */
    public function getButtonList()
    {
        return $this->buttonList;
    }

    /**
     * @param ButtonCollection $buttonList
     * @return static
     */
    public function setButtonList(ButtonCollection $buttonList)
    {
        $this->buttonList = $buttonList;
        return $this;
    }

    /**
     * Add Button, just a helper function
     *
     * @param string $name
     * @param array $attributes
     * @param string $icon
     * @return \Tk\Ui\Button|\Tk\Ui\Element
     */
    public function addButton($name, $attributes = array(), $icon = '')
    {
        if (strtolower($name) == 'close' || strtolower($name) == 'cancel') {
            $attributes['data-dismiss'] = 'modal';
        }
        $attributes['name'] = $name;
        $attributes['id'] = $this->getId() . '-' . preg_replace('/[^a-z0-9]/i', '_', $name);
        $btn = $this->getButtonList()->append(\Tk\Ui\Button::createButton($name, $icon)->setAttr($attributes));
        return $btn;
    }

    /**
     * @return bool
     */
    public function isLarge()
    {
        return $this->large;
    }

    /**
     * @param bool $b
     * @return $this
     */
    public function setLarge($b = true)
    {
        $this->large = $b;
        return $this;
    }

    /**
     * @return string
     */
    public function getSizeCss()
    {
        return $this->sizeCss;
    }

    /**
     * @param string $sizeCss
     * @return $this
     */
    public function setSizeCss(string $sizeCss)
    {
        $this->sizeCss = $sizeCss;
        return $this;
    }

    /**
     * @return \Dom\Template|\DOMDocument|string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param \Dom\Template|\DOMDocument|string $content
     * @return $this
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @return Callback
     */
    public function getOnInit()
    {
        return $this->onInit;
    }

    /**
     * @param callable|null $onInit
     * @return $this
     * @deprecated use $this->addOnInit($callable, $priority)
     */
    public function setOnInit($onInit)
    {
        $this->addOnInit($onInit);
        return $this;
    }

    /**
     * function ($dialog) {}
     *
     * @param callable $callable
     * @param int $priority
     * @return $this
     */
    public function addOnInit($callable, $priority = Callback::DEFAULT_PRIORITY)
    {
        $this->getOnInit()->append($callable, $priority);
        return $this;
    }

    /**
     * @return Callback
     */
    public function getOnExecute()
    {
        return $this->onExecute;
    }

    /**
     * @param callable|null $onExecute
     * @return $this
     * @deprecated use $this->addOnExecute($callable, $priority)
     */
    public function setOnExecute($onExecute)
    {
        $this->addOnExecute($onExecute);
        return $this;
    }

    /**
     * function ($dialog) {}
     *
     * @param callable $callable
     * @param int $priority
     * @return $this
     */
    public function addOnExecute($callable, $priority = Callback::DEFAULT_PRIORITY)
    {
        $this->getOnExecute()->append($callable, $priority);
        return $this;
    }

    /**
     * @return Callback
     */
    public function getOnShow()
    {
        return $this->onShow;
    }

    /**
     * @param callable|null $onShow
     * @return $this
     */
    public function setOnShow($onShow)
    {
        $this->addOnShow($onShow);
        return $this;
    }

    /**
     * function ($dialog) {}
     *
     * @param callable $callable
     * @param int $priority
     * @return $this
     */
    public function addOnShow($callable, $priority = Callback::DEFAULT_PRIORITY)
    {
        $this->getOnShow()->append($callable, $priority);
        return $this;
    }


    /**
     *
     */
    public function init()
    {
        $this->getOnInit()->execute($this);
//        if (is_callable($this->onInit)) {
//            call_user_func_array($this->onInit, array($this));
//        }
    }

    /**
     *
     */
    public function execute()
    {
        $this->getOnExecute()->execute($this);
//        if (is_callable($this->onExecute)) {
//            call_user_func_array($this->onExecute, array($request, $this));
//        }
    }

    /**
     * @return \Dom\Template
     */
    public function show()
    {
        $template = $this->getTemplate();
        $this->getOnShow()->execute($this);
//        if (is_callable($this->onShow)) {
//            call_user_func_array($this->onShow, array($this));
//        }
        $template->appendTemplate('footer', $this->buttonList->show());
        $template->insertText('title', $this->getTitle());
        $template->setAttr('title', 'id', $this->getId().'-Label');

        if ($this->getContent() instanceof \DOMDocument) {
            $template->appendDoc('content', $this->getContent());
        } else if ($this->getContent() instanceof \Dom\Template) {
            $template->appendTemplate('content', $this->getContent());
        } else if ($this->getContent()) {
            $template->appendHtml('content', $this->getContent());
        }

        // Add attributes
        $template->setAttr('dialog', $this->getAttrList());
        $template->addCss('dialog', $this->getCssList());

        if ($this->isLarge()) {
            $template->addCss('modal-dialog', 'modal-lg');
        } else if ($this->getSizeCss()) {
            $template->addCss('modal-dialog', $this->getSizeCss());
        }

        return $template;
    }

    /**
     * DomTemplate magic method
     *
     * @return \Dom\Template
     */
    public function __makeTemplate()
    {
        $xhtml = <<<HTML
<div class="modal" id="" tabindex="-1" role="dialog" aria-hidden="true" aria-labelledby="_exampleModalLabel" var="dialog">
  <div class="modal-dialog" role="document" var="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="_exampleModalLabel" var="title"></h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body" var="content"></div>
      <div class="modal-footer" var="footer"></div>
    </div>
  </div>
</div>
HTML;
        return \Dom\Loader::load($xhtml);
    }

    /**
     * Create an alpha hash for unique ID for dialogs if needed
     *
     * @param string $seed
     * @return bool|string
     */
    protected function makeIdHash($seed)
    {
        $hash = substr(strtolower(preg_replace('/[0-9_\/]+/','',base64_encode(sha1($seed)))),0,8);
        return $hash;
    }

}
