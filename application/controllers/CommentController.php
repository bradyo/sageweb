<?php

class CommentController extends Zend_Controller_Action
{
    public function editAction()
    {
        $id = $this->getRequest()->getParam('id');
        $post = Sageweb_Table_Comment::findOneById($id);
        if (!$post) {
            throw new Zend_Controller_Action_Exception('Page not found', 404);
        }

        $viewingUser = Application_Registry::getUser();
        if (!$viewingUser->canEdit($post)) {
            throw new Zend_Controller_Action_Exception('Permission denied.', 404);
        }

        $form = new Application_Form_PostComment(array('viewingUser' => $viewingUser));
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->_getAllParams())) {
                $formValues = $form->getValues();

                // save revision entry
                $data = $this->_getRevisionData($formValues, $viewingUser);
                $data['updatedAt'] = date('Y-m-d H:i:s');
                $comment = $formValues['creatorComment'];
                $revision = $viewingUser->createRevision($post->entity, $data, $comment);
                if ($viewingUser->isModerator()) {
                    $comment = $formValues['reviewerComment'];
                    $viewingUser->acceptRevision($revision, $comment);
                }

                Application_Registry::getFlashMessenger()->addMessage(array(
                    'type' => 'info',
                    'value' => 'Updated successfully.'
                ));
                $this->_redirect($this->view->url());
            }
        } else {
            $form->populate($post->toArray());
            if ($viewingUser->isModerator()) {
                $form->getElement('author')->setValue($viewingUser->username);
            }
        }

        $this->view->post = $post;
        $this->view->form = $form;
    }

    private function _getRevisionData($formValues, $viewingUser)
    {
        $revisionData['authorId'] = $viewingUser->id;
        $revisionData['body'] = $formValues['body'];
        if ($viewingUser->isModerator()) {
            $revisionData['status'] = $formValues['status'];

            $username = $formValues['author'];
            $author = Sageweb_Table_User::findOneByUsername($username);
            $revisionData['authorId'] = $author->id;
        }
        return $revisionData;
    }
    
    public function addCommentAction()
    {
        $postId = $this->_getParam('id');

        $user = Zend_Auth::getInstance()->getIdentity();
        if (!$user) {
            return;
        }

        $comment = new Sageweb_Model__Comment();
        $comment->fromArray(array(
            'parentEntityId' => $this->_getParam('parentEntityId'),
            'parentCommentId' => $this->_getParam('parentCommentId'),
            'depth' => $depth,
            'body' => $this->_getParam('body')
        ));
    }
}
