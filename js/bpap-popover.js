/**
 * popover
 *
 * @time 2013-04-30
 * @author Lujun
 */

; (function($) {
    'use strict';

    $.fn.popover = function(opts, callback) {
        var defaults = {
            times: 500
        };
        var settings = $.extend({},
        defaults, opts),
        el = $(this),
        timer,
        container = $('<div id="popover"><div class="popover right"><div class="arrow"></div><div class="popover-content"><div class="load"><span class="loader loader-large"></span> Loading...</div></div></div></div>'),
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

/**
 * popover
 *
 * @time 2013-04-30
 * @author Lujun
 */

//console.log(_member.id);

(function($){
	$("#groups-list .item-avatar a").each( function() {
		var link = $(this).attr('href');
		var reg = new RegExp("http://.*?/groups/(.*)/");
		var matchs = link.match(reg); 
		var slug = null;

		if (matchs != null)
			slug = matchs[1];

		$(this).popover({},function(){
			$.ajax({
			    url: '/wp-admin/admin-ajax.php',
			    type: 'post',
			    data: {
			        'action': 'get_group_popover_box',
			        'group_slug': slug
			    },
			    success: function(response) {
					$('#popover').find('.popover-content').html(response);
			    },
			    error: function(data) {
			    }
			});
		})
	});
	$("#members-list .item-avatar img, .comment-author img").each( function() {
		//var link = $(this).attr('href');
		//var reg = new RegExp("http://.*?/members/(.*)/");
		//var matchs = link.match(reg); 
		//var name = null;

		//if (matchs != null)
		//	name = matchs[1];
		var clazz = $(this).attr('class');
		var reg = new RegExp("user-(.*)-avatar");
		var matchs = clazz.match(reg);
		var id = -1;

		if (matchs != null)
			id = matchs[1];

		if ( id == _member.id ){
		//	$(this).tooltip({
		//		placement: 'top',
		//		title: 'This is you'
		//	});
		} else {
			$(this).popover({},function(){
				$.ajax({
				    url: '/wp-admin/admin-ajax.php',
				    type: 'post',
				    data: {
				        'action': 'get_member_popover_box',
				        'id': id
				    },
				    success: function(response) {
						$('#popover').find('.popover-content').html(response);
				    },
				    error: function(data) {
				    }
				});
			});
		}
	});
})(jQuery);