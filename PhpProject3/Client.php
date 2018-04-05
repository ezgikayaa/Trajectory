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
    <p id="demo">
    <form action="Client.php " method="post">
        <textarea id="myTextarea"> </textarea>
        <input type=hidden id="myTextarea2" name="text1"/>
        <input type=hidden id="myTextarea3" name="text2"/>
        <input type="submit" value="Ham Veri" name="but" />
        <input type="submit" value="İndirgenmiş Veri" name="but2" />
    </form>

<div id="map" style="width:100%;height:290px"></div>
<br>
<div id="map2" style="width:100%;height:290px"></div>
<script>
<?php
//Dosyadan veriyi string olarak çekme
$file = file_get_contents("C:\Users\EZGİ\Desktop\latlong.txt", FILE_IGNORE_NEW_LINES);

$dizi = explode(",", $file);
$sayac = 0;
$sayac3 = 0;

for ($i = 0; $i < count($dizi); $i++) {
    if ($i % 7 == 0) {

        $lat[$sayac] = $dizi[$i];
        $lng[$sayac] = $dizi[$i + 1];
        $sayac++;
    }
}
$lat_lenght = count($lat);

for ($i = 0; $i < $lat_lenght; $i++) {
    $gonder[$sayac3] = $lat[$i];
    $gonder[$sayac3 + 1] = $lng[$i];
    $sayac3+=2;
}
$gonder2 = implode(" ", $gonder);

$host = "127.0.0.1";
$port = 8081;
$a = 0;
$c = 0;

// Socket oluşturma

$socket = socket_create(AF_INET, SOCK_STREAM, 0) or die("Could not create socket\n");

$res = socket_connect($socket, $host, $port) or die("Could not connect to server\n");

//Javascriptten phpye veri post etme
if (isset($_POST['but'])) {
    global $a;
    $a = 1;
    $gond = $_POST['text1'] . " 1";

    socket_write($socket, $gond, strlen($gond)) or die("Could not send data to server\n");
    sleep(1);
    $a = 2;
}
//Javascriptten phpye veri post etme
if (isset($_POST['but2'])) {
    global $c;
    $c = 1;
    $gond2 = $_POST['text2'] . " 2";

    socket_write($socket, $gond2, strlen($gond2)) or die("Could not send data to server\n");
    sleep(1);
    $c = 2;
}

if ($a == 0 && $c == 0) {

    socket_write($socket, $gonder2, strlen($gonder2)) or die("Could not send data to server\n");
    sleep(1);
}

//indirgenmiş olarak gelen veri
$result = socket_read($socket, 8192) or die("Could not read server response\n");

$dizi2 = explode(" ", $result);

$sayac2 = 0;

for ($i = 0; $i < count($dizi2) / 2; $i++) {

    $lat2[$i] = $dizi2[$sayac2];
    $lng2[$i] = $dizi2[$sayac2 + 1];
    $sayac2+=2;
}
$lat2_lenght = count($lat2);

$result3 = "";
$result4 = "";
$result6 = "";
$res1 = "";
$res2 = "";
//Kare içinde gelen kordinatlar (Ham veri)
if ($a == 2) {
    global $result3;
    global $result4;
    global $result6;
    global $res1;
    global $res2;
    $result1 = socket_read($socket, 8192) or die("Could not read server response\n");
    $result2 = socket_read($socket, 8192) or die("Could not read server response\n");
    $result5 = socket_read($socket, 8192) or die("Could not read server response\n");
    $result3 = explode(" ", $result1);
    $result4 = explode(" ", $result2);
    $result6 = explode(" ", $result5);
    $res1 = $result6[0];
    $res2 = $result6[1];
}
//Kare içinde gelen kordinatlar (İndirgenmiş  veri)
if ($c == 2) {
    global $result3;
    global $result4;
    global $result6;
    global $res1;
    global $res2;
    $result1 = socket_read($socket, 8192) or die("Could not read server response\n");
    $result2 = socket_read($socket, 8192) or die("Could not read server response\n");
    $result5 = socket_read($socket, 8192) or die("Could not read server response\n");
    $result3 = explode(" ", $result1);
    $result4 = explode(" ", $result2);
    $result6 = explode(" ", $result5);
    $res1 = $result6[0];
    $res2 = $result6[1];
}


$b = $a;
$d = $c;
$yaz_lenght = count($result3);



