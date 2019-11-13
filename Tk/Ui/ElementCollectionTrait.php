<?php
namespace Tk\Ui;

/**
 * @author Michael Mifsud <info@tropotek.com>
 * @see http://www.tropotek.com/
 * @license Copyright 2016 Michael Mifsud
 */
trait ElementCollectionTrait
{

    /**
     * @var array|Element[]
     */
    protected $elementList = array();


    /**
     * @return array
     */
    public function getElementList()
    {
        return $this->elementList;
    }

    /**
     * @param array $list
     * @return $this
     */
    public function setElementList($list)
    {
        $this->elementList = $list;
        return $this;
    }

    /**
     * @param Element $element
     * @param null|Element $refElement
     * @return mixed
     */
    public function append($element, $refElement = null)
    {
        if (is_string($refElement)) {
            $refElement = $this->find($refElement);
        }
        if (!$refElement) {
            $this->elementList[] = $element;
        } else {
            $newArr = array();
            foreach ($this->elementList as $b) {
                $newArr[] = $b;
                if ($b === $refElement) $newArr[] = $element;
            }
            $this->elementList = $newArr;
        }
        return $element;
    }

    /**
     * @param Element $element
     * @param null|Element $refElement
     * @return mixed
     */
    public function prepend($element, $refElement = null)
    {
        if (is_string($refElement)) {
            $refElement = $this->find($refElement);
        }
        if (!$refElement) {
            $this->elementList = array_merge(array($element), $this->elementList);
        } else {
            $newArr = array();
            foreach ($this->elementList as $b) {
                if ($b === $refElement) $newArr[] = $element;
                $newArr[] = $b;
            }
            $this->elementList = $newArr;
        }
        return $element;
    }

    /**
     * @param string|Element $element
     * @return null|Element Return null if no element removed
     */
    public function remove($element)
    {
        if (is_string($element)) {
            $element = $this->find($element);
        }
        $newArr = array();
        foreach ($this->elementList as $b) {
            if ($b !== $element) $newArr[] = $b;
        }
        $this->elementList = $newArr;
        return $element;
    }

    /**
     * @param string $title
     * @return null|Element
     */
    public function find($title)
    {
        /** @var Element $element */
        foreach ($this->elementList as $element) {
            if ($element->hasAttr('title') && $element->getAttr('title') == $title)
                return $element;
        }
        return null;
    }

    /**
     * Remove all buttons and start again
     * @return $this
     * @deprecated Use clear()
     */
    public function reset()
    {
        $this->clear();
        return $this;
    }

    /**
     * Remove all elements from the array
     * @return $this
     */
    public function clear()
    {
        $this->elementList = array();
        return $this;
    }

}