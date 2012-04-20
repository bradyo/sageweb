<?php

/**
 *
 * @author brady
 */
class ManageController extends Zend_Controller_Action
{
    public function preDispatch()
    {
        parent::preDispatch();

        $viewingUser = Sageweb_Registry::getUser();
        if (!$viewingUser->isModerator()) {
            throw new Zend_Controller_Action_Exception('Access Denied', 404);
        }

        $this->view->moderatorMenu = $this->_buildModeratorMenu();
    }

    private function _buildModeratorMenu()
    {
        $pages = array();

        $revisionsPage = new Zend_Navigation_Page_Mvc(array(
            'label' => 'Revisions',
            'action' => 'revisions',
            'controller' => 'manage',
            'module' => 'default',
            'route' => 'manage',
        ));
        $pages[] = $revisionsPage;

        $flaggedPage = new Zend_Navigation_Page_Mvc(array(
            'label' => 'Flags',
            'action' => 'flags',
            'route' => 'manage'
        ));
        $pages[] = $flaggedPage;

        return new Zend_Navigation($pages);
    }

    public function indexAction()
    {
        $this->_forward('revisions');
    }

    public function revisionsAction()
    {
        $q = Doctrine_Query::create()
            ->from('Sageweb_Cms_EntityRevision r, r.entity e, r.creator c, r.reviewer r2')
            ->where('r.status = ?', Sageweb_Cms_EntityRevision::STATUS_PENDING)
            ->andWhere('e.type <> ?', 'comment');

        $type = $this->_getParam('type', 'any');
        if ($type != 'any') {
            $q->andWhere('e.type = ?', $this->_getParam('type'));
        }

        $sort = $this->_getParam('sort', 'createdAt');
        $order = $this->_getParam('order', 'desc');
        if ($sort != '') {
            if (!in_array($order, array('asc', 'desc'))) {
                $order = 'asc';
            }
            switch ($sort) {
                case 'type':
                    $q->orderBy('e.type ' . $order);
                    break;
                case 'status':
                    $q->orderBy('r.status ' . $order);
                    break;
                case 'creator':
                    $q->orderBy('c.username ' . $order);
                    break;
                case 'reviewer':
                    $q->orderBy('r2.username ' . $order);
                    break;
                case 'createdAt':
                    $q->orderBy('r.createdAt ' . $order);
                    break;
                case 'reviewedAt':
                    $q->orderBy('r.reviewedAt ' . $order);
                    break;
            }
        }
        $this->view->revisions = $q->execute();

        $this->view->type = $this->_getParam('role');
        $this->view->status = $this->_getParam('status', Sageweb_Cms_EntityRevision::STATUS_PENDING);
        $this->view->creator = $this->_getParam('creator');
        $this->view->reviewer = $this->_getParam('reviewer');
        $this->view->sort = $this->_getParam('sort', 'createdAt');
        $this->view->order = $this->_getParam('order');
    }

    public function flagsAction()
    {
        if ($this->_hasParam('id')) {
            $this->_forward('flag');
        }

        $this->view->flags = Sageweb_Cms_Table_Flag::findAll();
    }

    public function flagAction()
    {
        $id = $this->_getParam('id');

        $flag = Sageweb_Cms_Table_Flag::findOneById($id);
        if (!$flag) {
            throw new Zend_Controller_Action_Exception('Page not found', 404);
        }

        if ($this->getRequest()->isPost() && $this->_hasParam('deleteSubmit')) {
            $flag->delete();

            $url = $this->view->url(
                array('controller' => 'manage', 'action' => 'flags'),
                'default', true);
            $this->_redirect($url);
        }

        $entity = Sageweb_Cms_Table_Entity::findOneById($flag->entityId);
        $post = Sageweb_Cms_Table_Entity::findPostByEntity($entity);
        
        $this->view->flag = $flag;
        $this->view->post = $post;
    }

}
