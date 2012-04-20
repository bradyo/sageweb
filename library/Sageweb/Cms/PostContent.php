<?php

/**
 * @property string $entityType
 * 
 * @author Brady Olsen <bradyo@uw.edu>
 */
class Sageweb_Cms_PostContent extends Sageweb_Cms_Abstract_PostContent
{
    public function setTableDefinition()
    {
        parent::setTableDefinition();
        $this->setTableName('post_content');

        $this->hasColumn('post_id as postId', 'integer');
        $this->hasColumn('entity_type as entityType', 'string', '32');
    }

    public function postSave($event)
    {
        // skip call to parent on save (since it syncs with PostContent table)
    }

    public function getSlugId()
    {
        $slug = $this->_get('slug');
        if (!empty($slug)) {
            return $this->_get('postId') . '-' . $this->_get('slug');
        } else {
            return $this->_get('postId');
        }
    }
}
