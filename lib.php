<?php
use mod_data\event\record_created;

function tool_gnotify_before_footer() {
    
    global $PAGE, $DB,$USER;
    if (!isloggedin() || isguestuser()) {
        return;
    }
    if (in_array($PAGE->pagelayout, ['maintenance', 'print', 'redirect'])) {
        // Do not try to show notifications inside iframe, in maintenance mode,
        // when printing, or during redirects.
        return;
    }
    $sql= "SELECT g.id,l.content FROM {gnotify_tpl_ins} g, {gnotify_tpl_lang} l WHERE :time between fromdate AND todate AND l.lang = 'en' AND l.tplid = g.tplid AND NOT EXISTS( SELECT 1 FROM {gnotify_tpl_ins_ack} a WHERE g.id=a.insid AND a.userid = :userid)";
    $records = $DB->get_records_sql($sql,['time' => time(), 'userid' => $USER->id]);
    
    if($records) {
        $context = [];
        foreach ($records as $record) {
            $context['notifications'][] = ['html' => format_text($record->content)];
        }
        $PAGE->requires->js_call_amd('tool_gnotify/notification', 'init', $context);
    }
}
