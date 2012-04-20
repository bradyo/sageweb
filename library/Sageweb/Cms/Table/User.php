<?php

/**
 *
 */
class Sageweb_Cms_Table_User
{
    /**
     * Finds a user by identity (either e-mail or username).
     *
     * @param string $identity the username or e-mail fo the user to find
     * @return Sageweb_Model_Orm_User
     */
    public static function findByIdentity($identity)
    {
        $q = Doctrine_Query::create()->from('Sageweb_Cms_User u')
            ->where('u.id = ? OR u.username = ?', array($identity, $identity))
            ->limit(1);
        return $q->fetchOne();
    }

    public static function findByActivationKey($key)
    {
        $q = Doctrine_Query::create()->from('Sageweb_Cms_User u')
            ->where('u.activationKey = ?', $key)
            ->limit(1);
        return $q->fetchOne();
    }

    public static function findAll()
    {
        return Doctrine_Query::create()->from('Sageweb_Cms_User u')
                ->execute();
    }

    public static function getPager()
    {
        $query = Doctrine_Query::create()
            ->from('Sageweb_Cms_User u')
            ->where('u.status = ?', Sageweb_Cms_User::STATUS_ACTIVE);

        $adapter = new My_Paginator_Adapter_DoctrineQuery($query);
        $pager = new Zend_Paginator($adapter);
        return $pager;
    }

    /**
     * Finds a user by username
     * @param string $username the username
     * @return Sageweb_Model_Orm_User
     */
    public static function findOneByUsername($username)
    {
        $q = Doctrine_Query::create()->from('Sageweb_Cms_User u')
            ->where('u.username = ?', array($username))
            ->limit(1);
        return $q->fetchOne();
    }

    public static function findOneById($id)
    {
        $q = Doctrine_Query::create()->from('Sageweb_Cms_User u')
            ->where('u.id = ?', $id)
            ->limit(1);
        return $q->fetchOne();
    }

    public static function getUser($username)
    {
        $q = Doctrine_Core::getTable('Sageweb_Cms_User')->createQuery('u')
            ->leftJoin('u.profile p')
            ->where('u.username = ?', $username)
            ->limit(1);
        return $q->fetchOne();
    }

    public static function getDefaultUser()
    {
        $user = new Sageweb_Cms_User();
        $user->role = Sageweb_Cms_User::ROLE_GUEST;
        $user->username = 'anonymous';
        $user->profile = new Sageweb_Cms_Profile();
        return $user;
    }

    public static function getUsernames($q, $count = 10)
    {
        $query = Doctrine_Query::create()->from('Sageweb_Cms_User u')
            ->select('u.username')
            ->where('u.username LIKE ?', $q . '%')
            ->orderBy('u.username ASC')
            ->limit($count);
        $rows = $query->execute(array(), Doctrine::HYDRATE_ARRAY);

        $usernames = array();
        foreach ($rows as $row) {
            $usernames[] = $row['username'];
        }
        return $usernames;
    }

    public static function findByNewsletter($newsletter)
    {
        return Doctrine_Query::create()->from('Sageweb_Cms_User u')
            ->where('u.newsletter = ?', $newsletter)
            ->execute();
    }
}
