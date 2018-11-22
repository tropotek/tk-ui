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
abstract class Dialog extends \Dom\Renderer\Renderer implements \Dom\Renderer\DisplayInterface
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
     * DialogBox constructor.
     * @param string $dialogId
     * @param string $title
     */
    public function __construct($dialogId, $title = '')
    {
        $this->id = $dialogId;
        if (!$title)
            $title = ucwords(preg_replace('/[A-Z_-]/', ' $0', $title));
        $this->setTitle($title);
        $this->setButtonList(\Tk\Ui\ButtonCollection::create());

        $this->setAttr('id', $this->getId());
        $this->setAttr('aria-labelledby', $this->getId().'-Label');
        $this->getButtonList()->append(\Tk\Ui\Button::createButton('Close')->setAttr('data-dismiss', 'modal'));

        $this->init();
    }

    /**
     * @param string $dialogId
     * @param string $title
     * @return static
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
     * Override this if you need it
     * It will be called after the constructor is executed
     */
    public function init() {}

    /**
     * Override this for your own dialogs not the show method
     * @return \Dom\Template|\DOMDocument|string
     * @todo NOTE it would be nice to find a way to use show but that does not seem to be an elegant option.
     */
    abstract public function doShow();

    /**
     * @return \Dom\Template
     */
    public function show()
    {
        $dialogTemplate = $this->makeDialogTemplate();

        $template = $this->doShow();
        if ($template instanceof \Dom\Template) {
            $dialogTemplate->insertTemplate('content', $template);
        } else if ($template instanceof \DOMDocument) {
            $dialogTemplate->insertDoc('content', $template);
        } else {
            $dialogTemplate->insertHtml('content', $template);
        }

        $dialogTemplate->appendTemplate('footer', $this->buttonList->show());
        $dialogTemplate->insertText('title', $this->getTitle());
        $dialogTemplate->setAttr('title', 'id', $this->getId().'-Label');

        // Add attributes
        $dialogTemplate->setAttr('dialog', $this->getAttrList());
        $dialogTemplate->addCss('dialog', $this->getCssList());

        if ($this->isLarge()) {
            $dialogTemplate->addCss('modal-dialog', 'modal-lg');
        }

        return $dialogTemplate;
    }


    /**
     * DomTemplate magic method
     *
     * @return \Dom\Template
     */
    public function makeDialogTemplate()
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
