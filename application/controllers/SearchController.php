<?php

/**
 * Handles Search requests
 *
 * @author brady
 */
class SearchController extends Zend_Controller_Action
{
    const ITEMS_PER_PAGE = 30;

    public function indexAction()
    {
        $queryString = $this->_getParam('q');
        $pager = Sageweb_Cms_Table_Search::getSearchPager($queryString);

        $page = $this->_getParam('page', 1);
        $pager->setItemCountPerPage(self::ITEMS_PER_PAGE);
        $pager->setCurrentPageNumber($page);

        $this->view->pager = $pager;
    }
}
