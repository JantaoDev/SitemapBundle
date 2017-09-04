<?php

/**
 * This file is part of the JantaoDevSitemapBundle package
 */

namespace JantaoDev\SitemapBundle\DoctrineIterator;

use Doctrine\ORM\AbstractQuery;

/**
 * Doctrine query iterator
 * 
 * @author Sergey Hayevoy <jantao.dev@gmail.com>
 */
class DoctrineIterator implements \Iterator
{
    
    protected $query;
    protected $currentKey;
    protected $pageSize;
    protected $pageStart;
    protected $pageData;

    /**
     * Constructor
     * 
     * @param AbstractQuery $query Doctrine query
     * @param int $pageSize Cache page size
     */
    public function __construct(AbstractQuery $query, $pageSize = 1000)
    {
        $this->query = $query;
        $this->pageSize = $pageSize;
        $this->currentKey = 0;
        $this->loadPage(0);
    }

    /**
     * Load cache page
     * 
     * @param int $page Page number
     */
    protected function loadPage($page)
    {
        $this->pageStart = $page * $this->pageSize;
        $this->pageData = $this->query->setFirstResult($this->pageStart)->setMaxResults($this->pageSize)->getResult();
    }
    
    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        $this->currentKey = 0;
        $this->loadPage(0);
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        return isset($this->pageData[$this->currentKey - $this->pageStart]) ? $this->pageData[$this->currentKey - $this->pageStart] : null;
    }
    
    /**
     * {@inheritdoc}
     */
    public function next()
    {
        $this->currentKey++;
        if ($this->currentKey >= $this->pageStart + $this->pageSize) {
            $this->loadPage(intval($this->currentKey / $this->pageSize));
        }
        return $this->current();
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        return $this->currentKey;
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        return isset($this->pageData[$this->currentKey - $this->pageStart]);
    }
}
