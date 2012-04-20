<?php

class Sageweb_Cms_Table_Person
{
    private static function _getRootQuery()
    {
        return Doctrine_Query::create()
            ->from('Sageweb_Cms_PostPerson p, p.entity e, p.author a');
    }

    /**
     * @return Sageweb_Cms_PostPerson
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
     * @return Sageweb_Cms_PostPerson
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
            SELECT SUBSTR(last_name, 1, 1) AS letter, COUNT(*) AS letter_count
            FROM post_person
            WHERE status = ?
            GROUP BY SUBSTR(last_name, 1, 1);
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
        $index = Sageweb_Registry::getPersonIndex();

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

        $hits = $index->find($query, 'lastName', SORT_REGULAR, SORT_ASC);
        return $hits;
    }

    public static function rebuildIndex()
    {
        // re-create index directory
        $index = Zend_Search_Lucene::create(DATA_PATH . '/lucene/person-index');
        $persons = self::_getRootQuery()
            ->where('p.status = ?', Sageweb_Cms_PostPerson::STATUS_PUBLIC)
            ->execute();
        foreach ($persons as $person) {
            $person->updateIndex();
            $person->updateSiteIndex();
        }
        $index->commit();
        $index->optimize();
    }
}
