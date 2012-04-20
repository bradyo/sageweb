<?php

/**
 * @property string $title
 * @property string $slug
 * @property string $summary
 * @property string $body
 * @property boolean $isFeatured
 *
 * @author Brady Olsen <bradyo@uw.edu>
 */
class Sageweb_Cms_Abstract_PostContent extends Sageweb_Cms_Abstract_Post
{
    public function setTableDefinition()
    {
        parent::setTableDefinition();

        $this->hasColumn('title', 'string', 128);
        $this->hasColumn('slug', 'string', 128);
        $this->hasColumn('summary', 'clob');
        $this->hasColumn('body', 'clob');
        $this->hasColumn('is_featured as isFeatured', 'boolean');
    }

    public function preSave($event)
    {
        parent::preSave($event);
        $this->slug = My_Converter_Slugify::getSlug($this->_get('title'));
    }

    public function postSave($event) {
        parent::postSave($event);

        $content = Sageweb_Cms_Table_Content::findOneByEntityId($this->entityId);
        if (!$content) {
            $content = new Sageweb_Cms_PostContent();
        }
        $content->entityType = $this->entity->type;
        $content->entityId = $this->entityId;
        $content->postId = $this->id;
        $content->status = $this->status;
        $content->createdAt = $this->createdAt;
        $content->updatedAt = $this->updatedAt;
        $content->authorId = $this->authorId;
        $content->title = $this->title;
        $content->slug = $this->slug;
        $content->isFeatured = $this->isFeatured;
        $content->summary = $this->summary;
        $content->save();
    }

    public function getRevisionData()
    {
        $data = $this->getData();
        $data['categories'] = $this->getCategoryValues();
        $data['tags'] = $this->getTagValues();
        return $data;
    }

    public function getSlugId()
    {
        $slug = $this->_get('slug');
        if (!empty($slug)) {
            return $this->_get('id') . '-' . $this->_get('slug');
        } else {
            return $this->_get('id');
        }
    }
    
}
