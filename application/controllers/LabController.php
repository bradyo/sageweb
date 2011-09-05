<?php

class LabController extends Zend_Controller_Action
{
    const ENTITY_TYPE = Sageweb_Entity::TYPE_LAB;
    const ITEMS_PER_PAGE = 25;

    public function indexAction()
    {
        $queryString = $this->_getParam('search');
        $letter = $this->_getParam('letter');
        $letterCounts = Sageweb_Table_Lab::getLetterCounts();

        $pager = Sageweb_Table_Lab::getSearchPager($queryString, $letter);
        $page = $this->_getParam('page', 1);
        $pager->setItemCountPerPage(self::ITEMS_PER_PAGE);
        $pager->setCurrentPageNumber($page);

        $this->view->title = 'Labs in Gerontology';
        $this->view->letterCounts = $letterCounts;
        $this->view->sortNavigation = $this->_getSortNavigation($letterCounts);
        $this->view->pager = $pager;
    }

    private function _getSortNavigation($letterCounts)
    {
        $navigation = new Zend_Navigation();
        foreach ($letterCounts as $letter => $count) {
            $page = new Application_Navigation_Page_MvcCount(array(
                'label' => strtoupper($letter),
                'count' => $count,
                'route' => 'labs',
                'module' => 'default',
                'controller' => 'lab',
                'action' => 'index',
                'params' => array('letter' => $letter, 'search' => $this->_getParam('search')),
            ));
            $navigation->addPage($page);
        }
        return $navigation;
    }

    public function showAction()
    {
        $id = $this->_getParam('id');
        $post = Sageweb_Table_Lab::findOneById($id);
        if (!$post) {
            throw new Zend_Controller_Action_Exception('Page not found', 404);
        }

        $viewingUser = Application_Registry::getCurrentUser();
        $pendingRevisions = array();
        $canEdit = $viewingUser->canEdit($post);
        if ($canEdit) {
            $pendingRevisions = Sageweb_Table_Revision::findPendingById($post->entityId);
        }

        $this->view->pendingRevisions = $pendingRevisions;
        $this->view->canEdit = $canEdit;
        $this->view->title = $post->name;
        $this->view->post = $post;
    }

    public function newAction()
    {
        $viewingUser = Application_Registry::getCurrentUser();
        $form = new Application_Form_PostLab(array('viewingUser' => $viewingUser));
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

                Application_Registry::getFlashMessenger()->addMessage(array(
                    'type' => 'thanks',
                    'value' => $this->view->render('post/_thanksMessage.phtml')
                ));
                $url = $this->view->url(array('id' => $post->id), 'lab', true);
                $this->_redirect($url);
            }
        } else {
            if ($viewingUser->isModerator()) {
                $form->getElement('author')->setValue($viewingUser->username);
            }
        }

        $this->view->title = 'Add Lab';
        $this->view->form = $form;
    }

    public function editAction()
    {
        $id = $this->_getParam('id');
        $post = Sageweb_Table_Lab::findOneById($id);
        if (!$post) {
            throw new Zend_Controller_Action_Exception('Page not found', 404);
        }

        $viewingUser = Application_Registry::getCurrentUser();
        if (!$viewingUser->canEdit($post)) {
            throw new Zend_Controller_Action_Exception('Permission denied.', 404);
        }
        
        $form = new Application_Form_PostLab(array('viewingUser' =>  $viewingUser));
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

                $url = $this->view->url(array('id' => $post->id), 'lab', true);
                $this->_redirect($url);
            }
        } else {
            $form->populate($post->toArray());
        }

        $this->view->title = 'Edit Lab';
        $this->view->post = $post;
        $this->view->form = $form;
    }

    private function _getRevisionData($formValues, $viewingUser)
    {
        $revisionData['authorId'] = $viewingUser->id;
        $revisionData['type'] = $formValues['type'];
        $revisionData['name'] = $formValues['name'];
        $revisionData['location'] = $formValues['location'];
        $revisionData['url'] = $formValues['url'];
        $revisionData['body'] = $formValues['body'];
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
        $post = Sageweb_Table_Lab::findOneById($id);
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
        $post = Sageweb_Table_Lab::findOneById($id);

        $revisionId = $this->_getParam('revisionId');
        $revision = Sageweb_Table_Revision::findOneByEntityId($post->entityId, $revisionId);

        if (!$post || !$revision) {
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
                    array('action' => 'revisions', 'id' => $post->id), 'lab', true);
                $this->_redirect($url);
            }
        }

        $this->view->post = $post;
        $this->view->currentData = $post->getRevisionData();

        $this->view->revision = $revision;
        $this->view->revisionData = Zend_Json::decode($revision->jsonData);
    }
}
