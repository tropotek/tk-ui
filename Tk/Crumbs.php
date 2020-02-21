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
     * @var string
     */
    public static $homeUrl = '/index.html';

    /**
     * @var string
     */
    public static $homeTitle = 'Home';


    /**
     * @var Crumbs
     */
    public static $instance = null;

    /**
     * @var array
     */
    protected $list = array();

    /**
     * @var \Tk\Session
     */
    protected $session = null;

    /**
     * @var boolean
     */
    protected $visible = true;


    /**
     * @param \Tk\Session $session
     */
    protected function __construct($session)
    {
        $this->session = $session;
    }

    /**
     * @param \Tk\Session $session
     * @return static
     */
    static protected function create($session)
    {
        $obj = new static($session);
        return $obj;
    }

    /**
     * @param null|\Tk\Session $session
     * @return Crumbs
     */
    public static function getInstance($session = null)
    {
        if (!$session)
            $session = \Tk\Config::getInstance()->getSession();

        if (!self::$instance) {
            $crumbs = self::create($session);
            if ($session->has($crumbs->getSid())) {
                $crumbs->setList($session->get($crumbs->getSid()));
            }
            if (!count($crumbs->getList())) {
                $crumbs->addCrumb(self::$homeTitle, \Tk\Uri::create(self::$homeUrl));
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
            $homeTitle = self::$homeTitle;
            $url = \Tk\Uri::create(self::$homeUrl);
            $this->getSession()->remove($this->getSid());
            $this->setList();
            $this->addCrumb($homeTitle, $url);
            $this->save();
        }
        return $this;
    }

//    public static function reset($homeTitle = 'Dashboard', $url = null)
//    {
//        $crumbs = self::getInstance();
//        if ($crumbs && !$this->getRequest()->has(self::CRUMB_IGNORE)) {
//            if (!$url) {
//                $homeTitle = self::$homeTitle;
//                $url = \Tk\Uri::create(self::$homeUrl);
//            }
//            $crumbs->getSession()->remove($crumbs->getSid());
//            $crumbs->setList();
//            $crumbs->addCrumb($homeTitle, $url);
//            $crumbs->save();
//            return $crumbs;
//        }
//    }


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
        return 'crumbs.' . self::$homeUrl;
    }

    /**
     * @return \Tk\Session
     */
    public function getSession()
    {
        return $this->session;
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
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;
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
     * Use to restore crumb list.
     * format:
     *   array(
     *     'Page Name' => '/page/url/pageUrl.html'
     *   );
     *
     * @param $list
     */
    public function setList($list = array())
    {
        $this->list = $list;
    }

    /**
     * @return \Tk\Uri
     */
    public function getBackUrl()
    {
        $url = '';
        if (count($this->list) == 1) {
            $url = end($this->list);
        } if (count($this->list) > 1) {
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
    public function replaceCrumb($title, $url) {
        array_pop($this->list);
        return $this->addCrumb($title, $url);
    }

    /**
     * @param $title
     * @return array
     */
    public function trimByTitle($title) {
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
    public function trimByUrl($url, $ignoreQuery = true) {
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
            if ($i < count($this->list)-1) {
                $repeat->setAttr('url', 'href', \Tk\Uri::create($url)->toString());
                $repeat->insertText('url', $title);
            } else {    // Last item
                $repeat->insertText('li', $title);
                $repeat->addCss('li','active');
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
