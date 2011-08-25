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
    $data['type'] = 'research';
    $data['name'] = $rowData[0];
    $data['url'] = $rowData[1];
    $data['body'] = $rowData[2];
    makePost(Sageweb_Entity::TYPE_LAB, $data);
}

function makePost($type, $data)
{
    $user = Zend_Registry::get('scriptUser');
    $post = Sageweb_Table_Entity::createPost($type, $data);
    $revision = $user->createRevision($post->entity, $data);
    $user->acceptRevision($revision);
}
