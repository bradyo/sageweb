<?php

class Application_View_Helper_PostEditUrl extends Zend_View_Helper_Abstract
{
    /**
     * Gets uri for a given post
     * @param Sageweb_Abstract_Post $post
     * @return string url to post
     */
    public function postEditUrl($post)
    {
        $routeName = $post->entity->type;
        $url = $this->view->url(array('action' => 'edit', 'id' => $post->id), $routeName, true);
        return $url;
    }
}