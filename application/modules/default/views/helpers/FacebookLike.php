<?php

class Zend_View_Helper_FacebookLike extends Zend_View_Helper_Abstract {

	public function facebookLike($part)
	{
        ob_start();
        if ($part == "init") {
            ?>
                <div id="fb-root"></div>
                <script>
                    window.fbAsyncInit = function() {
                        FB.init({
                            appId      : '303437013074447', // App ID
                            channelUrl : '//masterholiday.net/public/channel.php', // Channel File
                            status     : true, // check login status
                            cookie     : true, // enable cookies to allow the server to access the session
                            xfbml      : true  // parse XFBML
                        });

                        // Additional initialization code here
                    };

                    // Load the SDK Asynchronously
                    (function(d){
                        var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
                        if (d.getElementById(id)) {return;}
                        js = d.createElement('script'); js.id = id; js.async = true;
                        js.src = "//connect.facebook.net/en_US/all.js";
                        ref.parentNode.insertBefore(js, ref);
                    }(document));
                </script>
            <?php
        }
        elseif ($part == "button") {
            ?>
                <div class="fb-like" data-href="http://www.facebook.com/MasterskaaPrazdnikov" data-send="false" data-layout="button_count" data-width="50" data-show-faces="true"></div>
            <?php
        }
        $s = ob_get_clean();
        return $s;
	}
}