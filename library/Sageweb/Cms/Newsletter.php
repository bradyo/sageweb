<?php

/**
 * E-mails newsletter.
 * 
 * @author Brady Olsen <bradyo@uw.edu>
 */
class Sageweb_Cms_Newsletter
{
    /**
     *
     * @param string $type "weekly" or "monthly"
     */
    public static function sendEmails($type = 'monthly')
    {
        // calculate date to get posts from based on interval
        $date = new Zend_Date();
        if ($type == 'weekly') {
            $date->sub('7', Zend_Date::DAY);
        } else {
            $date->sub('1', Zend_Date::MONTH);
        }
        $dateData = $date->toArray();
        $sinceDate = $date->toString('YYYY-MM-dd');

        // build email body
        $title = 'Sageweb.org ' . ucwords($type) . ' Newsletter';
        $view = new Zend_view();
        $view->setScriptPath(array(APPLICATION_PATH . '/views/scripts'));
        $view->title = $title;
        $view->sinceDate = $sinceDate;
        $html = $view->render('emails/newsletter.phtml');

        echo $html;

        
        // set up mailer
        $mail = new Zend_Mail('UTF-8');
        $mail->setFrom('support@sageweb.org', 'Sageweb Team');
        $mail->setSubject($title);
        $mail->setBodyHtml($html);

        // send emails
        $users = Sageweb_Cms_Table_User::findByNewsletter($type);
        foreach ($users as $user) {
            echo "mailing: " . $user->username . "\n";

            /* @var $user Sageweb_Cms_User */
            $mail->clearRecipients();
            $mail->addTo($user->email, $user->getDisplayName());
            $mail->send();
        }
    }
}