<?php
class m_dao_base{
    
    static function init($staticKey)
    {
        $ret = [];
        $localPath = SEN::static_path($staticKey);
        if(file_exists($localPath)){
            $res = file_get_contents($localPath);
            $json = json_decode($res);
        }else{
            $res = file_get_contents(SEN::REMOTE_URL[$staticKey]);
            $json = json_decode($res);
            file_put_contents($localPath, json_encode($json, JSON_UNESCAPED_UNICODE));
        }
        $ret['version'] = $json->version;
        $ret['season'] = $json->season;
        $ret['time'] = $json->time;
        $ret['data'] = $json->data;
        return $ret;
    }
}