<?php

class Sageweb_Cms_Table_Flag
{
    /**
     * @param integer $id
     * @return Sageweb_Cms_EntityFlag
     */
    public static function findOneById($id)
    {
        return Doctrine_Query::create()
            ->from('Sageweb_Cms_EntityFlag f')
            ->where('f.id = ?', $id)
            ->limit(1)
            ->fetchOne();
    }

    public static function findAll()
    {
        return Doctrine_Query::create()
            ->from('Sageweb_Cms_EntityFlag f')
            ->orderBy('f.createdAt DESC')
            ->execute();
    }
}
