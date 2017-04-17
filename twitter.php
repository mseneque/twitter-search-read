<?php
// MATTHEW SENEQUE : 10401788

// read comments at: http://php.net/manual/en/function.curl-setopt.php

// add extension=php_curl.dll in php.ini file
// sudo apt-get install php5-curl

// usage: 'twitter.php (show|search) (query) (number)'

require 'autoload.php';
use Abraham\TwitterOAuth\TwitterOAuth;

require 'twitter_keys.php';

$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $access_token, $access_token_secret);

// verify the credentials are correct otherwise exit script
$content = $connection->get("account/verify_credentials");
if (empty($content->id)) {
    print "Could not connect to the Twitter api:\n";
    foreach ($content->errors as $error) {
        print "$error->code: $error->message\n";
    }
    exit();
}

// set query and number of most recent tweets to return
if (empty("$argv[3]")  || !is_numeric("$argv[3]")) {
    print "usage: 'twitter.php (show|search) (query) (number)'\n";
    exit();
} 

$query = urlencode("$argv[2]");
$number =  round("$argv[3]");

// set case for input args
switch ("$argv[1]") {
    case "show":
        try {
            // get tweets
            $tweets =  $connection->get('statuses/user_timeline', ['screen_name' => "$query", 'count' => $number, 'exclude_replies' => true]);
            print "Showing $number most recent tweets from $query:\n";
            // display all tweets to screen
            foreach ($tweets as $key => $tweet) {
                print $key+1 . ": $tweet->text\n";
            };
            break;
        } catch (exception $e) {
            print "Exception: $e->getMessage()\n";
        }
    case "search":
        try {
            // get word search
            $tweets = $connection->get('search/tweets', ['q' => "$query", 'count' => $number, 'include_entities' => false]);
            print "Searching $number most recent tweets that contain $query:\n";
            // display all tweets to screen
            foreach ($tweets->statuses as $key => $tweet ) {
                print $key+1 . ": $tweet->text\n";
            };
            break;
        } catch (Exception $e) {
            print "Exception: $e->getMessage()\n";
        }
    default:
        print "usage: 'twitter.php (show|search) (query) (number)'";
};

?>


