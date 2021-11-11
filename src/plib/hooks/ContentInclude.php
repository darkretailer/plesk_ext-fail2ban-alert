<?php

class Modules_fail2banalert_ContentInclude extends pm_Hook_ContentInclude {
    
    function f2bcheckIP($ip) {
        $api_result = pm_ApiCli::call('ip_ban', ['--banned']);

        preg_match("/$ip(.*)/", $api_result['stdout'], $result['Jails']);
        $result['Jails'] = preg_replace("/$ip/", "", $result['Jails']);
        $result['Jails'] = array_map('trim', $result['Jails']);
        $result['Jails'] = array_unique($result['Jails']);

        return $result;
    }

    function f2bunbanIP($ip) {
        $api_result = "";

        foreach (f2bcheckIP($ip)['Jails'] as $jail) {
            $api_result .= pm_ApiCli::call('ip_ban', ['--unban', "$ip,$jail"]);
        }
        
        return $api_result;
    }

    public function getJsOnReadyContent()
    {
        $msgtype = "";

        if (!isset($_GET['f2bunban'])) {
            $check_result = Modules_fail2banalert_ContentInclude::f2bcheckIP($_SERVER['REMOTE_ADDR']);

            if (count($check_result['Jails']) > 0) {
                $msgtype = 'error';
                $msg = pm_Locale::lmsg('banned', ['ip' => $_SERVER['REMOTE_ADDR']]);
                foreach ($check_result['Jails'] as $jail) {
                    $msg .= pm_Locale::lmsg($jail);
                }
                $msg .= '<a href="?f2bunban">'.pm_Locale::lmsg('link').'</a>';
            }
        } else {
            Modules_fail2banalert_ContentInclude::f2bunbanIP($_SERVER['REMOTE_ADDR']);
            $msgtype = 'info';
            $msg = 'Your IP ('.$_SERVER['REMOTE_ADDR'].') was unbanned.';
        }
    
        if (isset($msg)) {
            return "Jsw.addStatusMessage('$msgtype', '$msg');";
        } else {
            return '';
        }
    }
}
