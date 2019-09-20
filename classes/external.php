<?php
class tool_gnotify_external extends external_api {

    public static function acknoledge($id) {
        global $USER,$DB;
        if($USER) {
            if($USER->id) {
                if($DB->get_record('gnotify_tpl_ins', ['id' => $id]) && !$DB->get_record('gnotify_tpl_ins_ack', ['insid' => $id, 'userid' => $USER->id])) {
                    $dataobject = new stdClass();
                    $dataobject->insid = $id;
                    $dataobject->userid = $USER->id;
                    $DB->insert_record('gnotify_tpl_ins_ack', $dataobject);
                }
            }
        }
    }
    
    public static function acknoledge_parameters() {
        return new external_function_parameters(
            array(
                'id' => new external_value(PARAM_INT, 'id of notificationtemplate')
            )
        );
    }
    
    public static function acknoledge_returns() {
        return null;
    }

}