<?php

class Sageweb_Cms_Table_Upload
{
    /**
     * Gets the upload with the given id.
     * @param integer $id
     * @return Sageweb_Cms_Upload
     */
    public static function findOneById($id)
    {
        $query = Doctrine_Query::create()->from('Sageweb_Cms_Upload u');
        $query->where('u.id = ?', $id);
        $query->limit(1);
        return $query->fetchOne();
    }

    /**
     * Pulls the file from HTTP transfer and inserts into uploads table. Then
     * moves the file to a permanent location in the data directory.
     * @return integer upload id
     */
    public static function receiveUpload($isTemporary = false)
    {
        $uploader = new Zend_File_Transfer_Adapter_Http();
        $files = $uploader->getFileInfo('file');
        $file = $files['file'];
        if (!$file) {
            return;
        }

        // insert file meta data into uploads table
        $upload = new Sageweb_Cms_Upload();
        $upload->filename = $file['name'];
        $upload->mimeType = $file['type'];
        $upload->size = $file['size'];
        $upload->userId = Sageweb_Registry::getUser()->id;
        $upload->isTemporary = $isTemporary;
        $upload->createdAt = date('Y-m-d H:i:s');
        $upload->save();

        // append upload id to filename and copy to uploads directory
        $dest = DATA_PATH . '/uploads/' . $upload->getPublicFilename();
        $uploader->addFilter('Rename', array('target' => $dest, 'overwrite' => true));
        $uploader->receive();

        return $upload;
    }
}
