<?php

class PostController extends Zend_Controller_Action
{
    const ITEMS_PER_PAGE = 5;

    public function indexAction()
    {
        $this->_forward('list');
    }

    public function listAction()
    {
        if ($this->_getParam('format') == 'rss') {
            $this->_forward('rss');
        }

        $category = $this->_getParam('category', 'all');
        $order = $this->_getParam('order', Sageweb_Model_Orm_PostTable::ORDER_NEWEST);
        $this->view->category = $category;
        $this->view->orderBy = $orderBy;

        $q = Sageweb_Model_Orm_PostTable::getContentQuery($category, $order);
        $pageNumber = $this->_getParam('page', 1);
        $pager = new Doctrine_Pager($q, $pageNumber, self::ITEMS_PER_PAGE);
        $this->view->posts = $pager->execute(array(), DOCTRINE::HYDRATE_RECORD);
        $this->view->pager = $pager;
    }

    public function rssAction()
    {
        $this->getHelper('layout')->disableLayout();

        $category = $this->_getParam('category', 'all');
        $this->view->category = $category;
        $this->view->posts = Sageweb_Model_Orm_PostTable::findNewest($category);
    }

    public function showAction()
    {
        $postId = $this->_getParam('id');

        $post = Sageweb_Model_Orm_PostTable::getPost($postId);
        if (!$post) {
            throw new Zend_Controller_Action_Exception(404, 'Post not found.');
        }
        $this->view->post = $post;

        // increment views count
        $user = Zend_Auth::getInstance()->getIdentity();
        $post->addView($user);

        // render the appropriate view for the post ype
        $this->_helper->viewRenderer($post->type);
        $this->view->render($post->type);
    }

    public function editAction()
    {
        $postId = $this->_getParam('id');

    }

    public function addCommentAction()
    {
        $postId = $this->_getParam('id');

        $user = Zend_Auth::getInstance()->getIdentity();
        if (!$user) {
            return;
        }

        $comment = new Sageweb_Model_Orm_Comment();
        $comment->fromArray(array(
            'parentEntityId' => $this->_getParam('parentEntityId'),
            'parentCommentId' => $this->_getParam('parentCommentId'),
            'depth' => $depth,
            'body' => $this->_getParam('body')
        ));
    }
}
