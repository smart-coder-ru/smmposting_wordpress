<?php
class SMMP_System {
    public function check($action = 'before') {
        $result = array();
        if ($action == 'before') {
            if (!$this->checkCurl()) {
                $result['curl'] = false;
            }
        }
        return empty($result) ? true : $result;
    }

    private function checkCurl() {
        return function_exists('curl_version');
    }
}