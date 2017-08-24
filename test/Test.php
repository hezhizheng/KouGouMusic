<?php
require '../src/KGMusic.php';
use Dexterho\kuGouMusic\KGMusic;

class Test
{
    public function test()
    {
        $url = 'http://www.kugou.com/yy/index.php?r=play/getdata&hash=E3E46049E6CDF3B5F3DCB4F2B5C32C67&album_id=2716220&_=1503418389371';

        $c = KGMusic::getMusicInfoForKuGouUrl($url)->data->img;

        var_dump($c);
	}

    public function update()
    {

        $mp3_url = 'http://fs.web.kugou.com/54420e2f3f55c25c1df050b4c24e6f6b/599eee48/G103/M09/1C/17/R5QEAFlMh1SAaflWADUG6q5XmLo362.mp3';

        $c = KGMusic::checkMp3UrlIsValid($mp3_url);
        var_dump($c);
	}
}