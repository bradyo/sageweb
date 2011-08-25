<?php

class ForumController extends Zend_Controller_Action
{
    const ITEMS_PER_PAGE = 20;

    public function indexAction()
    {
        $forums = Sageweb_Table_Forum::findAll();
        $this->view->forums = $forums;
    }

    public function topicsAction()
    {
        $slug = $this->_getParam('slug');
        $forum = Sageweb_Table_Forum::findOneBySlug($slug);
        if (!$forum) {
            throw new Zend_Controller_Exception('Page not found', 404);
        }

        $pager = Sageweb_Table_Forum::getTopicsPager($slug);
        $pager->setItemCountPerPage(self::ITEMS_PER_PAGE);
        $pager->setCurrentPageNumber($this->_getParam('page', 1));

        $this->view->forum = $forum;
        $this->view->pager = $pager;
    }
}


