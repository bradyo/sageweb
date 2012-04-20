<?php

/**
 * Doctrine ORM Base class for Profile model.
 *
 * @property integer $id
 * @property integer $userId
 * @property string $location
 * @property string $aboutBody
 * @property string $interestsBody
 * @property string $websiteUrl
 * @property string $blogUrl
 * @property string $blogFeedUrl
 * @property string $email
 * @property string $linkedInUrl
 * @property string $facebookUrl
 * @property string $aimId
 * @property string $yahooId
 * @property string $msnId
 * @property string $twitterId
 * @property boolean $inTwitterStream
 * 
 * @author Brady Olsen <bradyo@uw.edu>
 */
class Sageweb_Cms_Profile extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('user_profile');
        $this->option('type', 'INNODB');
        
        $this->hasColumn('id', 'integer', 4, array(
            'primary' => true,
            'autoincrement' => true,
        ));
        $this->hasColumn('user_id as userId', 'integer', 4, array('notnull' => true));
        $this->hasColumn('location', 'string', 128);
        $this->hasColumn('about_body as aboutBody', 'clob');
        $this->hasColumn('interests_body as interestsBody', 'clob');
        $this->hasColumn('website_url as websiteUrl', 'string', 255);
        $this->hasColumn('blog_url as blogUrl', 'string', 255);
        $this->hasColumn('blog_feed_url as blogFeedUrl', 'string', 255);
        $this->hasColumn('email', 'string', 255);
        $this->hasColumn('facebook_id as facebookId', 'string', 64);
        $this->hasColumn('linkedin_id as linkedInId', 'string', 64);
        $this->hasColumn('aim_id as aimId', 'string', 64);
        $this->hasColumn('yahoo_id as yahooId', 'string', 64);
        $this->hasColumn('msn_id as msnId', 'string', 64);
        $this->hasColumn('twitter_id as twitterId', 'string', 64);
        $this->hasColumn('in_twitter_stream as inTwitterStream', 'boolean');
    }
}