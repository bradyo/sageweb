<?php

class TwitterStreamController extends Zend_Controller_Action
{
    public function indexAction()
    {
        $cache = Application_Registry::getCache();

        $cacheKey = 'twitterStream_list_usernames';
        $usernames = $cache->load($cacheKey);
        if (!$usernames) {
            $db = Application_Registry::getDb();
            $q = $db->prepare('
                SELECT u.username, p.twitter_id FROM user u
                LEFT JOIN user_profile p ON p.user_id = u.id
                WHERE p.twitter_id IS NOT NULL AND p.in_twitter_stream = 1
                ');
            $q->execute(array());
            $rows = $q->fetchAll(PDO::FETCH_ASSOC);

            $usernames = array();
            foreach ($rows as $row) {
                $username = $row['username'];
                $twitterId = $row['twitter_id'];
                if (!empty($twitterId)) {
                    $usernames[$username] = $twitterId;
                }
            }
            $cache->save($usernames, $cacheKey);
        }
        $this->view->usernames = $usernames;

        $cacheKey = 'twitterStream_list_items';
        $items = $cache->load($cacheKey);
        if (!$items) {
            $service  = new Application_Service_TwitterService();
            $findLocal = false;

            $items = array();
            if (count($usernames) > 0) {
                $qParts = array();
                $twitterIds = array_unique(array_values($usernames));
                foreach ($twitterIds as $twitterId) {
                    $qParts[] = 'from:' . $twitterId;
                }
                $q = join(' OR ', $qParts);

                $params = array();
                $params['q'] = $q;

                $findLocal = true;
                if ($findLocal) {
                    $user = Application_Registry::getUser();
                    $location = 'Seattle, WA';
                    $params['near'] = $location;
                }

                $response = $service->search($params);
                $items = $response['results'];
            }
            $cache->save($items, $cacheKey);
        }
        $this->view->items = $items;
    }
}
