/**
 * PPY popover
 *
 * @time 2013-04-30
 * @author Lujun
 */

; (function($) {
    'use strict';

    $.fn.ppypop = function(opts, callback) {
        var defaults = {
            times: 500
        };
        var settings = $.extend({},
        defaults, opts),
        el = $(this),
        timer,
        container = $('<div id="ppypop" class="ppy-pop"><div class="popover right"><div class="arrow"></div><div class="popover-content"><div class="load"><span class="loader loader-large"></span> Loading...</div></div></div></div>'),
        showpop = function() {
            if (timer) {
                clearTimeout(timer);
            }
            timer = setTimeout(function() {
                if (callback) {
                    callback();
                }
                container.insertAfter(el).show();
                container.find('.popover-content').html('<div class="load"><span class="loader loader-large"></span> Loading...</div>');
                container.css({
                    'left': el.offset().left + el.width() + 10,
                    'top': el.offset().top + el.height() / 2 - 120
                });
                if (el.css('padding-top') != '4px') {
                    container.css({
                        'left': el.offset().left + el.width() + 20
                    });
                }
                if ($('#wpadminbar').length > 0) {
                    container.css({
                        'top': el.offset().top + el.height() / 2 - 148
                    });
                }
            },
            settings.times);
            return container;
        },
        hidepop = function() {
            if (timer) {
                clearTimeout(timer);
            }
            timer = setTimeout(function() {
                container.remove();
            },
            settings.times);
        };

        el.hover(function() {
            clearTimeout(timer);
            showpop().hover(function() {
                clearTimeout(timer);
            },
            function() {
                hidepop();
            });
        },
        function() {
            hidepop();
        });
    }

})(jQuery);

var popover_group = function(elms) {
	elms.each( function(){
		var id = $(this).data('id');
		$(this).ppypop({},function(){
			$.ajax({
			    url: '/wp-admin/admin-ajax.php',
			    type: 'post',
			    data: {
			        'action': 'get_group',
			        'group_id': id
			    },
			    success: function(dataObj) {
			    	data = $.parseJSON(dataObj);
			        var content = '';
					var btn = '';

					if (data.is_user_member == 1) {
						btn = '<a title="Leave Group" class="btn">Leave Group</a>';
					} else {
						if ( !data.user_is_login ) {
							btn = '<a title="Join Group" class="btn" href="#login-popup" data-toggle="modal">Join Group</a>';
						} else {
							btn = '<a title="Join Group" class="btn" onclick="do_group_join_modal(' + data.id + ')">Join Group</a>';
						}
						
					}
			        content += '<div class="pop-inner pop-group">\
						<div class="media">\
							<div class="pull-left"><a class="thumbnail" href=""><img src="'+data.avatar+'" width="90" height="90"></a></div>\
							<div class="media-body">\
								<h5 class="media-heading"><a class="link-blue" title="'+data.name+'" href="/groups/'+data.slug+'/">'+data.name+'</a></h5>\
								<small class="muted">HQ: ' + data.city_name + ' &nbsp; | &nbsp; ' + data.total_member_count + ' Members</small>\
								<div class="intor"><p>'+data.description+'</p></div>\
							</div>\
						</div>\
						<div class="clearfix">\
							<div class="box box-hh pull-left">\
								<div class="hd">\
									<h5 class="muted">Group Chairs</h5>\
								</div>\
								<div class="bd">\
									<ul class="thumbnails list-thumb">';
				if(data.admins) {
					for ( var i=0; i<data.admins.length; i++) {
						content += '<li>\
										<a href="/members/'+data.admins[i].user_login+'/" class="thumbnail">\
						                  <img src="'+data.admins[i].avatar+'" width="50" height="50">\
						                </a>\
									</li>';
					};
				};
				content += '</ul>\
								</div>\
							</div>\
							<div class="pull-right" id="groupbutton-' + id + '">\
								<br><br>\
								' + btn  + ' \
							</div>\
						</div>\
					</div>';
					$('#ppypop').find('.popover-content').html(content);
					$('.pop-group a[title="Leave Group"]').bind('click', function() {
						var gid = jq(this).addClass('loading').parent().attr('id');
						gid = gid.split('-');
						gid = gid[1];

						var nonce = _wp_nonce_leave_group;

						var thelink = jq(this);

						jq.post( ajaxurl, {
							action: 'joinleave_group',
							'cookie': encodeURIComponent(document.cookie),
							'gid': id,
							'_wpnonce': nonce
						},
						function(response) {
							window.location.reload();
						});
						return false;
					} );
			    },
			    error: function(data) {
			        //alert(data.responseText);
			    }
			});
		})
	});
};

