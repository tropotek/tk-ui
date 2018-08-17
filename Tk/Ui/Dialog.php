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
    public function getTitle(): string
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
     * @param string|\Dom\Template|\DOMDocument $html
     * @return $this
     */
    public function setContent($html) {
        $this->content = $html;
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
     * @return \Dom\Template
     */
    public function show()
    {
        $template = $this->getTemplate();

        $this->setAttr('id', $this->getId());
        $this->setAttr('aria-labelledby', $this->getId().'-Label');

        if ($this->content instanceof \Dom\Template) {
            $template->insertTemplate('content', $this->content);
        } else if ($this->content instanceof \DOMDocument) {
            $template->insertHtml('content', $this->content);
        } else {
            $template->insertHtml('content', $this->content);
        }

        $template->appendTemplate('footer', $this->buttonList->show());
        $template->insertText('title', $this->getTitle());
        $template->setAttr('title', 'id', $this->getId().'-Label');

        // Add attributes
        $template->setAttr('dialog', $this->getAttrList());
        $template->addCss('dialog', $this->getCssList());

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
<div class="modal fade in" id="exampleModal" tabindex="-1" role="dialog" aria-hidden="true" aria-labelledby="_exampleModalLabel" var="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="_exampleModalLabel" var="title">New message</h4>
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
