<?php 
  require_once('lib/twitteroauth.php'); // Path to lib
class Fishpig_Wordpress_Helper_Twitter extends Fishpig_Wordpress_Helper_Abstract
{

  const ACCESS_TOKEN        = '949887060-aNHNpXHE1kdoSmiE9Ue1fjs8M2ovidk5BklP5hkl';
    const ACCESS_SECRET = 'EAn5nmpAhtCjAZZGCjJldVwMH0fddAiY0cDYxvzwgtzEO';
    const CONSUMER_KEY              = '4TboBRBdvmWYPDmmPpIK2eIyY';
    const CONSUMER_SECRET           = 'VRxPgcgdzISzT0XgbV0YSHbGrCujgbukjGuzSx32vuiTMULisv';
	const CACHE_ENABLED   = 'false';
	const CACHE_LIFETIME    = "3600";
	const HASH_SALT = " ";
	
    public function getTweets ()
    {
        
    // Check if keys are in place
    if (self::CONSUMER_KEY === '' || self::CONSUMER_SECRET === '' || self::CONSUMER_KEY === 'CONSUMER_KEY_HERE' || self::CONSUMER_SECRET === 'CONSUMER_SECRET_HERE') {
        echo 'You need a consumer key and secret keys. Get one from <a href="https://apps.twitter.com/">apps.twitter.com</a>';
      
        exit;
    }

    // If count of tweets is not fall back to default setting
    //$username = filter_input(INPUT_GET, 'username', FILTER_SANITIZE_SPECIAL_CHARS);
   // $number = filter_input(INPUT_GET, 'count', FILTER_SANITIZE_NUMBER_INT);
   // $exclude_replies = filter_input(INPUT_GET, 'exclude_replies', FILTER_SANITIZE_SPECIAL_CHARS);
    $list_slug = filter_input(INPUT_GET, 'list', FILTER_SANITIZE_SPECIAL_CHARS);
    //$hashtag = filter_input(INPUT_GET, 'hashtag', FILTER_SANITIZE_SPECIAL_CHARS);
     $hashtag = "@AureliaSkincare";
	$number = 5;
	$username = "Aurelia Skincare";
	$exclude_replies= true;
	
	if(CACHE_ENABLED) {
        // Generate cache key from query data
        $cache_key = md5(
            var_export(array($username, $number, $exclude_replies, $list_slug, $hashtag), true) . self::HASH_SALT
        );
    
        // Remove old files from cache dir
        $cache_path  = dirname(__FILE__) . '/cache/';
        foreach (glob($cache_path . '*') as $file) {
            if (filemtime($file) < time() - self::CACHE_LIFETIME) {
                unlink($file);
            }
        }
    
        // If cache file exists - return it
        if(file_exists($cache_path . $cache_key)) {
            header('Content-Type: application/json');
    
            echo file_get_contents($cache_path . $cache_key);
            exit;
        }
    }
	
    /**
     * Gets connection with user Twitter account
     * @param  String $cons_key     Consumer Key
     * @param  String $cons_secret  Consumer Secret Key
     * @param  String $oauth_token  Access Token
     * @param  String $oauth_secret Access Secrete Token
     * @return Object               Twitter Session
     */
    function getConnectionWithToken($cons_key, $cons_secret, $oauth_token, $oauth_secret)
    {
        $connection = new TwitterOAuth($cons_key, $cons_secret, $oauth_token, $oauth_secret);
      
        return $connection;
    }
    
    // Connect
    $connection = getConnectionWithToken(self::CONSUMER_KEY, self::CONSUMER_SECRET, self::ACCESS_TOKEN, self::ACCESS_SECRET);
    
    // Get Tweets

      $params = array(
          'count' => $number,
          'exclude_replies' => $exclude_replies,
          'screen_name' => $username,
		  'q' => '#'.$hashtag
      );

      $url = '/statuses/user_timeline';
    

    $tweets = $connection->get($url, $params);

    // Return JSON Object
   // header('Content-Type: application/json');

   // $tweets = json_encode($tweets);
    if(CACHE_ENABLED) file_put_contents($cache_path . $cache_key, $tweets);
	
    //print_r($tweets);
	// echo "<pre>";
	// foreach($tweets as $tweetrersult)
 // {
		 
			  // if (isset($tweetrersult->text)){
			// echo $tweetrersult->text;
				// }
			
	// }
	//	exit();
	 return $tweets;
    }
	
	}
	?>