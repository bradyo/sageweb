<?php

/**
 * @author brady
 */
class DiscussionController extends Zend_Controller_Action
{
    const ITEMS_PER_PAGE = 25;

    public function indexAction()
    {
        $queryString = $this->_getParam('search');

        $forumId = NULL;
        $forumSlug = $this->_getParam('forum');
        $forum = Sageweb_Table_Forum::findOneBySlug($forumSlug);
        if ($forum) {
            $queryString .= ' forumId:' . $forum->id;
        }

        $tag = $this->_getParam('tag');
        if ($tag) {
            $queryString .= ' tags:"' . $tag . '"';
        }

        $sort = $this->_getParam('sort');
        $pager = Sageweb_Table_Discussion::getSearchPager($queryString, $sort);
        $pager->setItemCountPerPage(self::ITEMS_PER_PAGE);
        $pager->setCurrentPageNumber($this->_getParam('page', 1));

        // fetch tags in this forum
        $tags = Sageweb_Table_Discussion::getForumTags($forumId);

        $this->view->tags = $tags;
        $this->view->forum = $forum;
        $this->view->pager = $pager;
    }

    public function rssAction()
    {
        $this->getHelper('layout')->disableLayout();

        $forumId = NULL;
        $slug = $this->_getParam('forum');
        $forum = Sageweb_Table_Forum::findOneBySlug($slug);
        if ($forum) {
            $forumId = $forum->id;
        }

        $tag = $this->_getParam('tag');
        $posts = Sageweb_Table_Discussion::findNewest($forumId, $tag);

        $this->view->forum = $forum;
        $this->view->tag = $tag;
        $this->view->posts = $posts;
    }

    public function showAction()
    {
        $id = $this->_getParam('id');
        $post = Sageweb_Table_Discussion::findOneById($id);
        if (!$post) {
            throw new Zend_Controller_Action_Exception('Page not found', 404);
        }

        if (!empty($post->slug) && preg_match('/^\d+$/', $id)) {
            $pieces = explode("#");
            $fullUri = '/discussions/' . $post->getSlugId();
            if (count($pieces) > 1) {
                $fullUri .= '#' . $pieces[0];
            }
            $this->_redirect($fullUri, array('code' => 301));
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

        $tags = Sageweb_Table_Discussion::getTags($post->id);

        $this->view->tags = $tags;
        $this->view->canEdit = $viewingUser->canEdit($post);
        $this->view->post = $post;
    }

    public function newAction()
    {
        $viewingUser = Application_Registry::getCurrentUser();
        $form = new Application_Form_PostDiscussion(array('viewingUser' => $viewingUser));
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getParams())) {
                $formValues = $form->getValues();
                $formValues['tags'] = Application_Converter_Tags::getArray($formValues['tags']);

                // create a new article entry
                $type = Sageweb_Entity::TYPE_DISCUSSION;
                $data = $this->_getRevisionData($formValues, $viewingUser);
                $post = Sageweb_Table_Entity::createPost($type, $data);

                // create revision entry
                $data['status'] = Sageweb_Abstract_Post::STATUS_PUBLIC;
                $revision = $viewingUser->createRevision($post->entity, $data);
                if ($viewingUser->isModerator()) {
                    $reviewerComment = $formValues['reviewerComment'];
                    $viewingUser->acceptRevision($revision, $reviewerComment);
                }

                $url = $this->view->url(array('id' => $post->getSlugId()), 'discussion', true);
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
        $id = $this->_getParam('id');
        $post = Sageweb_Table_Discussion::findOneById($id);
        if (!$post) {
            throw new Zend_Controller_Action_Exception('Page not found', 404);
        }

        $viewingUser = Application_Registry::getCurrentUser();
        if (!$viewingUser->canEdit($post)) {
            throw new Zend_Controller_Action_Exception('Permission denied.', 404);
        }

        $form = new Application_Form_PostDiscussion(array('viewingUser' => $viewingUser));
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->_getAllParams())) {
                $formValues = $form->getValues();
                $formValues['tags'] = Application_Converter_Tags::getArray($formValues['tags']);

                // save revision entry
                $data = $this->_getRevisionData($formValues, $viewingUser);
                $data['updatedAt'] = date('Y-m-d H:i:s');
                $creatorComment = $formValues['creatorComment'];
                $revision = $viewingUser->createRevision($post->entity, $data, $creatorComment);
                if ($viewingUser->isModerator()) {
                    $reviewerComment = $formValues['reviewerComment'];
                    $revison->status = Sageweb_EntityRevision::STATUS_ACCEPTED;
                    $viewingUser->updateRevision($revision, $reviewerComment);
                }

                $url = $this->view->url(array('id' => $post->getSlugId()), 'discussion', true);
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
        $revisionData['forumId'] = $formValues['forumId'];
        $revisionData['title'] = $formValues['title'];
        $revisionData['body'] = $formValues['body'];
        $revisionData['tags'] = $formValues['tags'];
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
        $post = Sageweb_Table_Discussion::findOneById($id);
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
        $post = Sageweb_Table_Discussion::findOneById($id);
        if (!$post) {
            throw new Zend_Controller_Action_Exception(404, 'Discussion not found.');
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
                $revision->status = $this->_getParam('status');
                $viewingUser->updateRevision($revision, $reviewerComment);

                $url = $this->view->url(
                    array('action' => 'revisions', 'id' => $post->id), 'discussion', true);
                $this->_redirect($url);
            }
        }

        $this->view->post = $post;
        $this->view->revision = $revision;
        $this->view->currentData = $post->getRevisionData();
        $this->view->revisionData = Zend_Json::decode($revision->jsonData);
    }
}
