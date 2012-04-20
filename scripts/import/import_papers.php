<?php
require_once '../init.php';

ini_set('auto_detect_line_endings', true);
ini_set('max_execution_time', 0);

$user = Sageweb_Table_User::findOneByUsername('jpnitya');

$handle = fopen('input/classic-papers-joao.csv', 'r');
while (($rowData = fgetcsv($handle)) !== FALSE) {
    print_r($rowData);

    $postData = array();
    $postData['status'] = Sageweb_Abstract_Post::STATUS_PUBLIC;
    $postData['type'] = 'classical';

		$pubmedId = trim($rowData[0]);
		if (!empty($pubmedId)) {
    	$pubmedData = Application_Service_PubMed::getCitationData($pubmedId);
      $postData['pubmedId'] = $pubmedId;
      $postData['url'] = $pubmedData['url'];
			$postData['year'] = $pubmedData['year'];
      $postData['authors'] = $pubmedData['authors'];
			$postData['title'] = $pubmedData['title'];
			$postData['source'] = $pubmedData['source'];
			$postData['publishDate'] = $pubmedData['publishDate'];
			$postData['abstract'] = $pubmedData['abstract'];
		} else {
			$postData['year'] = trim($rowData[1]);
      $postData['authors'] = trim($rowData[2]);
			$postData['title'] = trim($rowData[3]);
			$postData['source'] =  trim($rowData[4]);
			$postData['publishDate'] = trim($rowData[1]);
		}
		$postData['summary'] = trim($rowData[5]);

print_r($postData);

//    $post = Sageweb_Table_Entity::createPost(Sageweb_Entity::TYPE_PAPER, $postData);
//    $revision = $user->createRevision($post->entity, $postData);
//    $user->acceptRevision($revision);
}
fclose($handle);


