define(['jquery'], function($) {
 
    return {
        init: function() {
        	var element = document.getElementById('page');
        	var notifications = document.createElement('div');
        	notifications.id = 'notifications'
        	notifications.innerHTML = '<div class="content"/>';
        	element.parentNode.insertBefore(notifications, element.nextSibling);
        	require(['core/templates'], function(templates) {
        	    // This will be the context for our template. So {{name}} in the template will resolve to "Tweety bird".
        	    var context = { notifications: {html: '<div class="notification"><p>TestVars<p></div>' }};
        	 
        	    // This will call the function to load and render our template. 
        	    templates.render('tool_gnotify/notifications', context)
        	 
        	    // It returns a promise that needs to be resoved.
        	            .then(function(html, js) {
        	                // Here eventually I have my compiled template, and any javascript that it generated.
        	                // The templates object has append, prepend and replace functions.
        	                templates.appendNodeContents('#notifications .content', html, js);
        	            }).fail(function(ex) {
        	                // Deal with this exception (I recommend core/notify exception function for this).
        	            });
        	});
        }
    };
});
