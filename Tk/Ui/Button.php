<?php
namespace Tk\Ui;


/**
 * <code>
 *   \Tk\Ui\Button::create('Edit', \Tk\Uri::create('/dunno.html'), 'fa fa-edit)->addCss('btn-xs btn-success')->show();
 * </code>
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2017 Michael Mifsud
 */
class Button extends Link
{

    /**
     * @param string $text
     * @param null|string|\Tk\Uri $url
     * @param string $icon
     * @return static
     */
    public static function create($text, $url = null, $icon = '')
    {
        $obj = new static();
        $obj->text = $text;
        $obj->url = $url;
        $obj->icon = $icon;
        $obj->addCss('btn btn-default');
        return $obj;
    }

}