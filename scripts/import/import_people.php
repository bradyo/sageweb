<?php
require_once '../init.php';


ini_set('auto_detect_line_endings', true);
ini_set('max_execution_time', 0);

$viewingUser = Sageweb_Table_User::findOneByUsername('script');
Zend_Registry::set('scriptUser', $viewingUser);

$handle = fopen('input/people.csv', 'r');
while (($data = fgetcsv($handle)) !== FALSE) {
    print_r($data);
    importPerson($data);
}
fclose($handle);

function importPerson($rowData)
{
    $name = $rowData[0];
    $names = explode(',', $name);
    $lastName = trim($names[0]);
    $firstName = trim($names[1]);

    $data = array();
    $data['status'] = Sageweb_Abstract_Post::STATUS_PUBLIC;
    $data['firstName'] = $firstName;
    $data['lastName'] = $lastName;
    $data['personalUrl'] = $rowData[1];
    makePost(Sageweb_Entity::TYPE_PERSON, $data);
}

function makePost($type, $data)
{
    $user = Zend_Registry::get('scriptUser');
    $post = Sageweb_Table_Entity::createPost($type, $data);
    $revision = $user->createRevision($post->entity, $data);
    $user->acceptRevision($revision);
}
