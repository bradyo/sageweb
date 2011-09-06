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
    
    public function indexAction() {
        $this->_helper->redirector('calendar');
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
        $posts = $this->eventRepository->getAll($filter, $offset, self::ITEMS_PER_PAGE);
        $this->view->posts = $posts;
        
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
        $posts = $this->eventRepository->getAll($filter, $offset, self::ITEMS_PER_PAGE);
        $this->view->posts =  $posts;
        
        $this->renderScript('event/list.phtml');
    }

    public function calendarAction() {
        $this->view->title = 'Events Calendar';
        
        // set up search criteria
        $filter = $this->getFilterFromRequestParameters();
        $this->view->filter = $filter;
        
        // fetch items
        $posts = $this->eventRepository->getAll($filter);
        $this->view->posts =  $posts;
        
        // build json representation of events for calendar
        $jsonData = array();
        foreach ($posts as $post) {
            $url = $this->view->url(array('id' => $post->getSlugId()), 'events', true);
            $eventData = array(
                'title' => $this->view->escape($post->title),
                'start' => strtotime($post->event->startDate),
                'end' => strtotime($post->event->endDate),
                'url' => $this->view->escape($url),
                'className' => $post->event->eventType,
            );
            $jsonData[] = $eventData;
        }
        $this->view->eventsJson = Zend_Json::encode($jsonData);
    }

    public function rssAction() {
        $this->getHelper('layout')->disableLayout();
        
        // set up search criteria
        $filter = $this->getFilterFromRequestParameters();
        $filter->orderBy = 'post.createdAt desc';
        $this->view->filter = $filter;
        
        // fetch items
        $posts = $this->eventRepository->getAll($filter, 0, self::ITEMS_PER_FEED);
        $this->view->posts =  $posts;
    }

    public function showAction() {
        $slugId = $this->_getParam('id');
        $publicId = Application_Model_Post_Post::getPublicIdFromSlug($slugId);
        
        $post = $this->eventRepository->getLatestById($publicId);
        if (!$post) {
            throw new Zend_Controller_Action_Exception('Page not found', 404);
        }
        $this->view->post = $post;

        // increment view counter
        $currentUser = Application_Registry::getCurrentUser();
        if ($post->isPublic()) {
            $post->addViewBy($currentUser);

            // fetch vote record
            $existingVote = $currentUser->getVote($post->entityId);
            if ($existingVote) {
                $this->view->voteValue = $existingVote->value;
            }
        }

        $this->view->canEdit = $currentUser->canEdit($post);
    }
    
    public function addAction() {
        $currentUser = Application_Registry::getCurrentUser();
        $form = new Application_Model_Event_EventForm($currentUser);
        
        die();
        
        $this->view->form = $form;
        
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->getRequest()->getParams())) {
                $formValues = $form->getValues();
                
                print_r($formValues);
                die();
                
                $formValues['tags'] = Application_Converter_Tags::getArray($formValues['tags']);

                // create a new post
                $post = new Application_Model_Post_Post();
                $post->status = Application_Model_Post_Post::REVIEW_STATUS_PENDING;
                $post->title = $formValues['title'];
                $post->summary = $formValues['summary'];
 
                
                $post->event = new Application_Model_Event_Event();
                

                if ($currentUser->isModerator()) {
                    $post->reviewerId = $currentUser->id;
                    $post->status = Application_Model_Post_Post::REVIEW_STATUS_ACCEPTED;
                    $post->reviewerComment = $formValues['reviewerComment'];
                }
                $this->eventRepository->save($post);
                

                $url = $this->view->url(array('id' => $post->getSlugId()), 'event', true);
                $this->_redirect($url);
            }
        } else {
            if ($currentUser->isModerator()) {
                $form->getElement('author')->setValue($currentUser->username);
            }
        }
        
    }

    public function editAction()
    {
        $id = $this->getRequest()->getParam('id');
        $post = Sageweb_Table_Event::findOneById($id);
        if (!$post) {
            throw new Zend_Controller_Action_Exception('Page not found', 404);
        }

        $viewingUser = Application_Registry::getCurrentUser();
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
}
