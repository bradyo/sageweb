<?php

class EventController extends Zend_Controller_Action
{
    const ITEMS_PER_PAGE = 10;
    const ENTITY_TYPE = Sageweb_Cms_Entity::TYPE_EVENT;

    private function _getViewMenu()
    {
        $menu = new Zend_Navigation();

        $page = new Zend_Navigation_Page_Mvc(array(
            'label' => 'Upcoming',
            'route' => 'events',
            'module' => 'default',
            'controller' => 'event',
            'action' => 'upcoming',
        ));
        $page->setResetParams(false);
        $menu->addPage($page);

        $page = new Zend_Navigation_Page_Mvc(array(
            'label' => 'Past',
            'route' => 'events',
            'module' => 'default',
            'controller' => 'event',
            'action' => 'past',
        ));
        $page->setResetParams(false);
        $menu->addPage($page);

        $page = new Zend_Navigation_Page_Mvc(array(
            'label' => 'Calendar',
            'route' => 'events',
            'module' => 'default',
            'controller' => 'event',
            'action' => 'calendar',
        ));
        $page->setResetParams(false);
        $menu->addPage($page);

        $page = new Zend_Navigation_Page_Mvc(array(
            'label' => 'List',
            'route' => 'events',
            'module' => 'default',
            'controller' => 'event',
            'action' => 'list',
        ));
        $page->setResetParams(false);
        $menu->addPage($page);

        return $menu;
    }

    public function upcomingAction()
    {
        if ($this->getRequest()->isPost()) {
            // convert filter form post to get parameters
            $types = $this->_getParam('types');
            $typesString = join(',', $types);
            $url = $this->view->url(
                array('action' => 'upcoming', 'type' => $typesString), 'events'
            );
            $this->_redirect($url);
        }

        // get selected types from param
        $types = $this->getTypes($this->_getParam('type', 'all'));
        $pager = Sageweb_Cms_Table_Event::getUpcomingPager($types);

        $page = $this->_getParam('page', 1);
        $pager->setItemCountPerPage(self::ITEMS_PER_PAGE);
        $pager->setCurrentPageNumber($page);

        $this->view->viewMenu = $this->_getViewMenu();
        $this->view->pager = $pager;
        $this->view->types = Sageweb_Cms_PostEvent::getTypeChoices();
        $this->view->selectedTypes = $types;
    }

    public function pastAction()
    {
        if ($this->getRequest()->isPost()) {
            // convert filter form post to get parameters
            $types = $this->_getParam('types');
            $typesString = join(',', $types);
            $url = $this->view->url(
                array('action' => 'upcoming', 'type' => $typesString), 'events'
            );
            $this->_redirect($url);
        }

        // get selected types from param
        $types = $this->getTypes($this->_getParam('type', 'all'));
        $pager = Sageweb_Cms_Table_Event::getPastPager($types);

        $page = $this->_getParam('page', 1);
        $pager->setItemCountPerPage(self::ITEMS_PER_PAGE);
        $pager->setCurrentPageNumber($page);

        $this->view->viewMenu = $this->_getViewMenu();
        $this->view->pager = $pager;
        $this->view->types = Sageweb_Cms_PostEvent::getTypeChoices();
        $this->view->selectedTypes = $types;
    }

    public function listAction()
    {
        if ($this->getRequest()->isPost()) {
            // convert filter form post to get parameters
            $types = $this->_getParam('types');
            $typesString = join(',', $types);
            $url = $this->view->url(
                array('action' => 'upcoming', 'type' => $typesString), 'events'
            );
            $this->_redirect($url);
        }

        // get selected types from param
        $types = $this->getTypes($this->_getParam('type', 'all'));
        $pager = Sageweb_Cms_Table_Event::getUpcomingPager($types);

        $page = $this->_getParam('page', 1);
        $pager->setItemCountPerPage(200);
        $pager->setCurrentPageNumber($page);

        $this->view->viewMenu = $this->_getViewMenu();
        $this->view->pager = $pager;
        $this->view->types = Sageweb_Cms_PostEvent::getTypeChoices();
        $this->view->selectedTypes = $types;
    }

    public function calendarAction()
    {
        if ($this->getRequest()->isPost()) {
            // convert filter form post to get parameters
            $types = $this->_getParam('types');
            $typesString = join(',', $types);
            $url = $this->view->url(
                array('action' => 'calendar', 'type' => $typesString), 'events'
            );
            $this->_redirect($url);
        }

        // get selected types
        $types = $this->getTypes($this->_getParam('type', 'all'));
        $posts = Sageweb_Cms_Table_Event::findByType($types);
        
        // build json representation of events for calendar
        $jsonData = array();
        foreach ($posts as $post) {
            $url = $this->view->url(array('id' => $post->getSlugId()), 'event', true);
            $eventData = array(
                'title' => $post->title,
                'start' => strtotime($post->startsAt),
                'end' => strtotime($post->endsAt),
                'url' => $url,
                'className' => $post->type,
            );
            $jsonData[] = $eventData;
        }

        $this->view->viewMenu = $this->_getViewMenu();
        $this->view->selectedTypes = $types;
        $this->view->eventsJson = Zend_Json::encode($jsonData);
        $this->view->types = Sageweb_Cms_PostEvent::getTypeChoices();
    }

    public function rssAction()
    {
        $this->getHelper('layout')->disableLayout();
        $types = $this->getTypes($this->_getParam('type', 'all'));
        $posts = Sageweb_Cms_Table_Event::findByType($types);
        $this->view->types = $types;
        $this->view->posts = $posts;
    }

