<?php
/*
 Plugin Name: See attachments
 Plugin URI: http://www.mijnpress.nl
 Description: Shows all attachments for a post or page
 Version: 1.0
 Author: Ramon Fincken
 Author URI: http://www.mijnpress.nl
 Images by: http://24charlie.deviantart.com/art/Black-Pearl-Files-78798192
 */
if (!defined('ABSPATH')) die("Aren't you supposed to come here via WP-Admin?");

if(!class_exists('mijnpress_plugin_framework'))
{
	include('mijnpress_plugin_framework.php');
}

/* Define the custom box */
add_action('add_meta_boxes', 'myplugin_add_custom_box');

/* Adds a box to the main column on the Post and Page edit screens */
function myplugin_add_custom_box() {
	add_meta_box( 'myplugin_sectionid', __( 'See attachments', 'plugin_see_attachments' ),
                'myplugin_inner_custom_box', 'post' );
	add_meta_box( 'myplugin_sectionid', __( 'See attachments', 'plugin_see_attachments' ),
                'myplugin_inner_custom_box', 'page' );
}

/* Prints the box content */
function myplugin_inner_custom_box($post) {
	$args = array(
		'post_type' => 'attachment',
		'numberposts' => -1,
		'post_status' => null,
		'post_parent' => $post->ID
	);
	$attachments = get_posts($args);
	if ($attachments) {
		$image_dir = mijnpress_plugin_framework::get_plugin_url(NULL,__FILE__).'/images/';
		$i = 0;
		foreach ($attachments as $attachment) {
			$i++;
			echo "\n";
			echo '<div style="float: left; border: 1px solid; height: 190px; margin-bottom: 5px; margin-right: 5px;">';
			echo '<p>Attachment '.$i.'</p>';

			$icon = wp_mime_type_icon($attachment->post_mime_type);
			$temp = end(explode('/',$icon));
			if($temp == 'default.png')
			{
				$end = end(explode('.',$attachment->guid));
				$file = WP_PLUGIN_DIR.'/see-attachments/images/'.$end.'.png';
				// Wait! We have a better one!
				if(file_exists($file))
				{
					$icon = $image_dir.$end.'.png';
				}
			}

			// Show the real file? Even better!
			if(in_array($end, array('png','jpeg','jpg','gif','bmp')))
			{
				$icon = $attachment->guid;
			}

			$title = apply_filters('the_title', $attachment->post_title);
			$icon_html = '<a href="'.$attachment->guid.'" target="_blank"><img src="'.$icon.'" style="max-width: 128px; max-height: 128px;"></a>';
			$href_human = end(explode('uploads/',$attachment->guid));
			
			echo '<p><strong>'.$title.'</strong></p>';
			echo '<p>Link: <br/><a href="'.$attachment->guid.'" target="_blank">'.$href_human.'</a></p>';
			echo '<p>Type or preview:<br/>'.$icon_html.'</p>';
			
			echo '</div> <!-- end div for attachment -->';
		}
		echo '<div style="clear: both;"></div>';
	}
	else
	{
		echo '<div style="clear: both;">'.__('No attachments found').'</div>';
	}
}
?>