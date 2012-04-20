<?php

/**
 * Doctrine ORM Base class for content posts (article, file, link). Content posts
 * have title, summary, etc.
 * 
 * @property string $title
 * @property string $slug
 * @property string $summary
 * @property boolean $isFeatured
 *
 * @property array $comments
 *
 * @author Brady Olsen <bradyo@uw.edu>
 */
abstract class Sageweb_Cms_Abstract_Content extends Sageweb_Cms_Abstract_Post
{
    public function setTableDefinition()
    {
        parent::setTableDefinition();
        $this->hasColumn('title', 'string', 128);
        $this->hasColumn('slug', 'string', 128);
        $this->hasColumn('summary', 'clob');
        $this->hasColumn('is_featured as isFeatured', 'boolean');
    }


    public function getSlugId()
    {
        $slug = $this->_get('slug');
        if (!empty($slug)) {
            return $this->_get('entityId') . '-' . $this->_get('slug');
        } else {
            return $this->_get('entityId');
        }
    }

    public function preSave($event)
    {
        parent::preSave($event);

        // update slug
        $this->slug = My_Converter_Slugify::getSlug($this->_get('title'));
    }

    public function getFeaturedString()
    {
        if ($this->isFeatured) {
            return 'yes';
        } else {
            return 'no';
        }
    }

    public function hasIcon()
    {
        return false;
    }

    public function getIconUri()
    {
        return null;
    }
}
