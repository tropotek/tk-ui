<?php
namespace Tk\Ui;



/**
 * This class uses the bootstrap dialog box model
 * @link http://getbootstrap.com/javascript/#modals
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
    protected $title = '';

    /**
     * @var string
     */
    protected $content = '';

    /**
     * @var array|Button[]
     */
    protected $buttonList = array();


    /**
     * DialogBox constructor.
     * @param $title
     * @param string $content
     */
    public function __construct($title, $content = '')
    {
        $this->setTitle($title);
        $this->setContent($content);
    }

    /**
     * @return string
     */
    public function getId()
    {
        return 'fid-' . preg_replace('/[^a-z0-9]/i', '_', $this->title);
    }

    /**
     * @param string $title
     * @return $this
     */
    public function setTitle($title) {
        $this->title = $title;
        return $this;
    }

    /**
     * @param string $html
     * @return $this
     */
    public function setContent($html) {
        $this->content = $html;
        return $this;
    }

    /**
     * @param Button $button
     * @param null|Button $refButton
     * @return $this
     */
    public function appendButton($button, $refButton = null)
    {
        if (strtolower($name) == 'close' || strtolower($name) == 'cancel') {
            $attributes['data-dismiss'] = 'modal';
        }
        $attributes['name'] = $name;
        $attributes['id'] = $this->getId() . '-' . preg_replace('/[^a-z0-9]/i', '_', $name);

        $this->buttonList[] = array(
            'name' => $name,
            'attributes' => $attributes,
            'icon' => $icon
        );
        return $this;
    }


    public function findButton($button)
    {
        
    }





    /**
     * @return \Dom\Template
     * @throws \Exception
     */
    public function show()
    {
        $template = $this->getTemplate();

        $this->setAttr('id', $this->getId());
        $this->setAttr('aria-labelledby', $this->getId().'-Label');


        $template->insertText('title', $this->title);
        if ($this->content instanceof \Dom\Template) {
            $template->insertTemplate('body', $this->content);
        } else if ($this->content instanceof \DOMDocument) {
            $template->insertHtml('body', $this->content);
        } else {
            $template->insertHtml('body', $this->content);
        }


        foreach ($this->buttonList as $btn) {
            $row = $template->getRepeat('btn');
            $row->insertText('name', $btn['name']);
            if ($btn['icon']) {
                $row->setChoice('icon');
                $row->addCss('icon', $btn['icon']);
            }
            foreach ($btn['attributes'] as $k => $v) {
                $row->setAttr('btn', strip_tags($k), $v);
            }
            $row->appendRepeat();
        }

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
<div class="modal fade in" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="_exampleModalLabel" var="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="_exampleModalLabel" var="title">New message</h4>
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
