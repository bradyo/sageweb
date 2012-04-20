<?php

/**
 * Base class for Entity flag.
 *
 * @property integer $id
 * @property integer $entityId
 * @property integer $creatorId
 * @property string $type
 * @property string $comment
 *
 * @author Brady Olsen <bradyo@uw.edu>
 */

class Sageweb_Cms_EntityFlag extends Doctrine_Record
{
    const TYPE_SPAM = 'spam';
    const TYPE_INAPPROPRIATE = 'inappropriate';
    const TYPE_MIS_CATEGORIZED = 'mis-categorized';
    const TYPE_BROKEN_LINK = 'broken-link';
    const TYPE_PSEUDOSCIENCE = 'pseudoscience';
    const TYPE_OTHER = 'other';

    public function setTableDefinition()
    {
        $this->setTableName('entity_flag');
        $this->option('type', 'INNODB');

        $this->hasColumn('id', 'integer', 4, array(
            'primary' => true,
            'autoincrement' => true,
        ));
        $this->hasColumn('entity_id as entityId', 'integer');
        $this->hasColumn('creator_id as creatorId', 'integer');
        $this->hasColumn('created_at as createdAt', 'datetime');
        $this->hasColumn('type', 'string', 32);
        $this->hasColumn('comment', 'clob');
    }

    public static function getTypeChoices()
    {
        return array(
            self::TYPE_SPAM => "Spam",
            self::TYPE_INAPPROPRIATE => "Inappropriate",
            self::TYPE_MIS_CATEGORIZED => "Mis-categorized",
            self::TYPE_BROKEN_LINK => "Broken Link",
            self::TYPE_PSEUDOSCIENCE => "Pseudoscience",
            self::TYPE_OTHER => "Other",
        );
    }

    public static function getTypeLabel($type)
    {
        $choices = self::getTypeChoices();
        return $choices[$type];
    }
}