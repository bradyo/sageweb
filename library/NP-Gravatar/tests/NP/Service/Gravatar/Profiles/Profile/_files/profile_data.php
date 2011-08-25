<?php
return array(
    'id'=>1,
    'profileUrl'=>'http://gravatar.com/foo',
    'preferredUsername'=>'foo',
    'thumbnailUrl'=>'http://www.gravatar.com/avatar/1111111111111111nnnnnnnnnnnnnnnn',
    'photos'=>array(
        array(
            'value'=>'http://www.gravatar.com/avatar/1111111111111111nnnnnnnnnnnnnnnn',
            'type'=>'thumbnail'
        ),
        array('value'=>'http://www.gravatar.com/userimage/1/2222222222222222nnnnnnnnnnnnnnnn'),
        array('value'=>'http://www.gravatar.com/userimage/1/3333333333333333nnnnnnnnnnnnnnnn')
    ),
    'profileBackground'=>array(
        'color'=>'#d1d1d1',
        'position'=>'left',
        'repeat'=>'repeat',
        'url'=>'http://www.gravatar.com/bg/1/1111111111111111nnnnnnnnnnnnnnnn'
    ),
    'name'=>array(
        'givenName'=>'Foo',
        'familyName'=>'Bar',
        'formatted'=>'Foo Bar'
    ),
    'displayName'=>'Foo Bar',
    'aboutMe'=>'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce imperdiet egestas ultrices.',
    'currentLocation'=>'FooBar',
    'phoneNumbers'=>array(
        array('type'=>'mobile', 'value'=>1234567)
    ),
    'emails'=>array(
        array('value'=>'foo@bar.com', 'primary'=>true)
    ),
    'ims'=>array(
        array('type'=>'aim', 'value'=>'foobar'),
        array('type'=>'msn', 'value'=>'foo@bar.com'),
        array('type'=>'yahoo', 'value'=>'foobar'),
        array('type'=>'gtalk', 'value'=>'foo.bar@gmail.com')
    ),
    'accounts'=>array(
        array(
            'domain'=>'blogger.com',
            'userid'=>11111111111111111111,
            'display'=>'blogger.com',
            'url'=>'http://www.blogger.com/profile/11111111111111111111',
            'verified'=>true,
            'shortname'=>'blogger'
        ),
        array(
            'domain'=>'digg.com',
            'username'=>'foobar',
            'display'=>'foobar',
            'url'=>'http://digg.com/users/foobar',
            'verified'=>true,
            'shortname'=>'digg'
        ),
        array(
            'domain'=>'facebook.com',
            'username'=>'foobar',
            'display'=>'foobar',
            'url'=>'http://www.facebook.com/foobar',
            'verified'=>true,
            'shortname'=>'facebook'
        ),
        array(
            'domain'=>'twitter.com',
            'username'=>'foobar',
            'display'=>'@foobar',
            'url'=>'http://twitter.com/foobar',
            'verified'=>true,
            'shortname'=>'twitter'
        )
    ),
    'urls'=>array(
        array('title'=>'Google', 'value'=>'http://www.google.com'),
        array('title'=>'WordPress.com', 'value'=>'http://wordpress.com'),
        array('title'=>'Gravatar', 'value'=>'http://gravatar.com')
    )
);