<?php

class app_m_dao_base {

    static function init($staticKey, $ifUpdate = false) {
        $localPath = SEN::data_path($staticKey);
        if (!$ifUpdate && file_exists($localPath)) {
            $res = file_get_contents($localPath);
            $dataObj = json_decode($res);
        } else {
            $res = file_get_contents(SEN::REMOTE_URL[$staticKey]);
            $dataObj = json_decode($res);
            file_put_contents($localPath, json_encode($dataObj, JSON_UNESCAPED_UNICODE));
        }
        //转int类型
        foreach ($dataObj->data as $key => $val) {
            foreach ($val as $key2 => $val2) {
                if (filter_var($val2, FILTER_VALIDATE_INT)) {
                    $dataObj->data[$key]->{$key2} = intval($val2);
                }
            }
        }
        return $dataObj;
    }
}