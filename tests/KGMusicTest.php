<?php
/**
 * Description:
 * Author: hezhizheng
 * Date: 2018/11/8
 * Time: 13:35
 * Created by Created by Panxsoft.
 */

use DexterHo\kuGouMusic\KGMusic;

class KGMusicTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @uses  KGMusic::getMusicInfoList()
     */
    public function test_get_music_info_list()
    {
        $kg = new KGMusic;
        $keywords = ['张学友', '刘德华'];
        $keyword = array_rand($keywords, 1);
        $result = $kg::getMusicInfoList($keywords[$keyword]);

        $this->assertTrue(is_array($result));
        var_dump($result);

        $this->assertArrayHasKey('data', $result);
    }

    /**
     * @uses  KGMusic::getMusicInfoForHashAlbumId()
     */
    public function test_get_music_info_for_hash_album_id()
    {
        $kg = new KGMusic;

        $hash = 'DF1CAD153586F90827D61D45198561B4';
        $album_id = '970633';

        $result = $kg::getMusicInfoForHashAlbumId($hash, $album_id);

        var_dump($result);
        $this->assertTrue(is_object($result));
        $this->assertObjectHasAttribute('data', $result);
    }
}
