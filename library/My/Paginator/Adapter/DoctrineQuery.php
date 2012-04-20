<?php

/**
 * Paginator for a Doctrine_Query object.
 */
class My_Paginator_Adapter_DoctrineQuery implements Zend_Paginator_Adapter_Interface
{
    /**
     * @var Doctrine_Query
     */
    private $query = null;

    /**
     * @var integer
     */
    private $count = null;

    /**
     * Sets up the paginator with a doctrine query object
     *
     * @param Doctrine_Query $query
     * @return integer
     */
    public function __construct(Doctrine_Query $query)
    {
        $this->query = $query;
    }

    /**
     * Returns the total number of rows in the collection.
     *
     * @return integer
     */
    public function count()
    {
        if ($this->count === null) {
            $this->count = $this->query->count();
        }
        return $this->count;
    }

    /**
     * Returns an collection of items for a page.
     *
     * @param  integer $offset Page offset
     * @param  integer $itemCountPerPage Number of items per page
     * @return array
     */
    public function getItems($offset, $itemCountPerPage)
    {
        $this->query->limit($itemCountPerPage);
        $this->query->offset($offset);
        return $this->query->execute();
    }
}
