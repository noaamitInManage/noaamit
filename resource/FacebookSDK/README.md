                                        ### Custom Facebook SDK API ###

All files of Api are stored in DEV folder on azrieli.inmanage<br />
Very important to know PHP version of the server, minimum version 5.3 >=<br />

Do not touch files that in VENDOR folder,<br />
it's a core files of fasebook SDK, all changes will be made in API folder and settings.php and FasebookSDK.php

To start use the API first of all we set default configuration in <strong> SETTINGS.PHP </strong>

<br />
When You open that file you will see two includes of autoloads,<br /> if you got problem with facebook autoloader(<strong>FacebookSDK/vendor/autoload.php</strong>) go to <a href="https://developers.facebook.com/docs/php/gettingstarted" target="_blank">
https://developers.facebook.com/docs/php/gettingstarted
<a/>
and read the Guide

Second include is of Our API that in the same directory with <strong>settings.php</strong>,<br />
if You got Trouble with autoload Custom Classes of API,<br /> 
first set constant `define('_CUSTOM_FACEBOOK_SDK_PATH_', 'your path, and do not include in FacebookSDK folder')` in <strong>settings.php</strong><br/>
if after that you any way get trouble, check The <strong>autoloader.php</strong> print variable called `$file` and you see the absolute path of the called class, include class file<br />

`print $file;`

lets asume that you all ready setup all needed constants,<br/>
After that let's check if all autoloads are working clear,<br />

just type:<br />

`$FbApi = new FacebookUser('constant of app id', 'constant of app secret key');`<br />
`$FbApi->initFacebookSDKRequestFields()->setAccessToken('put here user access token');`

While Initialization of the class new FacebookUser we setting `APP_ID` and `APP_SECRET_KEY`<br />
<strong>It is very important to set them, otherwise oure app will be crushed</strong><br />
`$FbApi->initFacebookSDKRequestFields();`
After that we initializing all request fields that we want to get from facebook,<br />
if you want more fields or remove some field from array, for this job we got help functions,<br />
Example of them you see in the forward.

After that VERY IMPORTANT TO SET `USER_ACCESS_TOKEN`<br/> if not, app will crush!!!
We will do this with method `$FbApi->setAccessToken('put here user access token');`<br />

After that we Initialize the SDK API of Facebook like this:< br />

`$fb = new Facebook\Facebook([`

    `'app_id'                => (($fbapi->getFbAppId())     ? $fbapi->getFbAppId() : _FACEBOOK_APP_ID_),`
   
    `'app_secret'            => (($fbapi->getFbAppSecret()) ? $fbapi->getFbAppSecret() : _FACEBOOK_APP_SECRET_),`
    
    `'default_graph_version' => (($fbapi->getFbAppVersion()) ? $fbapi->getFbAppVersion() : _FACEBOOK_APP_VERSION_),`
    
`]);`
