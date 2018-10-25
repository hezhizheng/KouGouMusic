<?php
/**
 * Created by PhpStorm.
 * User: DexterHo
 * Date: 2017/8/22
 * Time: 23:02
 */

namespace DexterHo\kuGouMusic;


use Result\MusicInfo\MusicInfo;

class KGMusic
{

    private static $search_url = 'http://mobilecdn.kugou.com/api/v3/search/song';  //搜索的API

    private static $get_music_info_url = 'http://www.kugou.com/yy/index.php'; // 获取音乐信息的API

    private static $get_music_info_singer = 'http://so.service.kugou.com/get/complex'; //歌手名

    private static $get_music_info_list = 'http://songsearch.kugou.com/song_search_v2'; // all


    private static $format = 'json';  //搜歌的数据结构的类型

    private static $page = 1;

    private static $pagesize = 9999999;

    private static $r = 'play/getdata';  //酷狗要的参数，必须



    /**
     * 根据关键字获取搜索到的列表
     *
     * @param $keyword
     * @return mixed
     */
    public static function getMusicList($keyword)
    {
        $search_params = [
            'format' => self::$format,
            'keyword' => $keyword,
            'page' => self::$page,
            'pagesize' => self::$pagesize,
        ];

        $search_result = self::curlApi(self::$search_url,$search_params);

        $data = json_decode($search_result);

        return $data;
    }

    /**
     * 根据关键字获取hash跟AlbumId
     *
     * @param $keyword
     * @return array
     */
    public static function getHashAlbumId($keyword)
    {
        $search_params = [
            'format' => self::$format,
            'keyword' => $keyword,
            'page' => self::$page,
            'pagesize' => self::$pagesize,
        ];

        $search_result =  self::curlApi(self::$search_url,$search_params);

        $data = json_decode($search_result);

        $audio_id = $data->data->info[0]->audio_id;

        $hash = $data->data->info[0]->hash;

        return ['album_id' => $audio_id, 'hash' => $hash];
    }

    /**
     * 根据hash、album_id，获取音乐的详细信息（包括实际地址）
     *
     * @param $hash
     * @param $album_id
     * @return mixed
     */
    public static function getMusicInfoForHashAlbumId($hash , $album_id )
    {
        $params = [
            'r' => self::$r,
            'hash' => $hash,
            'album_id' => $album_id
        ];

        $music_info = self::curlApi(self::$get_music_info_url,$params);

        $result = json_decode($music_info);

        return $result;
    }

    /**
     * 根据'http://www.kugou.com/yy/index.php/......'的地址直接获取音乐信息(包含实际播放地址)
     * todo 这里酷狗有时会变数据结构 wtf
     *
     * @param $url
     * @return MusicInfo
     */
    public static function getMusicInfoForKuGouUrl($url)
    {
        $result = self::curlApi($url);

        return $result;
    }


    /**
     * 下载酷音乐，打开网页版酷狗官网抓取即可。
     *
     * @param string $mp3_url
     * @param string $artist //歌手名
     * @param string $title // 歌曲名
     * @return int
     */
    public static function downloadMusic($mp3_url , $artist , $title)
    {
        header("Content-length:");
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.$artist.' - '.$title.'.mp3'.'"');
        return readfile($mp3_url);

    }


    /**
     * 新版搜歌API
     *
     * @param $keyword
     * @param int $page
     * @param int $pagesize
     * @return bool|mixed
     */
    public static function getMusicInfoList($keyword, $page=1, $pagesize=30)
    {
        $params = [
            'keyword' => $keyword,
            'page' => $page,
            'pagesize' => $pagesize,
            'userid' => -1,
            'clientver' => null,
            'platform' => 'WebFilter',
            'tag' => 'em',
            'filter' => 2,
            'iscorrection' => 1,
            'privilege_filter' => 0,
        ];

        $result = self::curlApi(self::$get_music_info_list,$params);

        $result = json_decode($result ,true);

        return $result;
        
    }

    /**
     * 检mp3的地址是否失效
     *
     * @param $mp3_url
     * @return bool
     */
    public static function checkMp3UrlIsValid($mp3_url)
    {
        $status = get_headers($mp3_url)[0];

        if (  strstr($status,"200") ) return true;

        return false;

    }

    /**
     * @param string $url 请求网址
     * @param bool $params 请求参数
     * @param int $ispost 请求方式
     * @param int $https https协议
     * @return bool|mixed
     */
    private static function curlApi($url, $params = false, $ispost = 0, $https = 0)
    {
        $httpInfo = array();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2272.118 Safari/537.36');
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if ($https) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); // 对认证证书来源的检查
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); // 从证书中检查SSL加密算法是否存在
        }
        if ($ispost) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
            curl_setopt($ch, CURLOPT_URL, $url);
        } else {
            if ($params) {
                if (is_array($params)) {
                    $params = http_build_query($params);
                }
                curl_setopt($ch, CURLOPT_URL, $url . '?' . $params);
            } else {
                curl_setopt($ch, CURLOPT_URL, $url);
            }
        }

        $response = curl_exec($ch);

        if ($response === FALSE) {
            //echo "cURL Error: " . curl_error($ch);
            return false;
        }
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $httpInfo = array_merge($httpInfo, curl_getinfo($ch));
        curl_close($ch);
        return $response;
    }
    // 发送请求
    //$result = self::curl('网址', '参数', true);
    // 收到的数据需要转化一下
    //$json = json_decode($result);


}