<?php
require_once '../init.php';

ini_set('max_execution_time', 0);

$viewingUser = Sageweb_Table_User::findOneByUsername('script');
Zend_Registry::set('scriptUser', $viewingUser);

$handle = fopen('input/labs.csv', 'r');
while (($data = fgetcsv($handle)) !== FALSE) {
    print_r($data);
    importLab($data);
}
fclose($handle);

function importLab($rowData)
{
    $data = array();
    $data['status'] = Sageweb_Abstract_Post::STATUS_PUBLIC;
    $data['type'] = 'classical';

		$pubmedId = trim($rowData[0]);
		if (!empty($pubmedId)) {
    	$pubmedData = Application_Service_PubMed::getCitationData($pubmedId);
      if (count($pubmedData) > 0) {
        $data['pubmedId'] = $pubmedId;
        $data['url'] = $pubmedData['url'];
				$data['year'] = $pubmedData['year'];
	      $data['authors'] = $pubmedData['authors'];
				$data['title'] = $pubmedData['title'];
				$data['source'] = $pubmedData['source'];
				$data['publishDate'] = $pubmedData['publishDate'];
				$data['abstract'] = $pubmedData['abstract'];
      }
		} else {
				$data['year'] = trim($rowData[1]);
	      $data['authors'] = trim($rowData[2]);
				$data['title'] = trim($rowData[3]);
				$data['source'] =  trim($rowData[4]);
				$data['publishDate'] = $data['year'];
		}
		$data['summary'] = trim($rowData[5]);

        $this->hasColumn('abstract', 'clob');
        $this->hasColumn('summary', 'clob');

    makePost(Sageweb_Entity::TYPE_LAB, $data);
}

function makePost($type, $data)
{
    $user = Zend_Registry::get('scriptUser');
    $post = Sageweb_Table_Entity::createPost($type, $data);
    $revision = $user->createRevision($post->entity, $data);
    $user->acceptRevision($revision);
}
