<?php

function f2bcheckIP($ip) {
    $api_result = pm_ApiCli::call('ip_ban', ['--banned']);

    preg_match("/$ip(.*)/", $api_result['stdout'], $result['Jails']);
    $result['Jails'] = preg_replace("/$ip/", "", $result['Jails']);
    $result['Jails'] = array_map('trim', $result['Jails']);
    $result['Jails'] = array_unique($result['Jails']);

    return $result;
}

function f2bunbanIP($ip) {
    foreach (f2bcheckIP($ip)['Jails'] as $jail) {
        $api_result .= pm_ApiCli::call('ip_ban', ['--unban', "$ip,$jail"]);
    }
    
    return $api_result;
}

class Modules_fail2banalert_ContentInclude extends pm_Hook_ContentInclude {

    public function getJsConfig() {
        //if (!isset($_GET['f2bunban'])) {
            //$check_result = f2bcheckIP($_SERVER['REMOTE_ADDR']);

            //if (count($check_result['Jails']) > 0) {
                //$result['MessageType'] = 'error';
                //$result['Message'] = pm_Locale::lmsg('banned', ['ip' => $_SERVER['REMOTE_ADDR']]);
                //foreach ($check_result['Jails'] as $jail) {
                    //$result['Message'] .= pm_Locale::lmsg($jail);
                //}
                //$result['Message'] .= '<a href="?f2bunban">'.pm_Locale::lmsg('link').'</a>';
            //}
        //} else {
            //f2bunbanIP($_SERVER['REMOTE_ADDR']);
            //$result['MessageType'] = 'info';
            //$result['Message'] = 'Your IP ('.$_SERVER['REMOTE_ADDR'].') was unbanned.';
        //}
        //return $result;

        return '';
    }

    public function getJsOnReadyContent()
    {
        if (!isset($_GET['f2bunban'])) {
            $check_result = f2bcheckIP($_SERVER['REMOTE_ADDR']);

            if (count($check_result['Jails']) > 0) {
                $msgtype = 'error';
                $msg = pm_Locale::lmsg('banned', ['ip' => $_SERVER['REMOTE_ADDR']]);
                foreach ($check_result['Jails'] as $jail) {
                    $msg .= pm_Locale::lmsg($jail);
                }
                $msg .= '<a href="?f2bunban">'.pm_Locale::lmsg('link').'</a>';
            }
        } else {
            f2bunbanIP($_SERVER['REMOTE_ADDR']);
            $msgtype = 'info';
            $msg = 'Your IP ('.$_SERVER['REMOTE_ADDR'].') was unbanned.';
        }
    
        if (isset($msg)) {
            return "Jsw.addStatusMessage('$msgtype', '$msg');";
        } else {
            return '';
        }
    }

    public function getHeadContent()
    {
        return '';
    }

    public function getBodyContent()
    {
        return '';
    }
}
