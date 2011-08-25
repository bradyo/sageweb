<?php

class Application_Pager {
 
    private $itemsCount;
    private $itemsPerPage;
    private $pageRangeDelta;
    private $currentPage;
    
    public function __construct($itemsCount, $itemsPerPage = 20, $pageRangeDelta = 5) {
        $this->itemsCount = $itemsCount;
        $this->itemsPerPage = $itemsPerPage;
        $this->pageRangeDelta = $pageRangeDelta;
    }
    
    public function getItemsCount() {
        return $this->itemsCount;
    }
    public function setItemsCount($itemsCount) {
        $this->itemsCount = $itemsCount;
    }

    public function getItemsPerPage() {
        return $this->itemsPerPage;
    }
    public function setItemsPerPage($itemsPerPage) {
        $this->itemsPerPage = $itemsPerPage;
    }
        
    public function setPageRangeDelta($delta) {
        $this->pageRangeDelta = $delta;
    }
    public function getPageRangeDelta() {
        return $this->pageRangeDelta;
    }
    
    public function setCurrentPage($currentPage) {
        $this->currentPage = $currentPage;
    }
    public function getCurrentPage() {
        return $this->currentPage;
    }
    
    
    public function getPageCount() {
        return intval($this->itemsCount / $this->itemsPerPage) + 1;
    }
    
    public function getRangeStartPage() {
        $rangeStartPage = $this->currentPage - $this->pageRangeDelta;
        if ($rangeStartPage < 1) {
            $rangeStartPage = 1;
        }
        return $rangeStartPage;
    }
    
    public function getRangeEndPage() {
        $rangeEndPage = $this->currentPage + $this->pageRangeDelta;
        if ($rangeEndPage > $this->getPageCount()) {
            $rangeEndPage = $this->getPageCount();
        }
        if ($rangeEndPage < 2 * $this->pageRangeDelta + 1) {
            $rangeEndPage = 2 * $this->pageRangeDelta + 1;
        }
        if ($rangeEndPage > $this->getPageCount()) {
            $rangeEndPage = $this->getPageCount();
        }
        return $rangeEndPage;
    }
    
    public function hasMultiplePages() {
        return ($this->itemsCount > $this->itemsPerPage);
    }
}
