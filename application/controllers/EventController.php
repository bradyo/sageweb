<?php

class EventController extends Zend_Controller_Action {
    
    /**
     * Number of items to show on each page.
     */
    const ITEMS_PER_PAGE = 10;
    
    /**
     * Number of items to show in Feed listings.
     */
    const ITEMS_PER_FEED = 50;
  
    /**
     * @var Application_Model_Event_EventRepository
     */
    private $eventRepository;
    
    
    public function init() {
        $this->eventRepository = Application_Registry::getEventRepository();
        $this->view->eventTypes = new Application_Model_Event_EventTypes();
    }
    
    public function testAction() {
        //$this->eventRepository->rebuildIndex();
    }
    
    public function indexAction() {
        $this->_helper->redirector('calendar');
    }
       
    /**
     * @return Application_Model_Event_EventFilter 
     */
    private function getFilterFromRequestParameters() {
        $filter = new Application_Model_Event_EventFilter();
        $params = $this->getRequest()->getParams();
        if (!empty($params['search'])) {
            $filter->search = $params['search'];
        }
        if (!empty($params['eventTypes'])) {
            $types = array();
            foreach ($params['eventTypes'] as $value) {
                if (!empty($value)) {
                    $types[] = $value;
                }
            }
            if (count($types) > 0) {
                $filter->eventTypes = $types;
            }
        }
        if (!empty($params['sortBy'])) {
            $sortBy = $params['sortBy'];
            if ($sortBy == 'startDate') {
                $filter->orderBy = 'event.startDate asc';
            }
        }
        return $filter;
    }
   
    public function upcomingAction() {
        $this->view->title = 'Upcoming Events';
        
        // set up search criteria
        $filter = $this->getFilterFromRequestParameters();
        $filter->startDateAfter = date('Y-m-d');
        $this->view->filter = $filter;
        
        // set up pager
        $itemsCount = $this->eventRepository->getCount($filter);
        $pager = new Application_Pager($itemsCount, self::ITEMS_PER_PAGE);
        $pageNumber = $this->_getParam('page', 1);
        $pager->setCurrentPage($pageNumber);
        $this->view->pager = $pager;
        
        // fetch items
        $offset = ($pageNumber - 1) * self::ITEMS_PER_PAGE;
        $events = $this->eventRepository->getAll($filter, $offset, self::ITEMS_PER_PAGE);
        $this->view->events =  $events;
        
        $this->renderScript('event/list.phtml');
    }
    
    public function pastAction() {
        $this->view->title = 'Past Events';
        
        // set up search criteria
        $filter = $this->getFilterFromRequestParameters();
        $filter->startDateBefore = date('Y-m-d');
        $this->view->filter = $filter;
        
        // set up pager
        $itemsCount = $this->eventRepository->getCount($filter);
        $pager = new Application_Pager($itemsCount, self::ITEMS_PER_PAGE);
        $pageNumber = $this->_getParam('page', 1);
        $pager->setCurrentPage($pageNumber);
        $this->view->pager = $pager;
        
        // fetch items
        $offset = ($pageNumber - 1) * self::ITEMS_PER_PAGE;
        $events = $this->eventRepository->getAll($filter, $offset, self::ITEMS_PER_PAGE);
        $this->view->events =  $events;
        
        $this->renderScript('event/list.phtml');
    }

    public function calendarAction() {
        $this->view->title = 'Events Calendar';
        
        // set up search criteria
        $filter = $this->getFilterFromRequestParameters();
        $this->view->filter = $filter;
        
        // fetch items
        $events = $this->eventRepository->getAll($filter);
        $this->view->events =  $events;
        
        // build json representation of events for calendar
        $jsonData = array();
        foreach ($events as $event) {
            $url = $this->view->url(array('id' => $event->post->getSlugId()), 'events', true);
            $eventData = array(
                'title' => $this->view->escape($event->post->title),
                'start' => strtotime($event->startDate),
                'end' => strtotime($event->endDate),
                'url' => $this->view->escape($url),
                'className' => $event->eventType,
            );
            $jsonData[] = $eventData;
        }
        $this->view->eventsJson = Zend_Json::encode($jsonData);
    }

    public function rssAction() {
        $this->getHelper('layout')->disableLayout();
        
        // set up search criteria
        $filter = $this->getFilterFromRequestParameters();
        $filter->orderBy = 'event.post.createdAt desc';
        $this->view->filter = $filter;
        
        // fetch items
        $events = $this->eventRepository->getAll($filter, 0, self::ITEMS_PER_FEED);
        $this->view->events =  $events;
    }

    public function showAction() {
        $publicId = $this->_getParam('id');
        
        $event = $this->eventRepository->getLatestById($publicId);
        if (!$event) {
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
    
    public function getPublicIdFromSlug($slug) {
        $matches = $array();
        preg_match('^(\d+).+$', $slug, $matches);
        return $matches[1];
    }

    public function addAction()
    {
        $viewingUser = Application_Registry::getUser();
        $form = new Application_Model_Event_EventForm($viewingUser);
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getParams())) {
                $formValues = $form->getValues();
                $formValues['tags'] = Application_Converter_Tags::getArray($formValues['tags']);

                // create a new article entry
                $data = $this->_getRevisionData($formValues, $viewingUser);
                $post = Sageweb_Table_Entity::createPost('event', $data);

                // create revision entry
                $data['status'] = Sageweb_Abstract_Post::STATUS_PUBLIC;
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
        $post = Sageweb_Table_Event::findOneById($id);
        if (!$post) {
            throw new Zend_Controller_Action_Exception('Page not found', 404);
        }

        $viewingUser = Application_Registry::getUser();
        if (!$viewingUser->canEdit($post)) {
            throw new Zend_Controller_Action_Exception('Permission denied.', 404);
        }

        $form = new Application_Form_PostEvent(array('viewingUser' => $viewingUser));
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

    public function revisionsAction()
    {
        $id = $this->_getParam('id');
        $event = Sageweb_Table_Event::findOneById($id);
        if (!$event) {
            throw new Zend_Controller_Action_Exception(404, 'Post not found.');
        }
        $revisions = Sageweb_Table_Revision::findByEntityId($event->entityId);

        $this->view->post = $event;
        $this->view->revisions = $revisions;
    }

    public function revisionAction()
    {
        $id = $this->_getParam('id');
        $post = Sageweb_Table_Event::findOneById($id);
        if (!$post) {
            throw new Zend_Controller_Action_Exception(404, 'Post not found.');
        }

        $revisionId = $this->_getParam('revisionId');
        $revision = Sageweb_Table_Revision::findOneByEntityId($post->entityId, $revisionId);
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
