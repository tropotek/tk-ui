<?php
namespace Tk\Ui;



/** *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2016 Michael Mifsud
 * @deprecated Use the new \Tk\Ui\Dialog\Dialog object
 */
class DialogBox extends \Dom\Renderer\Renderer implements \Dom\Renderer\DisplayInterface
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
     * @var string|\Dom\Template|\DOMDocument
     */
    protected $content = '';

    /**
     * @var \Tk\Ui\ButtonCollection
     */
    protected $buttonList = null;

    /**
     * @var null|callable
     */
    protected $onInit = null;

    /**
     * @var null|callable
     */
    protected $onShow = null;


    /**
     * DialogBox constructor.
     * @param string $dialogId
     * @param string $title
     * @deprecated Use the new \Tk\Ui\Dialog\Dialog object
     */
    public function __construct($dialogId, $title = '')
    {
        throw new \Tk\Exception('Deprecated Class');

        $this->id = $dialogId;
        if (!$title)
            $title = ucwords(preg_replace('/[A-Z_-]/', ' $0', $title));
        $this->setTitle($title);
        $this->setButtonList(\Tk\Ui\ButtonCollection::create());

        $this->setAttr('id', $this->getId());
        $this->setAttr('aria-labelledby', $this->getId().'-Label');
        $this->getButtonList()->append(\Tk\Ui\Button::createButton('Close')->setAttr('data-dismiss', 'modal'));
    }

    /**
     * @param string $dialogId
     * @param string $title
     * @return static
     * @deprecated Use the new \Tk\Ui\Dialog\Dialog object
     */
    public static function create($dialogId, $title = '')
    {
        $obj = new static($dialogId, $title);
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
    public function getSizeCss(): string
    {
        return $this->sizeCss;
    }

    /**
     * @param string $sizeCss
     * @return DialogBox
     */
    public function setSizeCss(string $sizeCss): DialogBox
    {
        $this->sizeCss = $sizeCss;
        return $this;
    }

    /**
     * @param callable|null $onInit
     * @return DialogBox
     */
    public function setOnInit($onInit)
    {
        $this->onInit = $onInit;
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
     * @return DialogBox
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @param callable|null $onShow
     * @return DialogBox
     */
    public function setOnShow($onShow)
    {
        $this->onShow = $onShow;
        return $this;
    }

    /**
     * @return \Dom\Template
     */
    public function show()
    {
        $template = $this->getTemplate();
        if (is_callable($this->onInit)) {
            call_user_func_array($this->onInit, array($this));
        }

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
}
