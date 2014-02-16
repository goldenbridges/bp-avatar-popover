<?php
/*
Plugin Name: BuddyPress Avatar Popover
Plugin URI: https://github.com/goldenbridges/bp-avatar-popover/
Description: Adds a popover box when hovering on the group/member avatars and gives you more information at a glance.
Version: 0.1.0
Author: The Golden Bridges Foundation
Author URI: http://github.com/goldenbridges/

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
if ( !defined( 'BPAP_PLUGIN_VERSION' ) )
	define( 'BPAP_PLUGIN_VERSION', '0.1.0' );

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
	$user = wp_get_current_user();
	
	wp_enqueue_style( 'bpap-ppover', BPAP_PLUGIN_URL . 'css/bpap-popover.css', $dep = array(), $version = BPAP_PLUGIN_VERSION );
	wp_enqueue_script( 'bpap-ppover', BPAP_PLUGIN_URL . 'js/bpap-popover.js', $dep = array(), $version = BPAP_PLUGIN_VERSION );
	// Store current logged in member ID in the js
	$user = get_userdata( $user->ID );
	wp_localize_script( 'bpap-ppover', '_member', array( 'id' => $user->ID ) );
}
add_action( 'bp_after_member_home_content', 'bpap_enqueue_scripts_popover' );
add_action( 'bp_after_member_home_content', 'bpap_enqueue_scripts_popover' );
add_action( 'bp_after_directory_members', 'bpap_enqueue_scripts_popover' );
add_action( 'bp_after_members_loop', 'bpap_enqueue_scripts_popover' );
add_action( 'bp_after_directory_groups_page', 'bpap_enqueue_scripts_popover' );
add_action( 'bp_after_group_home_content', 'bpap_enqueue_scripts_popover' );

/**
 * get group popoverbox with ajax
 *
 * @author Bourne
 */
function bpap_group_ajax_get_group_popover_box() {
	$group_slug = $_POST['group_slug'];
	$group_id = groups_get_id( $group_slug );
	$user = wp_get_current_user();
	
	$group = groups_get_group( array( 'group_id' => $group_id ) );
	
	$avatar_options = array ( 
		'item_id' => $group_id,
		'object' => 'group',
		'type' => 'full',
		'width' => 90,
		'height' => 90
		);
?>
<div class="pop-inner pop-group">
						<div class="media">
							<div class="pull-left"><a class="thumbnail" href="<?php echo bp_group_permalink( $group );?>"><?php echo bpap_core_fetch_avatar( $avatar_options );?></a></div>
							<div class="media-body">
								<h5 class="media-heading"><a class="link-blue" title="<?php echo $group->name;?>" href="<?php echo bp_group_permalink( $group );?>"><?php echo $group->name;?></a></h5>
								<small class="muted"><?php echo $group->total_member_count;?> Members</small>
							</div>
							<div class="intor"><p><?php echo substr( $group->description, 0, 240 );?>...</p></div>
						</div>
						<div class="clearfix">
							<div class="box box-hh pull-left">
								<div class="hd">
									<h5 class="muted">Group Admins</h5>
								</div>
								<div class="bd">
									<ul class="thumbnails list-thumb">
<?php 
				$avatar_options['object'] = 'member';
				$avatar_options['width'] = 50;
				$avatar_options['height'] = 50;
				foreach( $group->admins as $member ) :
					$avatar_options['item_id'] = $member->user_id;
?>
									<li>
										<a href="<?php echo bp_core_get_user_domain( $member->user_id );?>" class="thumbnail">
						                  <?php echo bpap_core_fetch_avatar( $avatar_options );?>
						                </a>
									</li>
<?php endforeach;?>
				</ul>
								</div>
							</div>
							<?php if ( is_user_logged_in() ) : ?>
							<div class="pull-right group-button" id="groupbutton-<?php echo $group_id;?>">
								<?php if ( groups_is_user_member( $user->ID, $group_id ) ) : ?>
								<a title="Leave Group" class="Leave Group" href="<?php echo wp_nonce_url( bp_get_group_permalink( $group ) . 'leave-group', 'groups_leave_group' );?>">Leave Group</a>
								<?php else: ?>
								<a title="Join Group" class="group-button join-group" href="<?php echo wp_nonce_url( bp_get_group_permalink( $group ) . 'join', 'groups_join_group' );?>">Join Group</a>
								<?php endif;?>
							</div>
							<?php endif;?>
						</div>
					</div>
<?php
	wp_die();
}
add_action ('wp_ajax_get_group_popover_box', 'bpap_group_ajax_get_group_popover_box');
add_action( 'wp_ajax_nopriv_get_group_popover_box', 'bpap_group_ajax_get_group_popover_box' );


