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
     * @param null|\Tk\Uri|string $url
     * @param string $icon
     */
    public function __construct($text, $url = null, $icon = '')
    {
        parent::__construct($text, $url, $icon);
        $this->addCss('btn btn-default');
    }

}