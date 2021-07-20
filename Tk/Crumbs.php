<?php

namespace Tk;


/**
 * Use this object to track and render a crumb stack
 *
 * See the controlling object \Tk\Listeners\CrumbsHandler to
 * view its implementation.
 *
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @see http://www.tropotek.com/
 * @license Copyright 2016 Michael Mifsud
 */
class Crumbs extends \Dom\Renderer\Renderer implements \Dom\Renderer\DisplayInterface
{
    use ConfigTrait;

    /**
     * Request param: Reset the crumb stack
     */
    const CRUMB_RESET = 'crumb_reset';
    /**
     * Request param: Do not add the current URI to the crumb stack
     */
    const CRUMB_IGNORE = 'crumb_ignore';

    /**
     * @var Crumbs
     */
    public static $instance = null;

    /**
     * @var array
     */
    protected $list = array();

    /**
     * @var boolean
     */
    protected $visible = true;

    /**
     * @var string
     */
    protected $homeTitle = 'Dashboard';

    /**
     * @var string
     */
    protected $homeUrl = '/index.html';


    /**
     * @param string $homeUrl
     * @param string $homeTitle
     */
    protected function __construct($homeUrl = '/index.html', $homeTitle = 'Dashboard')
    {
        $this->homeUrl = $homeUrl;
        $this->homeTitle = $homeTitle;
    }

    /**
     * @param string $homeUrl
     * @param string $homeTitle
     * @return static
     */
    static protected function create($homeUrl = '/index.html', $homeTitle = 'Dashboard')
    {
        $obj = new static($homeUrl, $homeTitle);
        return $obj;
    }

    /**
     * @param string $homeUrl
     * @param string $homeTitle
     * @return Crumbs
     */
    public static function getInstance($homeUrl = '/index.html', $homeTitle = 'Dashboard')
    {
        if (!self::$instance) {
            $crumbs = self::create($homeUrl, $homeTitle);
            if ($crumbs->getSession()->has($crumbs->getSid())) {
                $crumbs->setList($crumbs->getSession()->get($crumbs->getSid()));
            }
            if (!count($crumbs->getList())) {
                $crumbs->addCrumb($crumbs->getHomeTitle(), $crumbs->getHomeUrl());
            }
            self::$instance = $crumbs;
        }
        return self::$instance;
    }

    /**
     * @return $this
     */
    public function reset()
    {
        if (!$this->getRequest()->has(self::CRUMB_IGNORE)) {
            $this->getSession()->remove($this->getSid());
            $this->setList();
            $this->addCrumb($this->getHomeTitle(), $this->getHomeUrl());
            $this->save();
        }
        return $this;
    }

    /**
     * save the state of the crumb stack to the session
     */
    public function save()
    {
        $this->getSession()->set($this->getSid(), $this->getList());
    }

    /**
     * Get the crumbs session ID
     *
     * @return string
     */
    public function getSid()
    {
        return 'crumbs.' . $this->getHomeUrl();
    }

    /**
     * @return bool
     */
    public function isVisible()
    {
        return $this->visible;
    }

    /**
     * @param bool $visible
     * @return Crumbs
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;
        return $this;
    }

    /**
     * Get teh crumb list
     *
     * @return array
     */
    public function getList()
    {
        return $this->list;
    }

    /**
     * @return string
     */
    public function getHomeTitle(): string
    {
        return $this->homeTitle;
    }

    /**
     * @return string
     */
    public function getHomeUrl(): string
    {
        return $this->homeUrl;
    }

    /**
     * Use to restore crumb list.
     * format:
     *   array(
     *     'Page Name' => '/page/url/pageUrl.html'
     *   );
     *
     * @param array $list
     * @return Crumbs
     */
    public function setList($list = array())
    {
        $this->list = $list;
        return $this;
    }

    /**
     * @return \Tk\Uri
     */
    public function getBackUrl()
    {
        $url = '';
        if (count($this->list) == 1) {
            $url = end($this->list);
        }
        if (count($this->list) > 1) {
            end($this->list);
            $url = prev($this->list);
        }
        return \Tk\Uri::create($url);
    }

    /**
     * @param string $title
     * @param \Tk\Uri|string $url
     * @return $this
     */
    public function addCrumb($title, $url)
    {
        $url = \Tk\Uri::create($url);
        $this->list[$title] = $url->toString();
        return $this;
    }

    /**
     * @param string $title
     * @param \Tk\Uri|string $url
     * @return $this
     */
    public function replaceCrumb($title, $url)
    {
        array_pop($this->list);
        return $this->addCrumb($title, $url);
    }

    /**
     * @param $title
     * @return array
     */
    public function trimByTitle($title)
    {
        $l = array();
        foreach ($this->list as $t => $u) {
            if ($title == $t) break;
            $l[$t] = $u;
        }
        $this->list = $l;
        return $l;
    }

    /**
     * @param $url
     * @param bool $ignoreQuery
     * @return array
     */
    public function trimByUrl($url, $ignoreQuery = true)
    {
        $url = \Tk\Uri::create($url);
        $l = array();
        foreach ($this->list as $t => $u) {
            if ($ignoreQuery) {
                if (\Tk\Uri::create($u)->getRelativePath() == $url->getRelativePath()) {
                    break;
                }
            } else {
                if (\Tk\Uri::create($u)->toString() == $url->toString()) {
                    break;
                }
            }
            $l[$t] = $u;
        }
        $this->list = $l;
        return $l;
    }

    /**
     * @return \Dom\Template
     */
    public function show()
    {
        $template = $this->getTemplate();

        $i = 0;
        foreach ($this->list as $title => $url) {
            $repeat = $template->getRepeat('li');
            if (!$repeat) continue;         // ?? why and how does the repeat end up null.
            if ($i < count($this->list) - 1) {
                $repeat->setAttr('url', 'href', \Tk\Uri::create($url)->toString());
                $repeat->insertText('url', $title);
            } else {    // Last item
                $repeat->insertText('li', $title);
                $repeat->addCss('li', 'active');
            }

            $repeat->appendRepeat();
            $i++;
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
        $html = <<<HTML
<ol class="breadcrumb" var="breadcrumb">
  <li class="breadcrumb-item" repeat="li" var="li"><a href="#" var="url"></a></li>
</ol>
HTML;

        return \Dom\Loader::load($html);
    }


}
