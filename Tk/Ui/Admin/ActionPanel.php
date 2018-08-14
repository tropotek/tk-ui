<?php
namespace Tk\Ui\Admin;


/**
 * @author Michael Mifsud <info@tropotek.com>
 * @see http://www.tropotek.com/
 * @license Copyright 2017 Michael Mifsud
 */
class ActionPanel extends \Tk\Ui\ButtonCollection
{
    /**
     * @var string
     */
    protected $title = '';
    /**
     * icon css 'fa fa-user'
     * @var string
     */
    protected $icon = '';

    /**
     * @var bool
     */
    protected $enabled = true;



    /**
     * @param string $title
     * @param string $icon
     * @return static
     */
    public static function create($title = 'Actions', $icon = 'fa fa-cogs')
    {
        $obj = new static();
        if ($title) $obj->setTitle($title);
        if ($icon) $obj->setIcon($icon);
        return $obj;
    }

    /**
     * @return \Dom\Template
     */
    public function show()
    {
        $template = parent::show();


        if ($this->title)
            $template->insertText('text', $this->title);
        if ($this->icon)
            $template->addCss('icon', $this->icon);

        if (!$this->isEnabled() || !$this->isVisible()) {
            $template->hide('panel');
        }

        return $template;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
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
     * @return string
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * @param string $icon
     * @return $this
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;
        return $this;
    }

    /**
     * makeTemplate
     *
     * @return \Dom\Template
     */
    public function __makeTemplate()
    {
        $html = <<<HTML
<div class="panel panel-default panel-shortcut tk-ui-action-panel" var="panel">
  <div class="panel-heading" var="heading">
    <h4 class="panel-title" var="title"><span><i var="icon"></i> <span var="text"></span></span></h4>
  </div>
  <div class="panel-body" var="body"></div>
</div>
HTML;
        return \Dom\Loader::load($html);
    }


}