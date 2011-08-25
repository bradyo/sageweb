NP-Gravatar package is a Zend Framework extension, which provides
classes for using and implementing Gravatar's services and features.

There are three separate components in this package, and each of them
represents implementation of some Gravatar's API feature. Those are:
   * NP_Service_Gravatar_Profiles - client for performing Gravatar 
     profile requests and retrieving profile data of some Gravatar
	 user, based on his/her primary email address. Profile data can be 
	 returned in various formats which are offered by Gravatar Profiles 
	 API (JSON, XML, PHP, VCF/vCard, QR Code), and some of those 
	 formats can be converted to the NP_Gravatar_Profile object, which 
	 represents profile data of some user in object-oriented manner. 
   * NP_Service_Gravatar_XmlRpc - client for the Gravatar XML-RPC API, 
     which maps all methods provided by that API. You can find out more 
	 about Gravatar XML-RPC API at this link: 
	 http://en.gravatar.com/site/implement/xmlrpc/.
   * NP_View_Helper_Gravatar - view helper for rendering Gravatar image 
     URLs, which follows Gravatar Image Requests API.

AUTHOR
------
Nikola Posa <posa.nikola@gmail.com>

FEATURES
--------
- Client for performing Gravatar profile requests
- XML-RPC client which provides interface to the Gravatar XML-RPC API
- View helper for rendering Gravatar image URLs

INSTALLATION
------------
Copy NP folder, with all of its contents, in some of your project's 
folder, for example, in folder where you keep your libraries, and put 
it in the include path.

USAGE
-----
NP_Service_Gravatar_Profiles usage examples:
--------------------------------------------
//Creating instance:
$gravatarService = new NP_Service_Gravatar_Profiles();
//Changing response format to XML:
$gravatarService->setResponseFormat(new NP_Service_Gravatar_Profiles_ResponseFormat_Xml());

//Getting profile data.
$profile = $gravatarService->getProfileInfo('foo@bar.com');
//$profile is instance of NP_Gravatar_Profile so we can access some of its properties.
echo 'ID: ' . $profile->id . '<br />';
echo 'Username: ' . $profile->getPreferredUsername() . '<br /><br />';

echo 'Photos: <br />';
foreach($profile->getPhotos() as $photo) {
   echo '<img src="' . $photo->value . '" /> <br />';
}

//Changing response format to JSON:
$gravatarService->setResponseFormat(new NP_Service_Gravatar_Profiles_ResponseFormat_Json());
//Getting profile data but forcing raw Zend_Http_Response object to be returned, 
//by passing boolean true for the second argument of the getProfileInfo() method:
$response = $gravatarService->getProfileInfo('foo@bar.com', true);
if ($response instanceof Zend_Http_Response) { //true!
   //do something
}

//Changing response format to QR Code:
$gravatarService->setResponseFormat(new NP_Service_Gravatar_Profiles_ResponseFormat_QRCode());
//QR Code response can not be exported NP_Gravatar_Profile object, as that 
//response format type does not implement 
//NP_Service_Gravatar_Profiles_ResponseFormat_ParserInterface interface, 
//so raw Zend_Http_Response object will allways be returned when using 
//that response format:
$response = $gravatarService->getProfileInfo('foo@bar.com');
echo $response->getHeader('Content-type'); //Prints "image/png".

NP_Service_Gravatar_XmlRpc usage examples:
------------------------------------------
//Gravatar XML-RPC implementation requires API key for the 
//authentication proccess. It can be retrieved on the page 
//for editing profile, on wordpress.com.
$apiKey = 'someAPIKey'; 
$email = 'foo.bar@foobar.com'; //Email address associated with the $apiKey.
//Creating instance:
$gravatarXmlRpc = new NP_Service_Gravatar_XmlRpc($apiKey, $email);

//Checking whether there's a gravatar account registered with supplied email addresses.
$result = $gravatarXmlRpc->exists(array(
   'posa.nikola@gmail.com', //That's me. :D
   'foo@example.com'
));
$values = array_values($result);
echo (bool)$values[0]; //Prints "true", as I do have Gravatar account. :)
echo (bool)$values[1]; //Prints "false", as that second email address probably doesn't exist.

//Getting user images on the current account:
$images = $gravatarXmlRpc->userImages();
//$image is instance of NP_Service_Gravatar_XmlRpc_UserImage, 
//as we didn't pass $raw parameter as "true" when executing 
//userImages() method.
$image = $images[0];
$imageUrl = $image->getUrl(); //Instance of Zend_Uri_Http.
echo $image->getRating(); //Prints some rating (G, PG, R or X).

//Saves some image to be a user image for the current account.
$this->_gravatarXmlRpc->saveData('path/to/someImage.jpg', NP_Service_Gravatar_XmlRpc::PG_RATED);

NP_View_Helper_Gravatar usage examples:
---------------------------------------
//Generating Gravatar URL.
echo '<img src="' . $this->gravatar('foo@bar.com') . '" />;

//Generating Gravatar URL and specifying size and rating options.
echo '<img src="' . $this->gravatar('foo@bar.com', array('s'=>200, 'r'=>'pg')) . '" />;
//Full parameter names are supported, too.
echo '<img src="' . $this->gravatar('foo@bar.com', array('size'=>100, 'default'=>'identicon')) . '" />;

//Generating Gravatar URL and specifying file-type extension.
echo '<img src="' . $this->gravatar('foo@bar.com', array('s'=>200), 'jpg') . '" />;
//Above view helper call will produce this URL:
//http://www.gravatar.com/avatar/f3ada405ce890b6f8204094deb12d8a8.jpg?s=200

SYSTEM REQUIREMENTS
-------------------
This package is made to be used along with some project which is
powered by Zend Framework (versions before 2.0), so this link is the
right place to find out more about the requirements:
http://framework.zend.com/manual/en/requirements.html.

LICENSE
-------
The files in this archive are released under the New BSD License.
You can find a copy of this license in LICENSE.txt.