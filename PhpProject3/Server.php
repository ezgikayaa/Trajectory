<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
    <title></title>
</head>
<body>

    <?php
    // Socket işlemleri
    $host = "172.20.10.2";
    $port = 8081;

    set_time_limit(0);

    $socket = socket_create(AF_INET, SOCK_STREAM, 0) or die("Could not create socket\n");

    $res = socket_bind($socket, $host, $port) or die("Could not bind to socket\n");

    $result = socket_listen($socket, 3) or die("Could not set up socket listener\n");

    $spawn = socket_accept($socket) or die("Could not accept incoming connection\n");

    $file = socket_read($spawn, 8192) or die("Could not read input\n");


    $dizi = explode(" ", $file);
    $sayac = 0;


    for ($i = 0; $i < count($dizi) / 2; $i++) {

        $lat[$i] = $dizi[$sayac];
        $long[$i] = $dizi[$sayac + 1];

        $sayac+=2;
    }
    $lat_lenght = count($lat);

    for ($i = 0; $i < $lat_lenght; $i++) {
        $point[$i][0] = $lat[$i];
        $point[$i][1] = $long[$i];
    }

    function DikeyUzaklık($ptX, $ptY, $l1x, $l1y, $l2x, $l2y) {
        $islem = 0;
        if ($l2x == $l1x) {
            
            
            $islem = abs($ptX - $l2x);
        } else {
            $eğim = (($l2y - $l1y) / ($l2x - $l1x));
            $deger = (0 - $l1x) * $eğim + $l1y;
            $islem = (abs(($eğim * $ptX) - $ptY + $deger)) /
                    (sqrt($eğim * $eğim + 1));
        }
        return $islem;
    }
    

    function İndirgeme($pointMatris, $epsilon) {

        if (count($pointMatris) < 2) {
            return $pointMatris;
        }

        
        $dmax = 0;
        $index = 0;
        $totalPoints = count($pointMatris);
        for ($i = 1; $i < ($totalPoints - 1); $i++) {
            $d = DikeyUzaklık(
                    $pointMatris[$i][0], $pointMatris[$i][1], $pointMatris[0][0], $pointMatris[0][1], $pointMatris[$totalPoints - 1][0], $pointMatris[$totalPoints - 1][1]);

            if ($d > $dmax) {
                $index = $i;
                $dmax = $d;
            }
        }

        $resultMatris = array();

      
        if ($dmax >= $epsilon) {
           
            $Results1 = İndirgeme(
                    array_slice($pointMatris, 0, $index + 1), $epsilon);
            $Results2 = İndirgeme(
                    array_slice($pointMatris, $index, $totalPoints - $index), $epsilon);

            
            $resultMatris = array_merge(array_slice($Results1, 0, count($Results1) - 1), array_slice($Results2, 0, count($Results2)));
        } else {
            $resultMatris = array($pointMatris[0], $pointMatris[$totalPoints - 1]);
        }
        
        return $resultMatris;
    }
 
    $indirgeme = İndirgeme($point, 0.000005);

    $string = "";
    
    //İndirgenmiş matrisi stringe çevirme

    for ($i = 0; $i < count($indirgeme); $i++) {
        $string .= strval($indirgeme[$i][0]) . " ";
        if ($i == count($indirgeme) - 1) {
            $string .= strval($indirgeme[$i][1]);
        } else {
            $string .= strval($indirgeme[$i][1]) . " ";
        }
    }


    echo "<br>";
    echo "<br>";
    echo "<br>";
    
    socket_write($spawn, $string, strlen($string)) or die("Could not write output\n");
    sleep(1);
    $x1=$port;
    
    
    

    class Node {

        public $NW, $NE, $SE, $SW;
        public $x, $y;

        function __construct($x = null, $y = null) {
            $this->x = $x;
            $this->y = $y;
        }

        public function init() {
            $this->NW = new Node();
            $this->SW = new Node();
            $this->SE = new Node();
            $this->NE = new Node();
        }

    }
    

    $spawn1 = socket_accept($socket) or die("Could not accept incoming connection\n");

    $gelen = socket_read($spawn1, 8192) or die("Could not read input\n");
    
    socket_write($spawn1, $string, strlen($string)) or die("Could not write output\n");
    sleep(1);
    $y1=$port;
    $rect = explode(" ", $gelen);

    print_r($rect);

    
    if ($rect[2] == 1) {

        print_r($dizi);

        $root = new Node($lat[0], $long[0]);
        $root->init();
//Ağaca veri ekleme
        function insert($x, $y) {
            global $root;


            $root = insertt($root, $x, $y);
        }

        function insertt(Node $h, $x, $y) {

            if ($h->x == null) {

                $h->init();
                $h->x = $x;
                $h->y = $y;

                return $h;
            } else if (less($x, $h->x) && less($y, $h->y)) { //SW
                insertt($h->SW, $x, $y);
            } else if (less($x, $h->x) && !less($y, $h->y)) {//SE
                insertt($h->SE, $x, $y);
            } else if (!less($x, $h->x) && !less($y, $h->y)) {//NE
                insertt($h->NE, $x, $y);
            } else if (!less($x, $h->x) && less($y, $h->y)) {//NW
                insertt($h->NW, $x, $y);
            }
            return $h;
        }

        function less($k1, $k2) {
            return $k1 < $k2;
        }

        for ($i = 1; $i < count($lat); $i++) {
            insert($lat[$i], $long[$i]);
        }

        function search($x, $y) {
            global $root;
            searchh($root, $x, $y);
        }

        $dizilat;
        $dizilng;
        $i = 0;

        function searchh(Node $h, $x, $y) {
            global $dizilat;
            global $dizilng;
            global $i;
            global $rect;
            global $x1;
            global $y1;

            if ($h->x < ($rect[0] + 0.0005) && $h->x > ($rect[0] - 0.0005) && $h->y < ($rect[1] + 0.0005) && $h->y > ($rect[1] - 0.0005)) {
                $dizilat[$i] = $h->x;
                $dizilng[$i] = $h->y;
                $i++;
            }if ($h->x == null) {

                return;
            }
            if (less($x, $x1) && less($y, $y1))  //SW
            searchh($h->SW, $x, $y);
            if (less($x, $x1) && !less($y1, $y)) //SE
            searchh($h->SE, $x, $y);
            if (!less($x1, $x) && !less($y1, $y)) //NE
            searchh($h->NE, $x, $y);
            if (!less($x1, $x) && less($y, $y1)) //NW
            searchh($h->NW, $x, $y);
        }
//Karenin kordinatlarını aramaya yollama
        search($rect[0], $rect[1]);
//Karenin içindeki Noktaları cliente yollama
        $gonder1 = implode(" ", $dizilat);
        $gonder2 = implode(" ", $dizilng);
        $gonder3 = implode(" ", $rect);

        socket_write($spawn1, $gonder1, strlen($gonder1)) or die("Could not write output\n");
        sleep(1);
        socket_write($spawn1, $gonder2, strlen($gonder2)) or die("Could not write output\n");
        sleep(1);
        socket_write($spawn1, $gonder3, strlen($gonder3)) or die("Could not write output\n");
        sleep(1);
    }

    if ($rect[2] == 2) {

        print_r($indirgeme);

        $root2 = new Node($indirgeme[0][0], $indirgeme[0][1]);
        $root2->init();
        
        //Ağaca veri ekleme
        
        function insert2($x, $y) {
            global $root2;

            $root2 = insertt($root2, $x, $y);
        }

        function insertt(Node $h, $x, $y) {

            if ($h->x == null) {

                $h->init();
                $h->x = $x;
                $h->y = $y;

                return $h;
            } else if (less($x, $h->x) && less($y, $h->y)) { //SW
                insertt($h->SW, $x, $y);
            } else if (less($x, $h->x) && !less($y, $h->y)) {//SE
                insertt($h->SE, $x, $y);
            } else if (!less($x, $h->x) && !less($y, $h->y)) {//NE
                insertt($h->NE, $x, $y);
            } else if (!less($x, $h->x) && less($y, $h->y)) {//NW
                insertt($h->NW, $x, $y);
            }
            return $h;
        }

        function less($k1, $k2) {
            return $k1 < $k2;
        }

        for ($i = 1; $i < count($indirgeme); $i++) {
            insert2($indirgeme[$i][0], $indirgeme[$i][1]);
        }

        function search2($x, $y) {
            global $root2;

            searchh2($root2, $x, $y);
        }

        $dizilat2;
        $dizilng2;
        $j = 0;
        
        //Ağaç içinde gezinme

        function searchh2(Node $h, $x, $y) {
            global $dizilat2;
            global $dizilng2;
            global $j;
            global $rect;
            global $x1;
            global $y1;


            if ($h->x < ($rect[0] + 0.0005) && $h->x > ($rect[0] - 0.0005) && $h->y < ($rect[1] + 0.0005) && $h->y > ($rect[1] - 0.0005)) {
                $dizilat2[$j] = $h->x;
                $dizilng2[$j] = $h->y;
                $j++;
            }if ($h->x == null) {

                return;
            }
            if (less($x, $x1) && less($y, $y1))  //SW
            searchh2($h->SW, $x, $y);
            if (less($x, $x1) && !less($y1, $y)) //SE
            searchh2($h->SE, $x, $y);
            if (!less($x1, $x) && !less($y1, $y)) //NE
            searchh2($h->NE, $x, $y);
            if (!less($x1, $x) && less($y, $y1)) //NW
            searchh2($h->NW, $x, $y);
        }
        
        //Karenin kordinatlarını aramaya gönderme
        search2($rect[0], $rect[1]);

        //Karenin içindeki Noktaları cliente yollama
        $gonder4 = implode(" ", $dizilat2);
        $gonder5 = implode(" ", $dizilng2);
        $gonder3 = implode(" ", $rect);
        socket_write($spawn1, $gonder4, strlen($gonder4)) or die("Could not write output\n");
        sleep(1);
        socket_write($spawn1, $gonder5, strlen($gonder5)) or die("Could not write output\n");
        sleep(1);
        socket_write($spawn1, $gonder3, strlen($gonder3)) or die("Could not write output\n");
        sleep(1);
    }



    socket_close($spawn);
    socket_close($spawn1);

    socket_close($socket);
    ?> 


</body>
</html>



