console.log('read tag js loaded');
var socket = io.connect('http://localhost:3000');

socket.on('tag_put', function (data) {
    console.log(data);
    var sidebar = $('.site-sidebar .sidebar-areas')[0];

    if (OBJECTS.hasOwnProperty(data)){
        var event = new CustomEvent('object_recognized', {detail: {
            'pattern': data,
            'object_url': OBJECTS[data]

        }});
        $('.site-sidebar').data('state', 'info');
        sidebar.dispatchEvent(event);

    } else {
        var event = new CustomEvent('no_object_recognized', {detail: {
            'pattern': data,
            'object_url': null,
            'touch_map': [],

        }});
        sidebar.dispatchEvent(event);

    }

});

socket.on('tag_remove', function (data) {
    console.log('tag removed');
    var sidebar = $('.site-sidebar .sidebar-areas')[0];
    var event = new CustomEvent('no_object_recognized', {detail: {
        'pattern': data,
        'object_url': null,
        'touch_map': [],

    }});
    sidebar.dispatchEvent(event);

});