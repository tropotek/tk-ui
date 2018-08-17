<?php
namespace Tk\Ui;


/**
 * <code>
 *   \Tk\Ui\Button::create('Edit', \Tk\Uri::create('/dunno.html'), 'fa fa-edit)->addCss('btn-xs btn-success')->show();
 * </code>
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @see http://www.tropotek.com/
 * @license Copyright 2017 Michael Mifsud
 */
class Button extends Link
{

    /**
     * @param string $text
     * @param null|string|\Tk\Uri $url
     * @param string|Icon $icon
     * @return Button|Link
     */
    public static function create($text, $url = null, $icon = '')
    {
        $obj = parent::create($text, $url, $icon);
        $obj->addCss('btn btn-default');
        return $obj;
    }

}
