<?php

/**
 * Base class for an article post.
 *
 * @author Brady Olsen <bradyo@uw.edu>
 */
class Sageweb_Cms_Content extends Sageweb_Cms_Abstract_Content
{
    public function setTableDefinition()
    {
        parent::setTableDefinition();
        $this->setTableName('content');

        $this->hasColumn('entity_type as entityType', 'string', 32);
    }

    public function getRevisionData()
    {
    }
}
