<?php

$services = array(
    'tool_gnotify_services' => array(
        'functions' => array ('tool_gnotify_acknoledge_notification'),
        'requiredcapability' => '',
        'restrictedusers' =>0,
        'enabled'=>1,
    )
);

$functions = array(
    'tool_gnotify_acknoledge_notification' => array(
        'classname'   => 'tool_gnotify_external',
        'methodname'  => 'acknoledge', // implement this function into the above class
        'classpath'   => 'tool/gnotify/externallib.php',
        'description' => 'Allows users to acknoledge that they have seen a notification',
        'type'        => 'write', // the value is 'write' if your function does any database change, otherwise it is 'read'.
        'ajax'        => true, // true/false if you allow this web service function to be callable via ajax
        'capabilities'  => '',  // TODO capabilities
    )
);