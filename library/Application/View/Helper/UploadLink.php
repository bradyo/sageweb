<?php

class Application_View_Helper_UploadLink extends Zend_View_Helper_Abstract
{
    public function uploadLink($id, $target = null)
    {
        $upload = Sageweb_Table_Upload::findOneById($id);
        if ($upload) {
            $url = $this->view->url(array('filename' => $upload->getUri()), 'uploads', true);
            $label = $this->view->escape($upload->filename);
            $html = '<a href="' . $url . '"';
            if ($target) {
                $html .= ' target="' . $target . '"';
            }
            $html .= '>' . $label . '</a>';
            return $html;
        }
    }
}