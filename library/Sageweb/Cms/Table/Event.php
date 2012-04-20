<?php

class Sageweb_Cms_Table_Event
{
    const ORDER_NEWEST = 'newest';
    const ORDER_POPULAR = 'popular';
    const ORDER_TOP = 'top';

    private static function _getRootQuery()
    {
        return Doctrine_Query::create()
            ->from('Sageweb_Cms_PostEvent p, p.entity e, p.author a');
    }

    /**
     * @return Sageweb_Cms_PostEvent
     */
    public static function findOneById($id)
    {
        $query = self::_getRootQuery();
        $query->where('p.id = ?', $id);
        $query->limit(1);
        return $query->fetchOne();
    }

    public static function getQuery($types = null, $order = null)
    {
        $query = self::_getRootQuery();

        if ($types !== null) {           
            $query->whereIn('p.type', $types);
        }
        switch ($order) {
            case ORDER_NEWEST:
                $query->orderBy('e.rating DESC');
                break;
            case ORDER_TOP:
                $query->orderBy('p.createdAt DESC');
                break;
            case ORDER_POPULAR:
                $query->orderBy('e.viewCount DESC');
                break;
        }
        return $query;
    }

    public static function findUpcoming($count = 10)
    {
        $query = self::_getRootQuery();
        $query->andWhere('p.status = ?', Sageweb_Cms_Abstract_Post::STATUS_PUBLIC);
        $query->andWhere('p.startsAt > ?', date('Y-m-d'));
        $query->orderBy('p.startsAt');
        $query->limit($count);
        return $query->execute();
    }

    /**
     * Gets the article with the given id.
     * @param integer $id
     * @return Sageweb_Cms_PostEvent
     */
    public static function findOneByEntityId($entityId)
    {
        $query = self::_getRootQuery();
        $query->andWhere('p.entityId = ?', $entityId);
        $query->limit(1);
        return $query->fetchOne();
    }

    public static function findByType($types = array())
    {
        $query = self::_getRootQuery();
        $query->andWhereIn('p.type', $types);
        return $query->execute();
    }

    /**
     * Gets a pager for events
     * @return Zend_Paginator
     */
    public static function getPager($display = null, $types = null)
    {
        $query = self::getQuery($types, $display);
        $query->andWhere('p.status = ?', Sageweb_Cms_Abstract_Post::STATUS_PUBLIC);
        $pagerAdapter = new My_Paginator_Adapter_DoctrineQuery($query);
        return new Zend_Paginator($pagerAdapter);;
    }

    /**
     * @return Zend_Paginator
     */
    public static function getUpcomingPager($types = null)
    {
        $query = self::getQuery($types, $display);
        $query->andWhere('p.status = ?', Sageweb_Cms_Abstract_Post::STATUS_PUBLIC);
        $query->andWhere('p.startsAt > ?', date('Y-m-d'));
        $query->orderBy('p.startsAt');
        $pagerAdapter = new My_Paginator_Adapter_DoctrineQuery($query);
        return new Zend_Paginator($pagerAdapter);;
    }

    /**
     * @return Zend_Paginator
     */
    public static function getPastPager($types = null)
    {
        $query = self::getQuery($types, $display);
        $query->andWhere('p.status = ?', Sageweb_Cms_Abstract_Post::STATUS_PUBLIC);
        $query->andWhere('p.startsAt < ?', date('Y-m-d'));
        $query->orderBy('p.startsAt DESC');
        $pagerAdapter = new My_Paginator_Adapter_DoctrineQuery($query);
        return new Zend_Paginator($pagerAdapter);;
    }

    /**
     * Gets a pager for newests items of a given user.
     * @param string $username
     * @param string $order
     * @return Zend_Paginator
     */
    public static function getPagerByUsername($username, $order = self::ORDER_NEWEST)
    {
        $query = self::getQuery(null, $order);
        $query->where('a.username = ?', $username);
        $pagerAdapter = new My_Paginator_Adapter_DoctrineQuery($query);
        return new Zend_Paginator($pagerAdapter);;
    }

    public static function findSince($sinceDate, $count = null)
    {
        $query = Doctrine_Query::create()->from('Sageweb_Cms_PostEvent p')
            ->where('p.createdAt > ?', $sinceDate)
            ->andWhere('p.status = ?', Sageweb_Cms_Abstract_Content::STATUS_PUBLIC)
            ->orderBy('p.createdAt DESC');
        if ($count) {
            $query->limit($count);
        }
        return $query->execute();
    }

    public static function rebuildIndex()
    {
        $index = Zend_Search_Lucene::create(DATA_PATH . '/lucene/event-index');
        Zend_Registry::set('eventIndex', $index);
        $posts = self::_getRootQuery()
            ->where('p.status = ?', Sageweb_Cms_PostEvent::STATUS_PUBLIC)
            ->execute();
        foreach ($posts as $post) {
            $post->updateIndex();
            $post->updateSiteIndex();
        }
        $index->commit();
        $index->optimize();
    }

}
