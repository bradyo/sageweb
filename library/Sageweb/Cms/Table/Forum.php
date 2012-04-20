<?php

class Sageweb_Cms_Table_Forum
{
    public static function getForumChoices()
    {
        $query = Doctrine_Query::create()->from('Sageweb_Cms_Forum f');
        $forums = $query->execute();

        $choices = array();
        foreach ($forums as $forum) {
            $choices[$forum->id] = $forum->title;
        }
        return $choices;
    }

    public static function getName($forumId)
    {
        $query = Doctrine_Query::create()->from('Sageweb_Cms_Forum f');
        $query->where('f.id = ?', $forumId);
        $query->limit(1);
        $forum = $query->fetchOne();

        if ($forum) {
            return $forum->title;
        }
    }

    /**
     * @param integer $id
     * @return Sageweb_Cms_PostForum
     */
    public static function findOneBySlug($slug)
    {
        $query = Doctrine_Query::create()->from('Sageweb_Cms_Forum f');
        $query->where('f.slug = ?', $slug);
        $query->limit(1);
        return $query->fetchOne();
    }

    public static function findAll()
    {
        $query = Doctrine_Query::create()->from('Sageweb_Cms_Forum f');
        return $query->execute();
    }

    /**
     * @return Zend_Paginator
     */
    public static function getTopicsPager($forumId = null, $tag = null)
    {
        $query = Doctrine_Query::create()
            ->from('Sageweb_Cms_PostDiscussion p, p.forum f, p.entity e, p.author a, e.tags t');
        if ($forumId) {
            $query->andWhere('f.id = ?', $forumId);
        }
        if ($tag) {
            $query->andWhere('t.value = ?', $tag);
        }
        $adapter = new My_Paginator_Adapter_DoctrineQuery($query);
        $pager = new Zend_Paginator($adapter);
        return $pager;
    }

}
