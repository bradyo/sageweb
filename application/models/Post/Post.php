<?php

/**
 * @property integer $id
 * @property integer $entityId
 * @property string $type
 * @property integer $creatorId
 * @property Application_Model_User_User $creator
 * @property string $review_status
 * @property integer $reviewerId
 * @property Application_Model_User_User $reviewer
 * @property string $status
 * @property integer $publicId
 * @property integer $version
 * @property integer $isLatest
 * @property integer $authorId
 * @property Application_Model_User_User $author
 * @property string $whenAt
 * @property string $title
 * @property string $slug
 * @property boolean $isFeatured
 * @property string $summary
 * @property string $body
 * 
 * @property Application_Model_Entity_EntityStats $stats
 * @property array $categories array of Application_Model_Post_Category objects
 * @property array $tags array of Application_Model_Post_Tag objects
 */
class Application_Model_Post_Post extends Doctrine_Record {

    const REVIEW_STATUS_PENDING = 'pending';
    const REVIEW_STATUS_ACCEPTED = 'accepted';
    const REVIEW_STATUS_REJECTED = 'rejected';
    
    const STATUS_PUBLIC = 'public';
    const STATUS_DELETED = 'deleted';
    
    public function setTableDefinition() {
        $this->setTableName('post');
        $this->hasColumn('id', 'integer', 4, array(
            'primary' => true,
            'autoincrement' => true,
        ));
        $this->hasColumn('type as type', 'string', 32);
        $this->hasColumn('entity_id as entityId', 'integer');
        $this->hasColumn('creator_id as creatorId', 'integer');
        $this->hasColumn('created_at as createdAt', 'timestamp');
        $this->hasColumn('creator_comment as creatorComment', 'string');
        $this->hasColumn('review_status as reviewStatus', 'integer');
        $this->hasColumn('reviewer_id as reviewerId', 'integer');
        $this->hasColumn('reviewer_comment as reviewerComment', 'string');
        $this->hasColumn('status', 'string', 32);
        $this->hasColumn('public_id as publicId', 'integer');
        $this->hasColumn('version', 'integer');
        $this->hasColumn('is_latest as isLatest', 'boolean');
        $this->hasColumn('author_id as authorId', 'integer');
        $this->hasColumn('when_at as whenAt', 'timestamp');
        $this->hasColumn('title', 'string', 128);
        $this->hasColumn('slug', 'string', 128);
        $this->hasColumn('is_featured as isFeatured', 'boolean');
        $this->hasColumn('summary', 'clob');
        $this->hasColumn('body', 'clob');        
    }

    public function setUp(){
        $this->hasOne('Application_Model_User_User as creator', array(
            'local' => 'creator_id',
            'foreign' => 'id',
        ));
        $this->hasOne('Application_Model_User_User as reviewer', array(
            'local' => 'reviewer_id',
            'foreign' => 'id',
        ));
        $this->hasOne('Application_Model_User_User as author', array(
            'local' => 'author_id',
            'foreign' => 'id',
        ));
        $this->hasOne('Application_Model_Entity_EntityStats as stats', array(
            'local' => 'entity_id',
            'foreign' => 'id',
        ));
        $this->hasMany('Application_Model_Post_Category as categories', array(
            'local' => 'post_id',
            'foreign' => 'category_id',
            'refClass' => 'Application_Model_Post_PostCategory',
        ));
        $this->hasMany('Application_Model_Post_Tag as tags', array(
            'local' => 'id',
            'foreign' => 'post_id'
        ));
        
        // link in post types
        // TODO: move to config, or figure out how to reverse the relationship
        // so these relations can be defined in post sub-type classes
        $this->hasOne('Application_Model_Event_Event as event', array(
            'local' => 'id',
            'foreign' => 'post_id',
        ));
    }

    public function getSlugId() {
        $slugId = $this->publicId;
        if (!empty($this->slug)) {
            $slugId .= '-' . $this->slug;
        }
        return $slugId;
    }
    
    public function isPublic() {
        return $this->status == self::STATUS_PUBLIC;
    }
    
    public static function getPublicIdFromSlug($slug) {
        $matches = array();
        preg_match('/^(\d+).*$/', $slug, $matches);
        if (isset($matches[1])) {
            return $matches[1];
        } else {
            return null;
        }
    }
    
    public function addViewBy(Application_Model_User_User $user) {
        if ($user->isGuest()) {
            return;
        }
        
        // check if view already exists in the last 30 min
        $targetDate = new Zend_Date(time());
        $targetDate->subMinute(30);
        
        // check if this user has had views in the last
        $viewSearch = new Application_Model_User_ViewSearch();
        $viewSearch->setUser($user);
        $viewSearch->setAfter($targetDate);
        
        // fetch existing views
        $recentViews = $this->fetchAll($viewSearch);
        if (count($recentViews) > 0) {
            return;
        }

        // TODO fix me
//        $query = Doctrine_Query::create()->from('Application_Model_Entity_EntityView v')
//            ->where('v.entityId = ?', $this->entityId)
//            ->andWhere('v.ipAddress = ?', $_SERVER['REMOTE_ADDR'])
//            ->andWhere('v.createdAt > ?', date('Y-m-d H:i:s', $date->getTimestamp()))
//            ->limit(1);
//        $recentView = $query->fetchOne();

        if (!$recentView) {
            // record the view
            $view = new Application_Model_Entity_EntityView();
            $view->entityId = $this->entityId;
            $view->ipAddress = $_SERVER['REMOTE_ADDR'];
            $view->userId = $user->id;
            $view->save();
        }
    }
}
