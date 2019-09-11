<?php
function tool_gnotify_before_footer() {
    
    global $PAGE, $DB;
    if (!isloggedin() || isguestuser()) {
        return;
    }
    if (in_array($PAGE->pagelayout, ['maintenance', 'print', 'redirect'])) {
        // Do not try to show notifications inside iframe, in maintenance mode,
        // when printing, or during redirects.
        return;
    }
    //$DB->get_records_select('gnotify_temp_ins',':time between fromdate todate',['time' => time()]);
    //$PAGE->requires->js_call_amd('tool_gnotify/notification', 'init', );
}
