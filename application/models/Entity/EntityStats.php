<?php

/**
 * Holds entity values such as statistics.
 *
 * @property integer $entityId
 * @property integer $upVotesCount
 * @property integer $downVotesCount
 * @property integer $rating
 * @property integer $commentsCount
 * @property integer $viewsCount
 */
class Application_Model_Entity_EntityStats extends Doctrine_Record {

    public function setTableDefinition() {
        // entity table holds statistics itself
        $this->setTableName('entity');
        $this->option('type', 'INNODB');
        
        $this->hasColumn('id as entityId', 'integer', 4, array(
            'primary' => true,
            'autoincrement' => true,
        ));
        $this->hasColumn('rating', 'integer');
        $this->hasColumn('up_votes_count as upVotesCount', 'integer');
        $this->hasColumn('down_votes_count as downVotesCount', 'integer');
        $this->hasColumn('comments_count as commentsCount', 'integer');
        $this->hasColumn('views_count as viewsCount', 'integer');
    }

    public function preSave($event) {
        $this->rating = $this->upVotesCount - $this->downVotesCount;
    }
}
