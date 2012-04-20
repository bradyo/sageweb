<?php

class Sageweb_Cms_Table_Lab
{
    private static $types = array(
        'research' => 'Research',
        'company' => 'Company',
        'other' => 'Other',
    );

    private static function _getRootQuery()
    {
        return Doctrine_Query::create()
            ->from('Sageweb_Cms_PostLab p, p.entity e, p.author a');
    }

    /**
     * @return Sageweb_Cms_PostLab
     */
    public static function findOneById($id)
    {
        $query = self::_getRootQuery();
        $query->where('p.id = ?', $id);
        $query->limit(1);
        return $query->fetchOne();
    }

    /**
     * @param integer $id
     * @return Sageweb_Cms_PostLab
     */
    public static function findOneByEntityId($entityId)
    {
        $query = self::_getRootQuery();
        $query->where('p.entityId = ?', $entityId);
        $query->limit(1);
        return $query->fetchOne();
    }

    
    public static function getLetterCounts()
    {
        $db = Sageweb_Registry::getDb();
        $stmt = $db->prepare('
            SELECT SUBSTR(name, 1, 1) AS letter, COUNT(*) AS letter_count
            FROM post_lab
            WHERE status = ?
            GROUP BY SUBSTR(name, 1, 1);
        ');
        $stmt->execute(array(Sageweb_Cms_Abstract_Post::STATUS_PUBLIC));

        $letterCounts = array();
        $rows = $stmt->fetchAll();
        foreach ($rows as $row) {
            $letter = strtolower($row['letter']);
            $count = $row['letter_count'];
            $letterCounts[$letter] = $count;
        }
        return $letterCounts;
    }


    /**
     *
     * @param string $queryString
     * @param string $sort
     * @return Zend_Paginator
     */
    public static function getSearchPager($queryString = null, $letter = null)
    {
        $hits = self::getSearchHits($queryString, $letter);
        $adapter = new Zend_Paginator_Adapter_Array($hits);
        return new Zend_Paginator($adapter);
    }

    public static function getSearchHits($queryString, $letter = null)
    {
        $index = Sageweb_Registry::getLabIndex();

        if ($letter) {
            $queryString .= ' letter:' . $letter;
        }

        try {
            if (empty($queryString)) {
                // return everything
                $query = Zend_Search_Lucene_Search_QueryParser::parse('entityId:[1 TO *]');
            } else {
                $query = Zend_Search_Lucene_Search_QueryParser::parse($queryString);
            }
        } catch (Zend_Search_Lucene_Exception $e) {
            return array();
        }

        $hits = $index->find($query, 'name', SORT_REGULAR, SORT_ASC);
        return $hits;
    }

    public static function rebuildIndex()
    {
        // re-create index directory
        $index = Zend_Search_Lucene::create(DATA_PATH . '/lucene/lab-index');
        $labs = self::_getRootQuery()
            ->where('p.status = ?', Sageweb_Cms_PostPerson::STATUS_PUBLIC)
            ->execute();
        foreach ($labs as $lab) {
            $lab->updateIndex();
            $lab->updateSiteIndex();
        }
        $index->commit();
        $index->optimize();
    }
}
