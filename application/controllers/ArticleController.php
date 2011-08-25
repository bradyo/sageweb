<?php

/**
 * Description of ArticleController
 *
 * @author brady
 */
class ArticleController extends Zend_Controller_Action
{
    public function showAction()
    {
        $id = $this->_getParam('id');
        $post = Sageweb_Table_Article::findOneById($id);
        if (!$post) {
            throw new Zend_Controller_Action_Exception('Page not found', 404);
        }

        // increment view counter
        $viewingUser = Application_Registry::getUser();
        if ($post->isPublic()) {
            $post->incrementViews($viewingUser);

            // fetch vote record
            $existingVote = $viewingUser->getVote($post->entity);
            if ($existingVote) {
                $this->view->voteValue = $existingVote->value;
            }
        }

        $this->view->canEdit = $viewingUser->canEdit($post);
        $this->view->post = $post;
    }

    public function newAction()
    {
        $viewingUser = Application_Registry::getUser();
        $form = new Application_Form_PostArticle(array('viewingUser' => $viewingUser));
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getParams())) {
                $formValues = $form->getValues();
                $formValues['tags'] = Application_Converter_Tags::getArray($formValues['tags']);

                // create a new article entry
                $type = Sageweb_Entity::TYPE_ARTICLE;
                $data = $this->_getRevisionData($formValues, $viewingUser);
                $post = Sageweb_Table_Entity::createPost($type, $data);

                // create revision entry
                $data['status'] = Sageweb_Abstract_Post::STATUS_PUBLIC;
                $revision = $viewingUser->createRevision($post->entity, $data);
                if ($viewingUser->isModerator()) {
                    $comment = $formValues['reviewerComment'];
                    $viewingUser->acceptRevision($revision, $comment);
                }

                $url = $this->view->url(array('id' => $post->getSlugId()), 'article', true);
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
        $post = Sageweb_Table_Article::findOneById($id);
        if (!$post) {
            throw new Zend_Controller_Action_Exception('Page not found', 404);
        }

        $viewingUser = Application_Registry::getUser();
        if (!$viewingUser->canEdit($post)) {
            throw new Zend_Controller_Action_Exception('Permission denied.', 404);
        }

        $form = new Application_Form_PostArticle(array('viewingUser' => $viewingUser));
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->_getAllParams())) {
                $formValues = $form->getValues();
                $formValues['tags'] = Application_Converter_Tags::getArray($formValues['tags']);

                // save revision entry
                $data = $this->_getRevisionData($formValues, $viewingUser);
                $data['updatedAt'] = date('Y-m-d H:i:s');
                $comment = $formValues['creatorComment'];
                $revision = $viewingUser->createRevision($post->entity, $data, $comment);
                if ($viewingUser->isModerator()) {
                    $comment = $formValues['reviewerComment'];
                    $viewingUser->acceptRevision($revision, $comment);
                }

                $url = $this->view->url(array('id' => $post->getSlugId()), 'article', true);
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
        $revisionData['title'] = $formValues['title'];
        $revisionData['summary'] = $formValues['summary'];
        $revisionData['body'] = $formValues['body'];
        $revisionData['categories'] = $formValues['categories'];
        $revisionData['tags'] = $formValues['tags'];
        $revisionData['isFeatured'] = false;
        if ($viewingUser->isModerator()) {
            $revisionData['status'] = $formValues['status'];
            $revisionData['isFeatured'] = $formValues['isFeatured'];

            $username = $formValues['author'];
            $author = Sageweb_Table_User::findOneByUsername($username);
            $revisionData['authorId'] = $author->id;
        }
        return $revisionData;
    }

    public function revisionsAction()
    {
        if ($this->_hasParam('revisionId')) {
            $this->_forward('revision');
        }

        $id = $this->_getParam('id');
        $article = Sageweb_Table_Article::findOneById($id);
        if (!$article) {
            throw new Zend_Controller_Action_Exception(404, 'Post not found.');
        }
        $revisions = Sageweb_Table_Revision::findByEntityId($article->entityId);

        $this->view->article = $article;
        $this->view->revisions = $revisions;
    }

    public function revisionAction()
    {
        $id = $this->_getParam('id');
        $article = Sageweb_Table_Article::findOneById($id);
        if (!$article) {
            throw new Zend_Controller_Action_Exception(404, 'Article not found.');
        }

        $revisionId = $this->_getParam('revisionId');
        $revision = Sageweb_Table_Revision::findOneByEntityId($article->entityId, $revisionId);
        if (!$revision) {
            throw new Zend_Controller_Action_Exception(404, 'Revision not found.');
        }

        if ($this->_request->isPost()) {
            $viewingUser = Application_Registry::getUser();
            if ($viewingUser->isModerator()) {
                // accept or reject revision
                $comment = $this->_getParam('reviewerComment');
                $status = $this->_getParam('status');
                if ($status == Sageweb_EntityRevision::STATUS_ACCEPTED) {
                    $viewingUser->acceptRevision($revision, $comment);
                } else {
                    $viewingUser->rejectRevision($revision, $comment);
                }

                $url = $this->view->url(array('action' => 'revisions', 'id' => $article->id),
                        'article', true);
                $this->_redirect($url);
            }
        }

        $this->view->article = $article;
        $this->view->revision = $revision;
        $this->view->currentData = $article->getRevisionData();
        $this->view->revisionData = Zend_Json::decode($revision->jsonData);
    }
}