var $ = jQuery;
$(document).ready(function(){
	var elms = $("#groups-list a");
	popover_group(elms);
});

var do_group_join_modal = function(id) {
	$('#group-join').modal('show').on('shown', function() {
		jQuery(this).attr('gid', id);
	});
};

$('button[name="group-join-button"]').click(function(){
	var nonce = _wp_nonce_join_group;
	var thelink = jq(this);

	jq.post( ajaxurl, {
			action: 'joinleave_group',
			'cookie': encodeURIComponent(document.cookie),
			'gid': jq('#group-join').attr('gid'),
			'join_year': jq('#group_join_year').val(),
			'_wpnonce': nonce
		},
		function(response){
			var parentdiv = jq('#groupbutton-' + jq('#group-join').attr('gid'));

			if (!jq('body.directory').length)
				location.href = location.href;
			else {
				jq(parentdiv).fadeOut(200,
					function() {
						parentdiv.fadeIn(200).html(response);
					}
					);
			}
		}
	);
});

/**
 * PPY popover
 *
 * @time 2013-04-30
 * @author Lujun
 */

//console.log(_member.id);

(function($){
	$("a[data-toggle=ppypop-member]").each( function(){
		var id = $(this).data('id');
		if ( id == _member.id ){
			$(this).tooltip({
				placement: 'top',
				title: 'This is you'
			});
		} else {
			$(this).ppypop({},function(){
				$.ajax({
				    url: '/wp-admin/admin-ajax.php',
				    type: 'post',
				    data: {
				        'action': 'get_profile',
				        'user_id': id
				    },
				    success: function(dataObj) {
				    	data = $.parseJSON(dataObj);
				        var content = '';
						var btn = '';

						if (data.is_friend == 'is_friend')
						    btn = '<li><a class="btn btn-mini btn-block friend_link" href="' + data.friend_action_url + '" title="Remove Pengyou"><i class="icon-minus"></i> Remove Pengyou</a></li>';
						else if (data.is_friend == 'not_friends')
							btn = '<li><a class="btn btn-mini btn-block btn-danger friend_link" href="' + data.friend_action_url + '" title="Add Pengyou"><i class="icon-plus icon-white"></i> Add Pengyou</a></li>';
						else if (data.is_friend == 'pending')
						    btn = '<li><a class="btn btn-mini btn-block friend_link" href="' + data.friend_action_url + '" title="Cancel Friendship Request"><i class="icon-minus"></i> Cancel Request</a></li>';
						
				        content += '<div class="pop-inner pop-member">\
										<div class="media">\
											<div class="pull-left"><a class="thumbnail" href=""><img src="'+data.avatar+'"></a></div>\
											<div class="media-body">\
												<h5 class="media-heading"><a class="link-blue" href="/members/'+data.user_login+'/">'+data.firstname+' '+data.lastname+'</a></h5>\
												<p class="muted">' + data.city_name + '<br/>' + data.work_title + '<br><a class="link-blue" title="" href="/groups/' + data.group_slug + '">' + data.group_name + '</a></p>\
											</div>\
										</div>\
										<div class="intor"><p>'+data.headline+'</p></div>\
										<ul class="unstyled list-pop-member">\
											' + btn + ' \
											<li><a class="btn btn-mini btn-block" href="' + data.send_msg_url + '"><i class="icon-envelope"></i> Send Message</a></li>\
										</ul>\
									</div>';
						$('#ppypop').find('.popover-content').html(content);
						$('#ppypop .friend_link').bind('click', function() {
							var url = jQuery(this).attr('href');
							var nonce = url.match(/_wpnonce=([^&]+)/)[1];

							jq.post( ajaxurl, {
								action: 'addremove_friend',
								'cookie': encodeURIComponent(document.cookie),
								'fid': data.user_id,
								'_wpnonce': nonce
							},
							function(response) {
								jq('#ppypop .list-pop-member li:first').html(response);
							});
							return false;
						});
				    },
				    error: function(data) {
				        //alert(data.responseText);
				    }
				});
			});
		}
	});

})(jQuery);
