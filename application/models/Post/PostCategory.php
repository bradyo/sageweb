<?php

/**
 * @property integer $postId
 * @property integer $categoryId
 */
class Application_Model_Post_PostCategory extends Doctrine_Record {
    
    public function setTableDefinition() {
        $this->setTableName('post_category');
        $this->option('type', 'INNODB');

        $this->hasColumn('post_id as postId', 'integer', 4, array(
            'primary' => true,
        ));
        $this->hasColumn('category_id as categoryId', 'integer', 4, array(
            'primary' => true,
        ));
    }
}
