<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected function _initHtmlPurifier()
    {
        require_once 'HTMLPurifier/Bootstrap.php';
        spl_autoload_register(array('HTMLPurifier_Bootstrap', 'autoload'));
    }

    protected function _initViewSettings()
    {
        $this->bootstrap('view');
        $view = $this->getResource('view');

        $view->setEncoding('UTF-8');
        $view->doctype('XHTML1_STRICT');

        $view->headMeta()->appendHttpEquiv('Content-Type', 'text/html; charset=UTF-8');
        $view->headMeta()->appendHttpEquiv('Content-Language', 'en-US');

        $view->headScript()->appendFile('/library/jquery-1.4.4.min.js');
        $view->headScript()->appendFile('/library/jquery.tools.min.js');

        $view->headLink()->appendStylesheet('/css/global.css');
        $view->headScript()->appendFile('/js/global.js');

        $view->headLink()->appendStylesheet('/library/jquery-ui/css/custom/jquery-ui-1.8.7.custom.css');
        $view->headScript()->appendFile('/library/jquery-ui/js/jquery-ui-1.8.7.custom.min.js');

        $view->headScript()->appendFile('/library/fancybox/jquery.fancybox-1.3.2.pack.js');
        $view->headLink()->appendStylesheet('/library/fancybox/jquery.fancybox-1.3.2.css', 'screen');

        $view->headTitle('Sageweb');
        $view->headTitle()->setSeparator(' - ');
    }

    protected function _initPlugins()
    {
        $this->bootstrap('frontController');
        $front = Zend_Controller_Front::getInstance();
        $front->registerPlugin(new Sageweb_Plugin_Access());
    }

    protected function _initCache()
    {
        $frontend = array(
            'lifetime' => 7200,
            'automatic_serialization' => true,
        );
        $backend = array(
            'cache_dir' => APPLICATION_PATH . '/../data/cache/application'
        );
        $cache = Zend_Cache::factory('core', 'File', $frontend, $backend);
        Zend_Registry::set('cache', $cache);
    }

    protected function _initDatabases()
    {
        $this->bootstrap('db');
        $db = $this->getPluginResource('db');
        $dbAdapter = $db->getDbAdapter();
        Zend_Registry::set('db', $dbAdapter->getConnection());

        // set up adapter for Zend_Db_Table class
        Zend_Db_Table_Abstract::setDefaultAdapter($dbAdapter);

				// set up doctrine
				$manager = Doctrine_Manager::getInstance();
        $manager->setAttribute(Doctrine::ATTR_AUTO_ACCESSOR_OVERRIDE, true);
        $manager->setAttribute(Doctrine::ATTR_MODEL_LOADING, Doctrine::MODEL_LOADING_CONSERVATIVE);
        $manager->setCharset('utf8');
        $manager->setCollate('utf8_general_ci');

        Doctrine_Manager::connection($dbAdapter->getConnection());
        Doctrine::loadModels(APPLICATION_PATH . '/../library');
    }

    protected function _initSession()
    {     
				$this->bootstrap('databases');
        $config = array(
					'name'           => 'session',
					'primary'        => 'id',
					'modifiedColumn' => 'modified',
					'dataColumn'     => 'data',
					'lifetimeColumn' => 'lifetime'
				);
        $sessionHandler = new Zend_Session_SaveHandler_DbTable($config);
				Zend_Session::setSaveHandler($sessionHandler);
				Zend_Session::start();
    }

    protected function _initMailer()
    {
        $this->bootstrap('mail');
        $mailer = new Zend_Mail('utf-8');
        Zend_Registry::set('mailer', $mailer);
    }
    
    protected function _initJsonEncoder()
    {
        // There is problem with php json_encode function and utf-8 strings.
        // Here we tell the Zend_Json encoder to use its built in encoder that
        // can properly encode utf-8 strings, unlike the php one.
        Zend_Json::$useBuiltinEncoderDecoder = true;
    }

    protected function _initApplicationConfig()
    {
        $options = $this->getOptions();
        Zend_Registry::set('config.recaptcha', $options['recaptcha']);
    }
    
    protected function _initRoutes()
    {
        $this->bootstrap('frontController');

        $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/routes.ini', 'production');
        $router = $this->frontController->getRouter();
        $router->setChainNameSeparator('/');
        $router->addConfig($config, 'routes');
    }

    protected function _initI18n()
    {
        // set up timezone
        date_default_timezone_set('America/Los_Angeles');

        // set up locale
        $locale = Zend_Locale::setDefault('en_US');
        Zend_Registry::set('Zend_Locale', $locale);
    }

    protected function _initLucene()
    {
        // allow numbers in searches (default is alpha only)
        $analyzer = new Zend_Search_Lucene_Analysis_Analyzer_Common_TextNum_CaseInsensitive();
        Zend_Search_Lucene_Analysis_Analyzer::setDefault($analyzer);

        // create content index
        $indexPath = DATA_PATH . '/lucene/content-index';
        try {
            $index = Zend_Search_Lucene::open($indexPath);
        } catch (Zend_Search_Lucene_Exception $e) {
            $index = Zend_Search_Lucene::create($indexPath);
        }
        Zend_Registry::set('contentIndex', $index);

        // create event index
        $indexPath = DATA_PATH . '/lucene/event-index';
        try {
            $index = Zend_Search_Lucene::open($indexPath);
        } catch (Zend_Search_Lucene_Exception $e) {
            $index = Zend_Search_Lucene::create($indexPath);
        }
        Zend_Registry::set('eventIndex', $index);

        // create discussion index
        $indexPath = DATA_PATH . '/lucene/discussion-index';
        try {
            $index = Zend_Search_Lucene::open($indexPath);
        } catch (Zend_Search_Lucene_Exception $e) {
            $index = Zend_Search_Lucene::create($indexPath);
        }
        Zend_Registry::set('discussionIndex', $index);

        // init search indexes
        $indexPath = DATA_PATH . '/lucene/paper-index';
        try {
            $index = Zend_Search_Lucene::open($indexPath);
        } catch (Zend_Search_Lucene_Exception $e) {
            $index = Zend_Search_Lucene::create($indexPath);
        }
        Zend_Registry::set('paperIndex', $index);

        // create person index
        $indexPath = DATA_PATH . '/lucene/person-index';
        try {
            $index = Zend_Search_Lucene::open($indexPath);
        } catch (Zend_Search_Lucene_Exception $e) {
            $index = Zend_Search_Lucene::create($indexPath);
        }
        Zend_Registry::set('personIndex', $index);

        // create lab index
        $indexPath = DATA_PATH . '/lucene/lab-index';
        try {
            $index = Zend_Search_Lucene::open($indexPath);
        } catch (Zend_Search_Lucene_Exception $e) {
            $index = Zend_Search_Lucene::create($indexPath);
        }
        Zend_Registry::set('labIndex', $index);

        // create post index (index for site wide search
        $indexPath = DATA_PATH . '/lucene/site-index';
        try {
            $index = Zend_Search_Lucene::open($indexPath);
        } catch (Zend_Search_Lucene_Exception $e) {
            $index = Zend_Search_Lucene::create($indexPath);
        }
        Zend_Registry::set('siteIndex', $index);
    }
}
