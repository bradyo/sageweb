<?php

class ContentController extends Zend_Controller_Action
{
    const ITEMS_PER_PAGE = 5;

    public function indexAction()
    {
        $queryString = $this->_getParam('search');

        $category = $this->_getParam('category', 'all');
        $tag = $this->_getParam('tag');
        if ($tag) {
            $queryString .= ' category:"' . $category . '" AND tag:"' . $tag . '"';
        } else {
            $queryString .= ' category:"' . $category . '"';
        }

        $sort = $this->_getParam('sort');
        $pager = Sageweb_Table_Content::getSearchPager($queryString, $sort);

        $page = $this->_getParam('page', 1);
        $pager->setItemCountPerPage(self::ITEMS_PER_PAGE);
        $pager->setCurrentPageNumber($page);

        $this->view->sortLinks = $this->_getSortNavigation();
        $this->view->category = $category;
        $this->view->tag = $tag;
        $this->view->pager = $pager;
    }

    private function _getSortNavigation()
    {
        $navigation = new Zend_Navigation();

        $page = new Zend_Navigation_Page_Mvc(array(
            'label' => 'Newest',
            'route' => 'content',
            'module' => 'default',
            'controller' => 'content',
            'action' => 'index',
            'params' => array('sort' => 'newest'),
            'resetParams' => false
        ));
        if (!$this->_hasParam('sort')) {
            $page->setActive(true);
        }
        $navigation->addPage($page);

        $page = new Zend_Navigation_Page_Mvc(array(
            'label' => 'Top-Rated',
            'route' => 'content',
            'module' => 'default',
            'controller' => 'content',
            'action' => 'index',
            'params' => array('sort' => 'top-rated'),
            'resetParams' => false
        ));
        $navigation->addPage($page);

        return $navigation;
    }

    public function rssAction()
    {
        $this->getHelper('layout')->disableLayout();

        $category = $this->_getParam('category');
        $tag = $this->_getParam('tag');
        $posts = Sageweb_Table_Content::findNewest($category, $tag);

        $this->view->category = $category;
        $this->view->tag = $tag;
        $this->view->posts = $posts;
    }
}
