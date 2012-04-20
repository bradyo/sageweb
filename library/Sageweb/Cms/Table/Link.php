<?php

class Sageweb_Cms_Table_Link
{
    const ORDER_NEWEST = 'newest';

    private static function _getRootQuery()
    {
        return Doctrine_Query::create()
            ->from('Sageweb_Cms_PostLink p, p.entity e, p.author a, p.categories c, p.tags t');
    }

    /**
     * @return Sageweb_Cms_PostLink
     */
    public static function findOneById($id)
    {
        $query = self::_getRootQuery();
        $query->where('p.id = ?', $id);
        $query->limit(1);
        return $query->fetchOne();
    }

    /**
     * Gets the article with the given id.
     * @param integer $id
     * @return Sageweb_Cms_PostArticle
     */
    public static function findOneByEntityId($entityId)
    {
        $query = self::_getRootQuery();
        $query->where('p.entityId = ?', $entityId);
        $query->limit(1);
        return $query->fetchOne();
    }

    public static function findPublic()
    {
        return self::_getRootQuery()
            ->where('p.status = ?', Sageweb_Cms_PostLink::STATUS_PUBLIC)
            ->execute();
    }

    /**
     * Gets a pager for newests items of a given user.
     * @param string $username
     * @param string $order
     * @return Zend_Paginator
     */
    public static function getPagerByUsername($username, $order = self::ORDER_NEWEST)
    {
        $query = self::_getRootQuery();
        $query->where('a.username = ?', $username);
        
        switch ($order) {
            case ORDER_NEWEST:
            default:
                $query->orderBy('p.createdAt DESC');
                break;
        }
        
        $pagerAdapter = new My_Paginator_Adapter_DoctrineQuery($query);
        return new Zend_Paginator($pagerAdapter);;
    }
}
