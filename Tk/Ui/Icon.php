<?php
namespace Tk\Ui;


/**
 * <code>
 *   \Tk\Ui\FontIcon::create('fa fa-users')->show();
 * </code>
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @see http://www.tropotek.com/
 * @license Copyright 2017 Michael Mifsud
 */
class Icon extends Element
{

    /**
     * @param string|Icon $icon   Example 'fa fa-user'
     * @return static
     */
    public static function create($icon)
    {
        if($icon instanceof Icon) return $icon;
        $obj = new static();
        if (is_string($icon))
            $obj->addCss($icon);
        return $obj;
    }


    /**
     * @return \Dom\Template
     */
    public function show()
    {
        $template = parent::show();
        if (!$this->isVisible()) {
            return $template;
        }

        $template->addCss('icon', $this->getCssList());
        $template->setAttr('icon', $this->getAttrList());

        return $template;
    }

    /**
     * @return \Dom\Template
     */
    public function __makeTemplate()
    {
        $html = '<i var="icon"></i>';
        return \Dom\Loader::load($html);
    }
}