// close socket
socket_close($socket);
?>


    function initMap() {

        //json ile alınan veri
        var lat = <?php echo json_encode($lat) ?>;
        var lng = <?php echo json_encode($lng) ?>;
        var lat2 = <?php echo json_encode($lat2) ?>;
        var lng2 = <?php echo json_encode($lng2) ?>;
        var lat_lenght = <?php echo json_encode($lat_lenght) ?>;
        var lat2_lenght = <?php echo json_encode($lat2_lenght) ?>;
        var yaz_lenght = <?php echo json_encode($yaz_lenght) ?>;
        var yaz = <?php echo json_encode($result3) ?>;
        var yaz2 = <?php echo json_encode($result4) ?>;
        var a = <?php echo json_encode($b) ?>;
        var c = <?php echo json_encode($d) ?>;
        var rectlat = <?php echo json_encode($res1) ?>;
        var rectlng = <?php echo json_encode($res2) ?>;
        var string = "";
        var oran;


        //Diziler
        var point = new Array(lat_lenght);
        var point2 = new Array(lat2_lenght);
        var point3 = new Array(yaz_lenght);
        var marker = new Array(lat_lenght);
        var marker2 = new Array(lat2_lenght);
        var marker3 = new Array(yaz_lenght);
        var marker4 = new Array(yaz_lenght);
        var flightPath = new Array(lat_lenght);
        var flightPath2 = new Array(lat2_lenght);

        //Kordinatların noktalara eklenmesi.
        for (i = 0; i < lat_lenght; i++) {
            point[i] = new google.maps.LatLng(lat[i], lng[i]);
        }
        for (i = 0; i < lat2_lenght; i++) {
            point2[i] = new google.maps.LatLng(lat2[i], lng2[i]);
        }
        for (i = 0; i < yaz_lenght; i++) {
            point3[i] = new google.maps.LatLng(yaz[i], yaz2[i]);
        }

        //Oranın hesaplanması
        oran = (1 - (lat2_lenght / lat_lenght)) * 100;
        string = "Ham veri = " + lat_lenght + "\nİndirgenmiş veri = " + lat2_lenght + "\nİndirgenme Oranı = " + oran;

        document.getElementById("myTextarea").value = string;
        //Harita

        var map = new google.maps.Map(document.getElementById('map'), {
            zoom: 16,
            center: point[0],
            mapTypeId: "terrain"
        });

        var map2 = new google.maps.Map(document.getElementById('map2'), {
            zoom: 16,
            center: point2[0],
            mapTypeId: "terrain"
        });



        //Noktaların haritada gösterimi
        for (i = 0; i < lat_lenght; i++) {

            marker[i] = new google.maps.Marker({
                position: point[i],
                icon: "flag.png"
            });
            marker[i].setMap(map);

        }


        for (i = 0; i < lat_lenght; i++) {

            marker2[i] = new google.maps.Marker({
                position: point2[i],
                icon: "flag.png"
            });
            marker2[i].setMap(map2);

        }



        //Çizgilerin haritada gösterimi
        flightPath = new google.maps.Polyline({
            path: point,
            strokeColor: "#000000",
            strokeOpacity: 0.8,
            strokeWeight: 2
        });

        flightPath2 = new google.maps.Polyline({
            path: point2,
            strokeColor: "#000000",
            strokeOpacity: 0.8,
            strokeWeight: 2
        });

        flightPath.setMap(map);
        flightPath2.setMap(map2);


        //Kare çizme (Ham veri)
        function DrawRect(lat, lng) {
            var rectangle = new google.maps.Rectangle({
                strokeColor: '#FF0000',
                strokeOpacity: 1,
                strokeWeight: 2,
                fillColor: '#000000',
                fillOpacity: 0,
                map: map,
                bounds: {
                    north: lat + 0.0005,
                    south: lat - 0.0005,
                    east: lng + 0.0005,
                    west: lng - 0.0005
                }
            });
        }

        //Kare çizme (İnsdirgenmiş veri)
        function DrawRect2(lat, lng) {
            var rectangle = new google.maps.Rectangle({
                strokeColor: '#FF0000',
                strokeOpacity: 1,
                strokeWeight: 2,
                fillColor: '#000000',
                fillOpacity: 0,
                map: map2,
                bounds: {
                    north: lat + 0.0005,
                    south: lat - 0.0005,
                    east: lng + 0.0005,
                    west: lng - 0.0005
                }
            });
        }

        //Kare içi kordinatların markerları (Ham veri)
        if (a == 2) {

            for (i = 0; i < yaz_lenght; i++) {

                marker3[i] = new google.maps.Marker({
                    position: point3[i],
                    icon: "flagblue.png"
                });
                marker3[i].setMap(map);
            }
            DrawRect(parseFloat(rectlat), parseFloat(rectlng));
        }

        //Kare içi kordinatların markerları(İndirgenmiş veri)
        if (c == 2) {

            for (i = 0; i < yaz_lenght; i++) {

                marker4[i] = new google.maps.Marker({
                    position: point3[i],
                    icon: "flagblue.png"
                });
                marker4[i].setMap(map2);


            }
            DrawRect2(parseFloat(rectlat), parseFloat(rectlng));
        }

        var str = "";
        map.addListener('click', function (event) {

            //Tıklanılan yerin kordinatları (Ham veri)
            DrawRect(event.latLng.lat(), event.latLng.lng());
            str = event.latLng.lat().toString() + " " + event.latLng.lng().toString();
            document.getElementById("myTextarea2").value = str;

        });
        //Tıklanılan yerin kordinatları (İndirgenmiş veri)
        map2.addListener('click', function (event) {
            DrawRect2(event.latLng.lat(), event.latLng.lng());
            str = event.latLng.lat().toString() + " " + event.latLng.lng().toString();
            document.getElementById("myTextarea3").value = str;
        });

    }

</script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBc3LWPb2QyrDZZsd90oPfIO7NMiXeg4zE&callback=initMap"></script>


</body>
</html>

