<?php
/*
Plugin Name: BuddyPress Avatar Popover
Plugin URI: http://premium.wpmudev.org/project/media-embeds-for-buddypress-activity
Description: A Facebook-style media sharing improvement for the activity box.
Version: 1.5
Author: Ve Bailovity (Incsub), designed by Brett Sirianni (The Edge)
Author URI: http://premium.wpmudev.org
WDP ID: 232

Copyright 2009-2011 Incsub (http://incsub.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License (Version 2 - GPLv2) as published by
the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

if ( !defined( 'BPAP_PLUGIN_DIRNAME' ) )
	define( 'BPAP_PLUGIN_DIRNAME', basename( dirname( __FILE__ ) ) );

// Path and URL
if ( !defined( 'BPAP_PLUGIN_DIR' ) )
	define( 'BPAP_PLUGIN_DIR', trailingslashit( WP_PLUGIN_DIR . '/' . BPAP_PLUGIN_DIRNAME ) );

if ( !defined( 'BPAP_PLUGIN_URL' ) ) {
	$plugin_url = trailingslashit( plugins_url( BPAP_PLUGIN_DIRNAME ) );
	define( 'BPAP_PLUGIN_URL', $plugin_url );
}
/**
 * Enqueue member popover scripts.
 *
 * @author Bourne
 */
function bpap_enqueue_scripts_popover() {
	wp_enqueue_script( 'bpap-ppover', BPAP_PLUGIN_URL . 'js/bpap-popover.js' );
	// Store current logged in member ID in the js
	wp_localize_script( 'bpap-member', '_member', array( 'id' => bp_loggedin_user_id() ) );
	
}
add_action( 'bp_after_member_home_content', 'bpap_enqueue_scripts_popover' );
add_action( 'bp_after_directory_members', 'bpap_enqueue_scripts_popover' );
add_action( 'bp_after_member_home_content', 'bpap_enqueue_scripts_popover' );
add_action( 'bp_after_directory_groups_page', 'bpap_enqueue_scripts_popover' );
add_action( 'bp_after_group_home_content', 'bpap_enqueue_scripts_popover' );

/**
 * ajax get group
 *
 * @author Bourne
 */
function bpap_group_ajax_get() {
	global $current_user;
	
	$group_id = 137;//$_POST['group_id'];
	
	$group = groups_get_group( array( 'group_id' => $group_id ) );
	
	$avatar_options = array ( 
		'item_id' => $group_id,
		'object' => 'group',
		'type' => 'full',
		'width' => 90,
		'height' => 90,
		'html' => false
		);
	
	$group->avatar = bp_core_fetch_avatar( $avatar_options );
	
	$group->description = substr( $group->description, 0, 140 ) . ' ...';
	
	$group->is_user_member = groups_is_user_member( $current_user->ID, $group_id );
	
	$group->user_is_login = is_user_logged_in();
	
	$avatar_options['object'] = 'member';
	
	foreach( $group->admins as $member ) {
		$avatar_options['item_id'] = $member->user_id;
		
		$avatar_options = array ( 
			'item_id' => $member->user_id,
			'object' => 'member',
			'type' => 'full',
			'width' => 50,
			'height' => 50,
			'html' => false
			);
		
		$member->avatar = bp_core_fetch_avatar( $avatar_options );
	}
	
	wp_die( json_encode( $group ) );
}
add_action ('wp_ajax_get_group', 'bpap_group_ajax_get');
add_action( 'wp_ajax_nopriv_get_group', 'bpap_group_ajax_get' );