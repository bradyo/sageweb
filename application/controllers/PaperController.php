<?php

/**
 * @author brady
 */
class PaperController extends Zend_Controller_Action
{
    const ENTITY_TYPE = Sageweb_Entity::TYPE_PAPER;
    const ITEMS_PER_PAGE = 10;

    private function _getSortNavigation()
    {
        $navigation = new Zend_Navigation();

        $page = new Zend_Navigation_Page_Mvc(array(
            'label' => 'Newest',
            'route' => 'papers',
            'module' => 'default',
            'controller' => 'paper',
            'action' => 'index',
            'params' => array('sort' => 'newest'),
        ));
        if (!$this->_hasParam('sort')) {
            $page->setActive(true);
        }
        $navigation->addPage($page);

        $page = new Zend_Navigation_Page_Mvc(array(
            'label' => 'Top-Rated',
            'route' => 'papers',
            'module' => 'default',
            'controller' => 'paper',
            'action' => 'index',
            'params' => array('sort' => 'top-rated')
        ));
        $navigation->addPage($page);

        return $navigation;
    }

private function _getTypeNavigation()
    {
        $navigation = new Zend_Navigation();

        $page = new Zend_Navigation_Page_Mvc(array(
            'label' => 'Current',
            'route' => 'papers',
            'module' => 'default',
            'controller' => 'paper',
            'action' => 'index',
            'params' => array('type' => 'current'),
        ));
        if (!$this->_hasParam('sort')) {
            $page->setActive(true);
        }
        $navigation->addPage($page);

        $page = new Zend_Navigation_Page_Mvc(array(
            'label' => 'Classical',
            'route' => 'papers',
            'module' => 'default',
            'controller' => 'paper',
            'action' => 'index',
            'params' => array('type' => 'classical')
        ));
        $navigation->addPage($page);

        return $navigation;
    }

    public function indexAction()
    {
        $queryString = $this->_getParam('search');
        $sort = $this->_getParam('sort');
        $pager = Sageweb_Table_Paper::getSearchPager($queryString, $sort);

        $page = $this->_getParam('page', 1);
        $pager->setItemCountPerPage(self::ITEMS_PER_PAGE);
        $pager->setCurrentPageNumber($page);

        $this->view->typeNavigation = $this->_getTypeNavigation();
        $this->view->sortNavigation = $this->_getSortNavigation();
        $this->view->pager = $pager;
    }

    public function rssAction()
    {
        $this->getHelper('layout')->disableLayout();
        $posts = Sageweb_Table_Paper::findNewest();
        $this->view->posts = $posts;
    }

    public function showAction()
    {
        $id = $this->_getParam('id');
        $post = Sageweb_Table_Paper::findOneById($id);
        if (!$post) {
            throw new Zend_Controller_Action_Exception('Page not found', 404);
        }

        // increment view counter
        $viewingUser = Application_Registry::getCurrentUser();
        if ($post->isPublic()) {
            $post->incrementViewCounter($viewingUser);

            // fetch vote record
            $existingVote = $viewingUser->getVote($post->entity);
            if ($existingVote) {
                $this->view->voteValue = $existingVote->value;
            }
        }

        $pendingRevisions = array();
        $canEdit = $viewingUser->canEdit($post);
        if ($canEdit) {
            $pendingRevisions = Sageweb_Table_Revision::findPendingById($post->entityId);
        }

        $this->view->pendingRevisions = $pendingRevisions;
        $this->view->canEdit = $viewingUser->canEdit($post);
        $this->view->post = $post;
    }

    public function newAction()
    {
        $viewingUser = Application_Registry::getCurrentUser();
        $form = new Application_Form_PostPaper(array('viewingUser' => $viewingUser));
        if ($this->getRequest()->isPost()) {
            if($form->isValid($this->_getAllParams())) {
                $formValues = $form->getValues();

                // create a new article entry
                $data = $this->_getRevisionData($formValues, $viewingUser);
                $post = Sageweb_Table_Entity::createPost(self::ENTITY_TYPE, $data);

                // create revision entry (pendign => public)
                $data['status'] = Sageweb_Abstract_Post::STATUS_PUBLIC;
                $revision = $viewingUser->createRevision($post->entity, $data);
                if ($viewingUser->isModerator()) {
                    $reviewerComment = $formValues['reviewerComment'];
                    $viewingUser->acceptRevision($revision, $reviewerComment);
                }

                $url = $this->view->url(array('id' => $post->id), 'paper', true);
                $this->_redirect($url);
            }
        } else {
            if ($viewingUser->isModerator()) {
                $form->getElement('author')->setValue($viewingUser->username);
            }
        }
        $this->view->form = $form;
    }

