<?php

/*

type: layout

name: Default

description: Default Twitter Feed

*/

?>
<style>
blockquote.twitter-tweet {
	display: inline-block;
	padding: 16px;
	margin: 10px 0;
	max-width: 468px;
	border: #ddd 1px solid;
	border-top-color: #eee;
	border-bottom-color: #bbb;
	border-radius: 5px;
	box-shadow: 0 1px 3px rgba(0,0,0,0.15);
	font: bold 14px/18px Helvetica, Arial, sans-serif;
	color: #000;
}
blockquote.twitter-tweet p {
	font: normal 18px/24px Georgia, "Times New Roman", Palatino, serif;
	margin: 0 5px 10px 0;
}
blockquote.twitter-tweet a {
	font-weight: normal;
	color: #666;
	font-size: 12px;
}
</style>
<?php if($items): ?>
<?php foreach($items as $tweet): ?>
<div class="twitter-feed-item-holder"> <a href="<?php print $tweet['url']; ?>" target="_blank"><img src="<?php print $tweet['profile_image']; ?>" /><?php print $tweet['name']; ?> </a>
  <?php if($tweet['media']): ?>
  <a href="<?php print $tweet['url']; ?>" target="_blank"> <img src="<?php print $tweet['media']; ?>" width="100%" /> </a>
  <?php endif; ?>
  <span><?php print $tweet['text']; ?></span> <span><?php print $tweet['ago']; ?></span> </div>
<?php endforeach; ?>
<?php endif; ?>
