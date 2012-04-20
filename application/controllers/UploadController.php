<?php

class UploadController extends Zend_Controller_Action
{
    private function _checkPermission()
    {
        if (!Zend_Auth::getInstance()->hasIdentity()) {
            throw new Zend_Controller_Action_Exception('Page not found', 404);
        }
    }

    public function imageAction()
    {
        $this->_checkPermission();

        if ($this->_request->isPost()) {
            if ($this->_hasParam('submitUpload')) {
                $upload = Sageweb_Cms_Table_Upload::receiveUpload(true);
                $this->view->upload = $upload;
            }
            else { // insert submit
                $caption = $this->view->escape(trim($this->_getParam('imageCaption')));
                $class = $this->view->escape($this->_getParam('imageClass'));
                if (!empty($caption)) {
                    $html = '<div class="figure ' . $class . '">'
                        . '<div class="figureImage">'
                        . '<img src="' . $this->view->escape($this->_getParam('imageSrc')) . '"'
                        . ' title="' . $this->view->escape($this->_getParam('imageTitle')) . '"'
                        . ' alt="" />'
                        . '</div>'
                        . '<div class="figureCaption">' . $caption . '</div>'
                        . '</div>';
                } else {
                    $html = '<img src="' . $this->view->escape($this->_getParam('imageSrc')) . '"'
                        . ' title="' . $this->view->escape($this->_getParam('imageTitle')) . '"'
                        . ' class="' . $class . '"'
                        . ' alt="" />';
                }
                $this->view->html = $html;
            }
        }

        $this->_helper->layout->disableLayout();
    }

    public function fileAction()
    {
        $this->_checkPermission();

        if ($this->_request->isPost()) {
            if ($this->_hasParam('submitUpload')) {
                $upload = Sageweb_Cms_Table_Upload::receiveUpload(true);
                $this->view->upload = $upload;
            } else {
                $html = '<a href="' . $this->view->escape($this->_getParam('fileHref')) . '" '
                    . 'class="download">'
                    . $this->view->escape($this->_getParam('fileTitle'))
                    . '</a>';
                $this->view->html = $html;
            }
        }

        $this->_helper->layout->disableLayout();
    }

    /**
     * Delivers the file through the browser for the files content type.
     */
    public function showAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        $filename = $this->_getParam('filename');
        $units = explode('-', $filename);
        $id = intval($units[0]);

        $upload = Sageweb_Cms_Table_Upload::findOneById($id);
        if (!$upload) {
            throw new Zend_Controller_Exception('File not found.', 404);
        }
        $path = DATA_PATH . '/uploads/' . $upload->getPublicFilename();

        $response = $this->getResponse();
        $response->clearAllHeaders();
        $response->setHeader('Content-Type', $upload->mimeType, true);
        $response->setHeader('Content-Length', $upload->size, true);
        $response->setBody(file_get_contents($path));
        $response->sendResponse();
        exit;
    }

    /**
     * Forces download of the file with the orignal filename.
     */
    public function downloadAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        // required for IE, otherwise Content-disposition is ignored
        if(ini_get('zlib.output_compression')) {
            ini_set('zlib.output_compression', 'Off');
        }

        $filename = basename($this->_getParam('filename'));
        $extension = strtolower(substr(strrchr($filename, "."), 1));
        $path = DATA_PATH . '/uploads' . '/' . $filename;
        if (!file_exists($path)) {
            throw new Zend_Controller_Exception('File not found.', 404);
        }

        // set content type
        $contentType = "application/octet-stream";
        $mapping = array(
            'pdf' => 'application/pdf',
            'zip' => 'application/zip',
            'doc' => 'application/msword',
            'xls' => 'application/vnd.ms-excel',
            'ppt' => 'application/vnd.ms-powerpoint',
            'gif' => 'image/gif',
            'png' => 'image/png',
            'jpeg' => 'image/jpg',
            'jpg' => 'image/jpg',
        );
        if (isset($mapping[$extension])) {
            $contentType = $mapping[$extension];
        }

        header("Pragma: public"); // required
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: private", false); // required for certain browsers
        header("Content-Type: $contentType");
        header("Content-Disposition: attachment; filename=\"".basename($path)."\";" );
        header("Content-Transfer-Encoding: binary");
        header("Content-Length: ".filesize($path));
        readfile($path);
        die();
    }
}