    private function getTypes($typesString)
    {
        $types = explode(',', $typesString);
        if (in_array('all', $types)) {
            $types = array_keys(Sageweb_Cms_PostEvent::getTypeChoices());
        }
        return $types;
    }


    public function showAction()
    {
        $id = $this->_getParam('id');
        $post = Sageweb_Cms_Table_Event::findOneById($id);
        if (!$post) {
            throw new Zend_Controller_Action_Exception('Page not found', 404);
        }

        if (!empty($post->slug) && preg_match('/^\d+$/', $id)) {
            $fullUri = '/events/' . $post->getSlugId();
            $this->_redirect($fullUri, array('code' => 301));
        }

        // increment view counter
        $viewingUser = Sageweb_Registry::getUser();
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
        $viewingUser = Sageweb_Registry::getUser();
        $form = new Application_Form_PostEvent(array('viewingUser' => $viewingUser));
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getParams())) {
                $formValues = $form->getValues();
                $formValues['tags'] = My_Converter_Tags::getArray($formValues['tags']);

                // create a new article entry
                $data = $this->_getRevisionData($formValues, $viewingUser);
                $post = Sageweb_Cms_Table_Entity::createPost(self::ENTITY_TYPE, $data);

                // create revision entry
                $data['status'] = Sageweb_Cms_Abstract_Post::STATUS_PUBLIC;
                $revision = $viewingUser->createRevision($post->entity, $data);
                if ($viewingUser->isModerator()) {
                    $comment = $formValues['reviewerComment'];
                    $viewingUser->acceptRevision($revision, $comment);
                }

                $url = $this->view->url(array('id' => $post->getSlugId()), 'event', true);
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
        $post = Sageweb_Cms_Table_Event::findOneById($id);
        if (!$post) {
            throw new Zend_Controller_Action_Exception('Page not found', 404);
        }

        $viewingUser = Sageweb_Registry::getUser();
        if (!$viewingUser->canEdit($post)) {
            throw new Zend_Controller_Action_Exception('Permission denied.', 404);
        }

        $form = new Application_Form_PostEvent(array('viewingUser' => $viewingUser));
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->_getAllParams())) {
                $formValues = $form->getValues();
                $formValues['tags'] = My_Converter_Tags::getArray($formValues['tags']);

                // save revision entry
                $data = $this->_getRevisionData($formValues, $viewingUser);
                $data['updatedAt'] = date('Y-m-d H:i:s');
                $comment = $formValues['creatorComment'];
                $revision = $viewingUser->createRevision($post->entity, $data, $comment);
                if ($viewingUser->isModerator()) {
                    $comment = $formValues['reviewerComment'];
                    $viewingUser->acceptRevision($revision, $comment);
                }

                $url = $this->view->url(array('id' => $post->getSlugId()), 'event', true);
                $this->_redirect($url);
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
        $revisionData['title'] = $formValues['title'];
        $revisionData['summary'] = $formValues['summary'];
        $revisionData['body'] = $formValues['body'];
        $revisionData['type'] = $formValues['type'];
        $revisionData['location'] = $formValues['location'];
        $revisionData['startsAt'] = $formValues['startsAt'];
        $revisionData['endsAt'] = (!empty($formValues['endsAt'])) ? $formValues['endsAt'] : NULL;
        $revisionData['url'] = $formValues['url'];
        $revisionData['categories'] = $formValues['categories'];
        $revisionData['tags'] = $formValues['tags'];
        $revisionData['isFeatured'] = false;
        if ($viewingUser->isModerator()) {
            $revisionData['status'] = $formValues['status'];
            $revisionData['isFeatured'] = $formValues['isFeatured'];

            $username = $formValues['author'];
            $author = Sageweb_Cms_Table_User::findOneByUsername($username);
            $revisionData['authorId'] = $author->id;
        }
        return $revisionData;
    }

    public function revisionsAction()
    {
        $id = $this->_getParam('id');
        $event = Sageweb_Cms_Table_Event::findOneById($id);
        if (!$event) {
            throw new Zend_Controller_Action_Exception(404, 'Post not found.');
        }
        $revisions = Sageweb_Cms_Table_Revision::findByEntityId($event->entityId);

        $this->view->post = $event;
        $this->view->revisions = $revisions;
    }

    public function revisionAction()
    {
        $id = $this->_getParam('id');
        $post = Sageweb_Cms_Table_Event::findOneById($id);
        if (!$post) {
            throw new Zend_Controller_Action_Exception(404, 'Post not found.');
        }

        $revisionId = $this->_getParam('revisionId');
        $revision = Sageweb_Cms_Table_Revision::findOneByEntityId($post->entityId, $revisionId);
        if (!$revision) {
            throw new Zend_Controller_Action_Exception(404, 'Revision not found.');
        }

        if ($this->_request->isPost()) {
            $viewingUser = Sageweb_Registry::getUser();
            if ($viewingUser->isModerator()) {
                // accept or reject revision
                $comment = $this->_getParam('reviewerComment');
                $status = $this->_getParam('status');
                if ($status == Sageweb_Cms_EntityRevision::STATUS_ACCEPTED) {
                    $viewingUser->acceptRevision($revision, $comment);
                } else {
                    $viewingUser->rejectRevision($revision, $comment);
                }

                $url = $this->view->url(
                    array('action' => 'revisions', 'id' => $post->id), 'event', true);
                $this->_redirect($url);
            }
        }

        $this->view->post = $post;
        $this->view->revision = $revision;
        $this->view->currentData = $post->getRevisionData();
        $this->view->revisionData = Zend_Json::decode($revision->jsonData);
    }
}