/**
 * get member popoverbox with ajax
 *
 * @author Bourne
 */
function bpap_ajax_get_member_popover_box() {
	$user_id = $_POST['id'];
	
	$avatar_options = array ( 'item_id' => $user_id, 'object' => 'member', 'type' => 'full', 'width' => 90, 'height' => 90 );
?>
<div class="pop-inner pop-member">
	<div class="media">
		<div class="pull-left"><a class="thumbnail" href=""><?php echo bpap_core_fetch_avatar( $avatar_options );?></a></div>
		<div class="media-body">
			<h5 class="media-heading"><a class="link-blue" href="<?php echo bp_core_get_user_domain( $user_id );?>"><?php echo bpap_core_get_user_displayname( $user_id );?></a></h5>
			<?php if ( is_plugin_active( 'buddypress/bp-loader.php' ) ) : 
				$groups = groups_get_groups( array( 'user_id' =>  $user_id, 'per_page' => 1 ) );
				$group = $groups['groups'][0];
			?>
			<p class="muted"><a class="link-blue" title="" href="<?php echo bp_group_permalink( $group );?>"><?php echo $group->name;?></a></p>
			<?php endif;?>
		</div>
	</div>
	<?php if ( is_plugin_active( 'buddypress/bp-loader.php' ) ) :?>
	<ul class="unstyled list-pop-member">
		<li class="friendship-button">
<?php
	$friendship_status = BP_Friends_Friendship::check_is_friend( bp_loggedin_user_id(), $user_id );
	
	if ( $friendship_status == 'is_friend') : ?>
		<a id="friend-<?php echo $user_id;?>" rel="add" class="btn btn-mini btn-block friend_link" href="<?php echo wp_nonce_url( bp_loggedin_user_domain() . bp_get_friends_slug() . '/remove-friend/' . $user_id . '/', 'friends_remove_friend' );?>" title="Remove Pengyou"><i class="icon-minus"></i> Remove Pengyou</a>
	<?php elseif ( $friendship_status == 'not_friends') : ?>
		<a id="friend-<?php echo $user_id;?>" rel="remove" class="btn btn-mini btn-block btn-danger friend_link" href="<?php echo wp_nonce_url( bp_loggedin_user_domain() . bp_get_friends_slug() . '/add-friend/' . $user_id . '/', 'friends_add_friend' );?>" title="Add Pengyou"><i class="icon-plus icon-white"></i> Add Pengyou</a>
	<?php elseif ( $friendship_status == 'pending') :?>
		<a id="friend-<?php echo $user_id;?>" rel="add" class="btn btn-mini btn-block friend_link" href="<?php echo wp_nonce_url( bp_loggedin_user_domain() . bp_get_friends_slug() . '/requests/cancel/' . $user_id . '/', 'friends_withdraw_friendship' );?>" title="Cancel Friendship Request"><i class="icon-minus"></i> Cancel Request</a>
	<?php endif;?>
		</li>
		
		<li><a class="btn btn-mini btn-block" href="<?php echo wp_nonce_url( bp_loggedin_user_domain() . bp_get_messages_slug() . '/compose/?r=' . bp_core_get_username( $user_id ) );?>"><i class="icon-envelope"></i> Send Message</a></li>
	</ul>
	<?php endif; ?>
</div>
<?php
	wp_die( );
}
add_action ('wp_ajax_get_member_popover_box', 'bpap_ajax_get_member_popover_box');
add_action( 'wp_ajax_nopriv_get_member_popover_box', 'bpap_ajax_get_member_popover_box' );

/**
 * Get an avatar
 * @author Bourne
 */
function bpap_core_fetch_avatar( $args = '' ) {
	if ( is_plugin_active( 'buddypress/bp-loader.php' ) ) 
		return bp_core_fetch_avatar( $args );
	
	extract( $args );
	return get_avatar( $item_id, $width, '', '' );
}

/**
 * Get an user displayname
 * @author Bourne
 */
function bpap_core_get_user_displayname( $user_id ) {
	if ( is_plugin_active( 'buddypress/bp-loader.php' ) ) 
		return bp_core_get_user_displayname( $user_id );

	$user_info = get_userdata( $user_id );
	return $user_info->user_nicename;
}