<?php

use plugin\saiadmin\utils\Arr;

if (!function_exists('urlToPath')) {
    /**
     * url转为本地地址
     * @param $upload_config
     * @param $url
     * @return string
     */
    function urlToPath($upload_config, $url): string
    {
        $upload_mode = Arr::getConfigValue($upload_config, 'upload_mode');
        $local_root = Arr::getConfigValue($upload_config, 'local_root');
        $local_domain = Arr::getConfigValue($upload_config, 'local_domain');
        $local_uri = Arr::getConfigValue($upload_config, 'local_uri');
        if ($upload_mode == 1) {
            // 本地模式
            $old = $local_domain . $local_uri;
            $url = str_replace($old, $local_root, $url);
        }
        return base_path() . DIRECTORY_SEPARATOR . $url;
    }
}
