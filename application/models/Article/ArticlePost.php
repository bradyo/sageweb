<?php

class Application_Model_Article_ArticlePost extends Sageweb_Post {
    
    public function setTableDefinition() {
        $this->setTableName('post_article');
        $this->option('type', 'INNODB');

    }
}
