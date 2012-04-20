<?php

class IndexController extends Zend_Controller_Action
{
public function indexAction()
    {
    }


    public function updateAction()
    {
        $db = Sageweb_Registry::getDb();
        $stmt = $db->prepare('SELECT id, pubmed_id FROM post_paper');
        $stmt->execute();

        $rows = $stmt->fetchAll(Zend_Db::FETCH_ASSOC);
        $updateStmt = $db->prepare('UPDATE post_paper SET publish_date = ? WHERE id = ?');
        foreach ($rows as $row) {
            
            $pubmedId = $row['pubmed_id'];
            $service = new Application_Service_PubMed();
            $data = $service->getCitationData($pubmedId);

            if (isset($data['publishDate'])) {
                $id = $row['id'];
                $publishDate = $data['publishDate'];
                $updateStmt->execute(array($publishDate, $id));
            }
        }

        die();
    }

    public function deniedAction()
    {
    }

    public function addAction()
    {
        
    }

    public function sendAction()
    {
        Sageweb_Cms_Newsletter::sendEmails('weekly');
        die();
    }

    public function rebuildAction()
    {
        ini_set('max_execution_time', '120');

        $index = Zend_Search_Lucene::create(DATA_PATH . '/lucene/site-index');
        Sageweb_Cms_Table_Content::rebuildIndex();
        Sageweb_Cms_Table_Discussion::rebuildIndex();
        Sageweb_Cms_Table_Event::rebuildIndex();
        Sageweb_Cms_Table_Paper::rebuildIndex();
        Sageweb_Cms_Table_Person::rebuildIndex();
        Sageweb_Cms_Table_Lab::rebuildIndex();
        $index->commit();
        $index->optimize();
        echo "done";
        die();
    }

}