    public function editAction()
    {
        $id = $this->getRequest()->getParam('id');
        $post = Sageweb_Table_Paper::findOneById($id);
        if (!$post) {
            throw new Zend_Controller_Action_Exception('Page not found', 404);
        }

        $viewingUser = Application_Registry::getCurrentUser();
        if (!$viewingUser->canEdit($post)) {
            throw new Zend_Controller_Action_Exception('Permission denied.', 404);
        }
        
        $form = new Application_Form_PostPaper(array('viewingUser' =>  $viewingUser));
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->_getAllParams())) {
                $formValues = $form->getValues();

                // save revision entry
                $data = $this->_getRevisionData($formValues, $viewingUser);
                $data['updatedAt'] = date('Y-m-d H:i:s');
                $creatorComment = $formValues['creatorComment'];
                $revision = $viewingUser->createRevision($post->entity, $data, $creatorComment);
                if ($viewingUser->isModerator()) {
                    $reviewerComment = $formValues['reviewerComment'];
                    $viewingUser->acceptRevision($revision, $reviewerComment);
                }

                $url = $this->view->url(array('id' => $post->id), 'paper', true);
                $this->_redirect($url);
            }
        } else {
            $form->populate($post->toArray());
        }
        
        $this->view->post = $post;
        $this->view->form = $form;
    }

    private function _getRevisionData($formValues, $viewingUser)
    {
        $revisionData['authorId'] = $viewingUser->id;
        $revisionData['pubmedId'] = $formValues['pubmedId'];
        $revisionData['title'] = $formValues['title'];
        $revisionData['type'] = $formValues['type'];
        $revisionData['authors'] = $formValues['authors'];
        $revisionData['source'] = $formValues['source'];
        $revisionData['publishDate'] = $formValues['publishDate'];
        $revisionData['url'] = $formValues['url'];
        $revisionData['abstract'] = $formValues['abstract'];
        $revisionData['summary'] = $formValues['summary'];
        if ($viewingUser->isModerator()) {
            $username = $formValues['author'];
            $author = Sageweb_Table_User::findOneByUsername($username);
            $revisionData['authorId'] = $author->id;
            $revisionData['status'] = $formValues['status'];
        }
        return $revisionData;
    }

    public function revisionsAction()
    {
        $id = $this->_getParam('id');
        $post = Sageweb_Table_Paper::findOneById($id);
        if (!$post) {
            throw new Zend_Controller_Action_Exception(404, 'Post not found.');
        }
        
        $revisions = Sageweb_Table_Revision::findByEntityId($post->entityId);

        $this->view->post = $post;
        $this->view->revisions = $revisions;
    }

    public function revisionAction()
    {
        $id = $this->_getParam('id');
        $post = Sageweb_Table_Paper::findOneById($id);
        if (!$post) {
            throw new Zend_Controller_Action_Exception(404, 'Post not found.');
        }

        $revisionId = $this->_getParam('revisionId');
        $revision = Sageweb_Table_Revision::findOneByEntityId($post->entityId, $revisionId);
        if (!$revision) {
            throw new Zend_Controller_Action_Exception(404, 'Revision not found.');
        }

        if ($this->_request->isPost()) {
            $viewingUser = Application_Registry::getCurrentUser();
            if ($viewingUser->isModerator()) {
                // accept or reject revision
                $reviewerComment = $this->_getParam('reviewerComment');
                $status = $this->_getParam('status');
                if ($status == Sageweb_EntityRevision::STATUS_ACCEPTED) {
                    $viewingUser->acceptRevision($revision, $reviewerComment);
                } else {
                    $viewingUser->rejectRevision($revision, $reviewerComment);
                }

                $url = $this->view->url(
                    array('action' => 'revisions', 'id' => $post->id), 'paper', true);
                $this->_redirect($url);
            }
        }

        $this->view->post = $post;
        $this->view->currentData = $post->getRevisionData();

        $this->view->revision = $revision;
        $this->view->revisionData = Zend_Json::decode($revision->jsonData);
    }
}
