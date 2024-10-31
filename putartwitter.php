<?php
/*
Plugin Name: Scroll Twitter Widget
Plugin URI: http://pakhermawan.com/
Description: Status Twitter dengan vertical scroll vTicker.
Version: 1
Author: Pakhermawan
Author URI: http://pakhermawan.com/
*/

/*
License: GPL
Compatibility: WordPress 3.3

Installation:
Put the putartwitter.php file in your /wp-content/plugins/ directory
and activate through the administration panel, and then go to the widget panel and
drag it to where you would like to have it!
*/

/*  Copyright pakhermawan http://pakhermawan.com

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/
/* Changelog
* Des 16 2011 - v0.1 (pakhermawan)
*/

function putartwitter_init() {
	if ( !function_exists('register_sidebar_widget') )
		return;
	function putartwitter($args) {
	  extract($args);
	  $options = get_option('putartwitter');
	  $title = $options['title'];
	  $src_count = $options['src_count'];
	  $src_length = $options['src_length'];
	  $src_akun = $options['src_akun'];
	  $pre_HTML = "<div style=\"height:".$src_length."px;\">";
	  $post_HTML = '</div>';

	  global $wpdb;
	
	?><script src="<?php bloginfo('url'); ?>/wp-content/plugins/scroll-twitter-widget/jquery.vticker.js" type="text/javascript"></script><?php
	?><style type="text/css">
	.atas-gambar {float: left;
			display: block;
			vertical-align: middle;
			margin : 2px;
			text-align: left; 
			}
	</style>
	<?php
	function make_urls_links($text){
    $ret = ' ' . $text;
    $ret = preg_replace("#(^|[\n ])([\w]+?://[\w]+[^ \"\n\r\t<]*)#ise", "'\\1<a target=\"_blank\" href=\"\\2\" >\\2</a>'", $ret);
    $ret = preg_replace("#(^|[\n ])((www|ftp)\.[^ \"\t\n\r<]*)#ise", "'\\1<a target=\"_blank\" href=\"http://\\2\" >\\2</a>'", $ret);
    $ret = preg_replace("#(^|[\n ])([a-z0-9&\-_\.]+?)@([\w\-]+\.([\w\-\.]+\.)*[\w]+)#i", "\\1<a href=\"mailto:\\2@\\3\">\\2@\\3</a>", $ret);
    $ret = substr($ret, 1);
    return($ret);
	}
	$jumlah = $src_count;
	$xml = simplexml_load_file("http://twitter.com/statuses/user_timeline/".$src_akun.".rss");
	$nomor = 1;
	$output = $pre_HTML;
	$output .= "<script type=\"text/javascript\"> 
		jQuery(document).ready(function(){
		jQuery('.komenjalan').vTicker({ 
		speed: 500,
		pause: 5000,
		animation: 'fade',
		mousePause: true,
		showItems: 2,
		direction: 'down',
		height:".$src_length."
			});
		});
		</script>
		<div class=\"komenjalan\" style=\"height:".$src_length."px;\"><ul>";
	  
	  foreach($xml->channel->item as $data)
		{	
		 $output .="<li><div style='font-size:11px;'>".make_urls_links($data->title).
		 "<br><i style='color:red;font-size:11px;'>".$data->pubDate."</i><br><br></div></li>";
			if ($nomor == $jumlah) break;
			else $nomor++;
		
		}
	    
	  $output .= "</ul></div>";
	  $output .= $post_HTML;
	  
	  echo $before_widget . $before_title . $title . $after_title;
	  echo $output;
	  echo $after_widget;
	}
	
	function putartwitter_control() {
	  $options = $newoptions = get_option('putartwitter');
	  if ( $_POST["src-submit"] ) {
	    $newoptions['title'] = strip_tags(stripslashes($_POST["src-title"]));
	    $newoptions['src_count'] = (int) $_POST["src_count"];
	    $newoptions['src_length'] = (int) $_POST["src_length"];
	    $newoptions['src_akun'] = strip_tags(stripslashes($_POST["src_akun"]));
	  }
	  if ( $options != $newoptions ) {
	    $options = $newoptions;
	    update_option('putartwitter', $options);
	  }
	  $src_count = $options['src_count'];
	  $src_length = $options['src_length'];
	  $src_akun = $options['src_akun'];
	  $title = htmlspecialchars($options['title'], ENT_QUOTES);

?>
	    <?php _e('Title:'); ?> 
		<input style="width: 200px;" id="src-title" name="src-title" type="text" value="<?php echo $title; ?>" />
		<?php _e('Akun:'); ?> 
		<input style="width: 200px;" id="src_akun" name="src_akun" type="text" value="<?php echo $src_akun; ?>" />
		<p style="text-align: left;">
		<?php _e('Jumlah Postingan :'); ?> 
		<input style="width: 40px;" id="src_count" name="src_count" type="text" value="5" readonly/> 
		<br />
   	    <?php _e('Tinggi:'); ?> 
		<input style="width: 40px;" id="src_length" name="src_length" type="text" value="350" readonly/>
		</p>
        <input type="hidden" id="src-submit" name="src-submit" value="1" />
<?php
	 }
	
	// This registers our widget so it appears with the other available
	// widgets and can be dragged and dropped into any active sidebars.
	register_sidebar_widget('Scroll Twitter Widget', 'putartwitter');

	// This registers our optional widget control form. Because of this
	// our widget will have a button that reveals a 520x480 pixel form.
	register_widget_control('Scroll Twitter Widget', 'putartwitter_control', 180, 180);
}
// Run our code later in case this loads prior to any required plugins.
add_action('plugins_loaded', 'putartwitter_init');

?>
