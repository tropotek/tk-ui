<?php
namespace Tk\Ui\Dialog;

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
    use \Tk\Dom\AttributesTrait;
    use \Tk\Dom\CssTrait;

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
     * @var null|callable
     */
    protected $onInit = null;

    /**
     * @var null|callable
     */
    protected $onExecute = null;

    /**
     * @var null|callable
     */
    protected $onShow = null;


    /**
     * @param string $dialogId
     * @param string $title
     */
    public function __construct($title = '')
    {
        $this->id = $this->makeIdHash($title);
        if (!$title)
            $title = ucwords(preg_replace('/[A-Z_-]/', ' $0', $title));
        $this->setTitle($title);
        $this->setButtonList(\Tk\Ui\ButtonCollection::create());

        $this->setAttr('id', $this->getId());
        $this->setAttr('aria-labelledby', $this->getId().'-Label');
        $this->getButtonList()->append(\Tk\Ui\Button::createButton('Close')->setAttr('data-dismiss', 'modal'));
    }

    /**
     * @param string $title
     * @return static
     */
    public static function create($title = '')
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
     * @return bool
     */
    public function isLarge()
    {
        return $this->large;
    }

    /**
     * @param bool $large
     * @return $this
     */
    public function setLarge($large)
    {
        $this->large = $large;
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
     * @param callable|null $onInit
     * @return $this
     * @throws \Tk\Exception
     */
    public function setOnInit($onInit)
    {
        if (!is_callable($onInit))
            throw new \Tk\Exception('Invalid callable object given');
        $this->onInit = $onInit;
        return $this;
    }

    /**
     * @param callable|null $onExecute
     * @return $this
     * @throws \Tk\Exception
     */
    public function setOnExecute($onExecute)
    {
        if (!is_callable($onExecute))
            throw new \Tk\Exception('Invalid callable object given');
        $this->onExecute = $onExecute;
        return $this;
    }

    /**
     * @param callable|null $onShow
     * @return $this
     * @throws \Tk\Exception
     */
    public function setOnShow($onShow)
    {
        if (!is_callable($onShow))
            throw new \Tk\Exception('Invalid callable object given');
        $this->onShow = $onShow;
        return $this;
    }

    public function init()
    {
        if (is_callable($this->onInit)) {
            call_user_func_array($this->onInit, array($this));
        }
    }

    public function execute(\Tk\Request $request)
    {
        if (is_callable($this->onExecute)) {
            call_user_func_array($this->onExecute, array($request, $this));
        }
    }

    /**
     * @return \Dom\Template
     */
    public function show()
    {
        $template = $this->getTemplate();
        if (is_callable($this->onShow)) {
            call_user_func_array($this->onShow, array($this));
        }

        $template->appendTemplate('footer', $this->buttonList->show());
        $template->insertText('title', $this->getTitle());
        $template->setAttr('title', 'id', $this->getId().'-Label');

        if ($this->getContent()) {
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
<div class="modal" id="exampleModal" tabindex="-1" role="dialog" aria-hidden="true" aria-labelledby="_exampleModalLabel" var="dialog">
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