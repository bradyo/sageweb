<?php
require_once 'init.php';

// rebuild site index
$index = Zend_Search_Lucene::create(DATA_PATH . '/lucene/site-index');

Sageweb_Table_Content::rebuildIndex();
Sageweb_Table_Discussion::rebuildIndex();
Sageweb_Table_Event::rebuildIndex();
Sageweb_Table_Paper::rebuildIndex();
Sageweb_Table_Person::rebuildIndex();
Sageweb_Table_Lab::rebuildIndex();

// save and optimize index
$index->commit();
$index->optimize();