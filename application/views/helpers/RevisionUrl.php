<?php

class Zend_View_Helper_RevisionUrl extends Zend_View_Helper_Abstract
{
    /**
     * Adds redirect uri to url route
     * @param string $url the url to format
     * @return string
     */
    public function revisionUrl($entity, $revisionId)
    {
        $post = Sageweb_Cms_Table_Entity::findPostByEntity($entity);

        $routeName = $entity->type . 'Revision';
        $url = $this->view->url(
            array('id' => $post->id, 'revisionId' => $revisionId),
            $routeName, true);
        return $url;
    }
}