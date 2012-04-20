<?php

class Sageweb_Cms_Table_Search
{
    public static function getSearchPager($queryString = null)
    {
        $hits = self::search($queryString);
        $adapter = new Zend_Paginator_Adapter_Array($hits);
        return new Zend_Paginator($adapter);
    }

    public static function search($queryString)
    {
        $index = Sageweb_Registry::getSiteIndex();
        try {
            if (empty($queryString)) {
                $query = Zend_Search_Lucene_Search_QueryParser::parse('entityId:[1 TO *]');
            } else {
                $query = Zend_Search_Lucene_Search_QueryParser::parse($queryString);
            }
        } catch (Zend_Search_Lucene_Exception $e) {
            return array();
        }
        $hits =  $index->find($query);
        return $hits;
    }

    public static function findNewest($count = 5) {
        $index = Sageweb_Registry::getSiteIndex();
        try {
            $query = Zend_Search_Lucene_Search_QueryParser::parse('entityId:[1 TO *]');
        } catch (Zend_Search_Lucene_Exception $e) {
            return array();
        }
        $allHits =  $index->find($query, 'createdAt', SORT_STRING, SORT_DESC);
        return array_slice($allHits, 0, $count);
    }
}
