<?php

/**
 * Provides access to posts of types.
 */
class Sageweb_Cms_Table_Revision
{
    public static function findOneByEntityId($entityId, $revisionId)
    {
        $q = Doctrine_Query::create()
            ->from('Sageweb_Cms_EntityRevision r, r.entity e')
            ->where('r.entityId = ?', $entityId)
            ->andWhere('r.id = ?', $revisionId)
            ->limit(1);
        return $q->fetchOne();
    }

    public static function findOneByEntity($entityId)
    {
        $q = Doctrine_Query::create()
            ->from('Sageweb_Cms_EntityRevision r, r.entity e')
            ->where('r.entityId = ?', $entityId)
            ->limit(1);
        return $q->fetchOne();
    }

    public static function findByEntityId($entityId)
    {
        $q = Doctrine_Query::create()
            ->from('Sageweb_Cms_EntityRevision r, r.entity e')
            ->orderBy('r.createdAt DESC')
            ->where('r.entityId = ?', $entityId);
        return $q->execute();
    }

    public static function findPendingById($entityId)
    {
        return Doctrine_Query::create()
            ->from('Sageweb_Cms_EntityRevision r, r.entity e')
            ->orderBy('r.createdAt DESC')
            ->where('r.entityId = ?', $entityId)
            ->andWhere('r.status = ?', Sageweb_Cms_EntityRevision::STATUS_PENDING)
            ->execute();
    }
}
