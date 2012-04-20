<?php

/**
 * Doctrine ORM Base class for a file upload.
 * 
 * @property integer $id
 * @property string $filename
 * @property string $mimeType
 * @property integer $size
 * @property integer $userId
 * @property boolean $isTemporary
 * @property datetime $createdAt
 *
 * @author Brady Olsen <bradyo@uw.edu>
 */
class Sageweb_Cms_Upload extends Doctrine_Record
{
    public function setTableDefinition()
    {
        parent::setTableDefinition();
        $this->option('type', 'INNODB');
        $this->setTableName('upload');
        
        $this->hasColumn('id', 'integer', 4, array(
            'primary' => true,
            'autoincrement' => true,
        ));
        $this->hasColumn('filename', 'string');
        $this->hasColumn('mime_type as mimeType', 'string');
        $this->hasColumn('size', 'integer');
        $this->hasColumn('user_id as userId', 'integer');
        $this->hasColumn('is_temporary as isTemporary', 'integer');
        $this->hasColumn('created_at as createdAt', 'datetime');
    }

    public function getPublicFilename()
    {
        return $this->id . '-' . $this->filename;
    }

    public function getUri() {
        return $this->getPublicFilename();
    }

    public function isVideo() {
        return preg_match('/\.flv$/i', $this->filename);  
    }
}
