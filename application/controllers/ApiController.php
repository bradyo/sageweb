<?php

/**
 * Handles API requests
 *
 * @author brady
 */
class ApiController extends Zend_Controller_Action
{
    public function checkUsernameAction()
    {
        $username = trim($this->_getParam('username'));

        // check validity
        $usernameElement = new Zend_Form_Element_Text('username', array(
            'required' => true
        ));
        $usernameElement->addValidators(array(
            new Zend_Validate_NotEmpty(),
            new Zend_Validate_StringLength(array(
                'min' => 3,
                'max' => 32
            )),
            new Zend_Validate_Alnum(false)
        ));

        $isValid = $usernameElement->isValid($username);
        if (! $isValid) {
            $errors = $usernameElement->getErrors();
            if (in_array('notAlnum', $errors)) {
                $message = 'must be alpha numeric';
            }
            if (in_array('stringLengthTooShort', $errors)) {
                $message = 'must be at least 3 characters';
            }
            if (in_array('stringLengthTooLong', $errors)) {
                $message = 'must be less than 32 characters';
            }
            if (in_array('isEmpty', $errors)) {
                $message = 'required';
            }
        }

        // check availablilty
        $isAvailable = false;
        $user = Sageweb_Cms_Table_User::findOneByUsername($username);
        if (!$user) {
            $isAvailable = true;
        }

        // return json data
        $data = array(
            'isAvailable' => $isAvailable,
            'isValid' => $isValid,
            'message' => $message,
        );
        $this->_helper->json->sendJson($data);
    }

    public function getUsernamesAction()
    {
        $q = $this->_getParam('term');
        $usernames = Sageweb_Cms_Table_User::getUsernames($q, $count = 10);
        $this->_helper->json->sendJson($usernames);
    }

    public function getTagsAction()
    {
        $searchTerm = $this->_getParam('term');

        $db = Sageweb_Registry::getDb();
        $stmt = $db->prepare('SELECT DISTINCT value FROM entity_tag WHERE value LIKE ?');
        $stmt->execute(array($searchTerm . '%'));
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $tags = array();
        foreach ($rows as $row) {
            $tags[] = $row['value'];
        }
        $this->_helper->json->sendJson($tags);
    }

    public function voteUpAction()
    {
        $entityId = $this->_getParam('entityId');
        $entity = Doctrine_Query::create()->from('Sageweb_Cms_Entity e')
            ->where('e.id = ?', $entityId)
            ->limit(1)
            ->fetchOne();
        Sageweb_Registry::getUser()->vote($entity, 1);
        
        $response = array('success' => true);
        $this->_helper->json->sendJson($response);
    }

    public function voteDownAction()
    {
        $entityId = $this->_getParam('entityId');
        $entity = Doctrine_Query::create()->from('Sageweb_Cms_Entity e')
            ->where('e.id = ?', $entityId)
            ->limit(1)
            ->fetchOne();
        Sageweb_Registry::getUser()->vote($entity, -1);

        $response = array('success' => true);
        $this->_helper->json->sendJson($response);
    }

    public function getPubmedDataAction()
    {
        $pubmedId = $this->_getParam('pubmedId');
        $data = Application_Service_PubMed::getCitationData($pubmedId);
        $this->_helper->json->sendJson($data);
    }

    public function commentAction()
    {
        $rootEntityId = $this->_getParam('rootEntityId');
        $parentEntityId = $this->_getParam('parentEntityId');

        $viewingUser = Sageweb_Registry::getUser();
        $form = new Application_Form_PostComment();
        if ($form->isValid($this->_getAllParams())) {
            $formValues = $this->_getAllParams();

            // create a new article entry
            $data = array(
                'rootEntityId' => $rootEntityId,
                'parentEntityId' => $parentEntityId,
                'status' => Sageweb_Cms_Abstract_Post::STATUS_PENDING,
                'createdAt' => date('Y-m-d H:i:s'),
                'authorId' => $viewingUser->id,
                'name' => $formValues['name'],
                'email' => $formValues['email'],
                'url' => $formValues['url'],
                'body' => $formValues['body'],
            );
            $type = Sageweb_Cms_Entity::TYPE_COMMENT;
            $comment = Sageweb_Cms_Table_Entity::createPost($type, $data);

            // create revision entry
            $data['status'] = Sageweb_Cms_Abstract_Post::STATUS_PUBLIC;
            $revision = $viewingUser->createRevision($comment->entity, $data);

            // if moderator, automatically accept revision
            if ($viewingUser->isModerator()) {
                $reviewerComment = $formValues['reviewerComment'];
                $viewingUser->acceptRevision($revision, $reviewerComment);
            }

            $entity = Sageweb_Cms_Table_Entity::findOneById($rootEntityId);
            $post = Sageweb_Cms_Table_Entity::findPostByEntity($entity);
            $post->updateCommentsCount();

            $comment->setDepth($this->_getParam('depth', 1));
            $response = $this->view->partial('post/_comment.phtml',
                array('comment' => $comment));
        }

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $this->getResponse()->setHeader('Content-Type', 'text/html');
        echo $response;
        return;
    }

    public function flagAction()
    {
        $entityId = $this->_getParam('entityId');

        $viewingUser = Sageweb_Registry::getUser();
        $form = new Application_Form_Flag();
        if ($form->isValid($this->_getAllParams()) && !$viewingUser->isGuest()) {
            $values = $form->getValues();

            $flag = new Sageweb_Cms_EntityFlag();
            $flag->creatorId = $viewingUser->id;
            $flag->entityId = $values['entityId'];
            $flag->createdAt = date('Y-m-d H:i:s');
            $flag->type = $values['type'];
            $flag->comment = $values['comment'];
            $flag->save();

            $data = array('result' => 'success');
        }
        else {
            $data = array('result' => 'error');
        }

        $this->_helper->json->sendJson($data);
    }
}
