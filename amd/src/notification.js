define(['jquery'], function ($) {
    return {
        init: function (context) {
            var element = document.getElementById('page');
            var notifications = document.createElement('div');
            notifications.id = 'notifications';
            notifications.className = 'row';
            notifications.innerHTML = '<div class="content"/>';
            element.insertBefore(notifications, element.firstChild);
            require(['core/templates'], function (templates) {
                // This will be the context for our template. So {{name}} in the template will resolve to "Tweety bird".
                // This will call the function to load and render our template.
                context['notifications'] = context;
                templates.render('tool_gnotify/notifications', context)
                // It returns a promise that needs to be resoved.
                    .then(function (html, js) {
                        // Here eventually I have my compiled template, and any javascript that it generated.
                        // The templates object has append, prepend and replace functions.
                        templates.appendNodeContents('#notifications .content', html, js);
                    }).fail(function (ex) {
                    templates.setBody(ex.message);
                });
            });
            $.fn.tool_gnotify_acknowledge_notification = function ($id) {
                var notification = document.getElementById($id + '-gnotify');
                notification.hidden = true;
                require(['core/ajax'], function (ajax) {
                    var promises = ajax.call([
                        {methodname: 'tool_gnotify_acknoledge_notification', args: {id: $id}}
                    ]);
                    promises[0].done(function (response) {
                        alert('done' + response);
                    }).fail(function (ex) {
                        // do something with the exception
                        alert('exception' + ex.message);
                    });
                });
            };
        }
    };
});
