<?php

/**
 * Provides access to posts of types.
 */
class Sageweb_Cms_Table_Content
{
    const ORDER_NEWEST = 'newest';
    const ORDER_POPULAR = 'most-viewed';
    const ORDER_TOP = 'top-rated';

    private static $defaultCategories = array(
        'database' => 'Database',
        'document' => 'Document',
        'funding' => 'Funding',
        'news' => 'News',
        'software' => 'Software',
        'tutorial' => 'Tutorial',
        'presentation' => 'Presentation',
        'protocol' => 'Protocol',
    );

    private static $mediaCategories = array(
        'audio' => 'Audio',
        'image' => 'Image',
        'video' => 'Video',
    );

    private static $linkCategories = array(
        'blogs' => 'Blogs',
        'biotech-and-pharma' => 'Biotech & Pharma',
        'information-and-resources' => 'Info & Resources',
        'institutes' => 'Institutes',
        'programs' => 'Programs',
        'societies-and-foundations' => 'Societies & Foundations',
        'websites' => 'Websites',
    );

    public static function getDefaultCategoryOptions()
    {
        return self::$defaultCategories;
    }

    public static function getMediaCategoryOptions()
    {
        return self::$mediaCategories;
    }

    public static function getLinkCategoryOptions()
    {
        return self::$linkCategories;
    }

    public static function getCategoryLabel($category)
    {
        if ($category == 'all') {
            return 'All';
        } elseif ($category == 'link') {
            return 'Links';
        } elseif ($category == 'media') {
            return 'Media';
        }
        $categories = array_merge(
            self::$defaultCategories,
            self::$mediaCategories,
            self::$linkCategories
        );
        return $categories[$category];
    }

    public static function findNewest($category = null, $tag = null, $count = 10)
    {
        $q = self::getQuery($category, $tag, self::ORDER_NEWEST);
        $q->limit($count);
        return $q->execute(array(), DOCTRINE::HYDRATE_RECORD);
    }

    public static function findTop($category = null, $tag = null, $count = 10)
    {
        $q = self::getQuery($category, $tag, self::ORDER_TOP);
        $q->limit($count);
        return $q->execute(array(), DOCTRINE::HYDRATE_RECORD);
    }

    public static function findPopular($category = null, $tag = null, $count = 10)
    {
        $q = self::getQuery($category, $tag, self::ORDER_TOP);
        $q->limit($count);
        return $q->execute(array(), DOCTRINE::HYDRATE_RECORD);
    }

    public static function getQuery($category = null, $tag = null, $order = null)
    {
        $q = Doctrine_Query::create()
            ->from('Sageweb_Cms_PostContent p, p.entity e, p.author a, e.categories c, e.tags t');
        $q->where('p.status = ?', Sageweb_Cms_PostContent::STATUS_PUBLIC);

        if ($category != null) {
            if ($category == 'link') {
                $categories = array_keys(self::$linkCategories);
                $q->whereIn('c.value', $categories);
            } else if ($category == 'media') {
                $categories = array_keys(self::$mediaCategories);
                $q->whereIn('c.value', $categories);
            } else if ($category != 'all') {
                $q->where('c.value = ?', $category);
            }
        }
        
        if ($tag != null) {
            $q->where('t.value = ?', $tag);
        }

        if ($order == self::ORDER_TOP) {
            $q->orderBy('e.rating DESC');
        } elseif ($order == self::ORDER_POPULAR) {
            $q->orderBy('e.viewCount DESC');
        } else {
            $q->orderBy('p.createdAt DESC');
        }
        return $q;
    }

    /**
     * @return Zend_Paginator
     */
    public static function getPager($sort = null, $category = null, $tag = null)
    {
        $query = self::getQuery($category, $tag, $sort);
        $adapter = new My_Paginator_Adapter_DoctrineQuery($query);
        $pager = new Zend_Paginator($adapter);
        return $pager;
    }

    /**
     *
     * @param <type> $entityId
     * @return Sageweb_Cms_PostContent
     */
    public static function findOneByEntityId($entityId)
    {
        return Doctrine_Query::create()->from('Sageweb_Cms_PostContent c')
            ->where('c.entityId = ?', $entityId)
            ->limit(1)
            ->fetchOne();
    }

    public static function findSince($sinceDate, $count = null)
    {
        $query = Doctrine_Query::create()->from('Sageweb_Cms_PostContent c')
            ->where('c.createdAt > ?', $sinceDate)
            ->andWhere('c.status = ?', Sageweb_Cms_Abstract_Content::STATUS_PUBLIC)
            ->orderBy('c.createdAt DESC');
        if ($count) {
            $query->limit($count);
        }
        return $query->execute();
    }

    public static function findPublic()
    {
        return Doctrine_Query::create()->from('Sageweb_Cms_PostContent p')
            ->where('p.status = ?', Sageweb_Cms_Abstract_PostContent::STATUS_PUBLIC)
            ->execute();
    }

    
    public static function getSearchPager($queryString = null, $sort = null)
    {
        $hits = self::search($queryString, $sort);
        $adapter = new Zend_Paginator_Adapter_Array($hits);
        return new Zend_Paginator($adapter);
    }

    public static function search($queryString, $sort = null)
    {
        $index = Sageweb_Registry::getContentIndex();
        try {
            if (empty($queryString)) {
                $query = Zend_Search_Lucene_Search_QueryParser::parse('entityId:[1 TO *]');
            } else {
                $query = Zend_Search_Lucene_Search_QueryParser::parse($queryString);
            }
        } catch (Zend_Search_Lucene_Exception $e) {
            return array();
        }

        switch ($sort) {
            case 'top-rated':
                $hits = $index->find($query, 'rating', SORT_NUMERIC, SORT_DESC);
                break;
            case 'most-viewed':
                $hits = $index->find($query, 'viewsCount', SORT_NUMERIC, SORT_DESC);
                break;
            case 'most-discussed':
                $hits = $index->find($query, 'commentsCount', SORT_NUMERIC, SORT_DESC);
                break;
            case 'neweset':
            default:
                $hits =  $index->find($query, 'createdAt', SORT_STRING, SORT_DESC);
                break;
        }
        return $hits;
    }

    public static function rebuildIndex()
    {
        $index = Zend_Search_Lucene::create(DATA_PATH . '/lucene/content-index');

        $articles = Sageweb_Cms_Table_Article::findPublic();
        foreach ($articles as $post) {
            $post->updateIndex();
            $post->updateSiteIndex();
        }

        $files = Sageweb_Cms_Table_File::findPublic();
        foreach ($files as $post) {
            $post->updateIndex();
            $post->updateSiteIndex();
        }

        $links = Sageweb_Cms_Table_Link::findPublic();
        foreach ($links as $post) {
            $post->updateIndex();
            $post->updateSiteIndex();
        }
        
        $index->commit();
        $index->optimize();
    }
}
