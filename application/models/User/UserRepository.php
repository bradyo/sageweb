<?php

class Application_Model_User_UserRepository {
    
    public function getAll(Application_Model_User_UserFilter $filter = null, 
            $offset = null, $limit = null) {
        $query = $this->getQuery($filter);
        if ($offset !== null) {
            $query->offset($offset);
        }
        if ($limit !== null) {
            $query->limit($limit);
        }
        return $query->execute();
    }
    
    /**
     * @param integer $id
     * @return Application_Model_User_User
     */
    public function getOneById($id) {
        $query = $this->getQuery();
        $query->where('user.id = ?', $id);
        $query->limit(1);
        return $query->fetchOne();
    }
    
    /**
     * @param Application_Model_User_UserFilter $filter
     * @return Doctrine_Query
     */
    private function getQuery(Application_Model_User_UserFilter $filter = null) {
        $query = Doctrine_Query::create()->from('Application_Model_User_User user');
        
        if ($filter !== null) {
            // todo: add filters
        }
        
        return $query;
    }
}
