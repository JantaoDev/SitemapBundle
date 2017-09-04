<?php

/**
 * This file is part of the JantaoDevSitemapBundle package
 */

namespace JantaoDev\SitemapBundle\Tests\DoctrineIterator;

use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use JantaoDev\SitemapBundle\DoctrineIterator\DoctrineIterator;
use Doctrine\ORM\AbstractQuery;

class DoctrineIteratorTest extends TestCase
{
    
    protected $query;
    protected $iterator;

    protected $queryFirstResult = 0;
    protected $queryMaxResults = 5;
    protected $queryResultsCount = 197;
    
    protected function getResultRow($row)
    {
        return [
            'id' => $row,
            'name' => "row_$row",
        ];
    }
    
    public function querySetFirstResult($value)
    {
        $this->queryFirstResult = $value;
        return $this->query;
    }
    
    public function querySetMaxResults($value)
    {
        $this->assertEquals($this->queryMaxResults, $value);
        return $this->query;
    }
    
    public function queryGetResult()
    {
        $result = [];
        for ($i = 0; $i < $this->queryMaxResults; $i++) {
            if ($this->queryFirstResult + $i >= $this->queryResultsCount) {
                break;
            }
            $result[] = $this->getResultRow($this->queryFirstResult + $i);
        }
        return $result;
    }
    
    protected function setUp()
    {
        $this->query = $this->getMockBuilder(AbstractQuery::class)->disableOriginalConstructor()->setMethods(['getResult', 'setFirstResult', 'setMaxResults'])->getMockForAbstractClass();
        $this->query->method('getResult')->will($this->returnCallback([$this, 'queryGetResult']));
        $this->query->method('setFirstResult')->will($this->returnCallback([$this, 'querySetFirstResult']));
        $this->query->method('setMaxResults')->will($this->returnCallback([$this, 'querySetMaxResults']));
        $this->iterator = new DoctrineIterator($this->query, $this->queryMaxResults);
    }
    
    public function testCurrent()
    {
        $this->iterator->rewind();
        for ($i = 0; $i < $this->queryResultsCount; $i++) {
            $this->assertEquals($this->getResultRow($i), $this->iterator->current());
            $this->iterator->next();
        }
        $this->assertNull($this->iterator->current());
        $this->iterator->next();
        $this->assertNull($this->iterator->current());
        $this->iterator->rewind();
        $this->assertEquals($this->getResultRow(0), $this->iterator->current());
        $this->iterator->next();
        $this->assertEquals($this->getResultRow(1), $this->iterator->current());
    }

    public function testNext()
    {
        $this->iterator->rewind();
        for ($i = 1; $i < $this->queryResultsCount; $i++) {
            $this->assertEquals($this->getResultRow($i), $this->iterator->next());
        }
        $this->assertNull($this->iterator->next());
        $this->assertNull($this->iterator->next());
        $this->iterator->rewind();
        $this->assertEquals($this->getResultRow(1), $this->iterator->next());
    }
    
    public function testKey()
    {
        $this->iterator->rewind();
        for ($i = 0; $i < $this->queryResultsCount; $i++) {
            $this->assertEquals($i, $this->iterator->key());
            $this->iterator->next();
        }
    }
    
    public function testValid()
    {
        $this->iterator->rewind();
        for ($i = 0; $i < $this->queryResultsCount; $i++) {
            $this->assertTrue($this->iterator->valid());
            $this->iterator->next();
        }
        $this->assertFalse($this->iterator->valid());
    }
    
}
