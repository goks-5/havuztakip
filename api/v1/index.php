<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include dirname(__DIR__) . '/config.php';
execute();

function execute()
{
    try {
        $data['get'] = $_GET;
        $data['00-*']  = $_POST ;
        $data['ip']  =  isset($_SERVER['HTTP_CF_CONNECTING_IP']) ? $_SERVER['HTTP_CF_CONNECTING_IP'] : $_SERVER['REMOTE_ADDR'] ;
        $data['ulke']  =  isset($_SERVER['HTTP_CF_IPCOUNTRY']) ? $_SERVER['HTTP_CF_IPCOUNTRY'] :"??" ;
        $data['HTTP_HOST']  = $_SERVER['HTTP_HOST'];
        $data['HTTP_USER_AGENT']  = $_SERVER['HTTP_USER_AGENT'];

        $text = addslashes(json_encode($data, true));
        if (isset($data['get']['mac']) && isset($data['get']['device_id'])) {
            $tanimli = getdata("SELECT id,tags ,last_data,period,updated_at ,tags_last_change from devices where device_id = '{$data['get']['device_id']}' and mac = '{$data['get']['mac']}'");
            if (is_array($tanimli)) {
                if (isset($data['get']['write'])) {
                    $timestamp = strtotime($tanimli[0]['updated_at']) + $tanimli[0]['period'] ;
                $changeTags = array_filter(json_decode($tanimli[0]['tags'], true), function($k) {
                    return $k >= '1000';
                }, ARRAY_FILTER_USE_KEY);
               $changeAt = json_decode($tanimli[0]['tags_last_change'], true);
               if (!is_array($changeAt)) {
                $changeAt = array();
            }
                $changeTags = array_replace( $changeTags,$changeAt );
                 write($tanimli[0]['id'], $data['get']['write'], json_decode($tanimli[0]['last_data'], true), $timestamp <= time(), $changeTags);
                }
                if (isset($data['get']['read'])) {
                    read($tanimli[0]['id'], $data['get']['read']);
                } else {
                    echo "OK";
                }
            } else {
                $data =   getdata("INSERT into undefine_devices (mac,device_id,created_at,updated_at) values('{$data['get']['mac']}','{$data['get']['device_id']}',now(),now()) ON DUPLICATE KEY UPDATE updated_at = now() ", 1);
                header('HTTP/1.0 403 Undefined');
                echo 'Undefined';
            }
            getdata("INSERT into full_data (full_text , created) values ('{$text}' , now())", 1);
        } else {
            $data['post']  = $_POST ;
            $data['hata']  = 1;

            $text = addslashes(json_encode($data, true));
            getdata("INSERT into full_data (full_text , created) values ('{$text}' , now())", 1);
            header("Location: https://enerjiyonetim.com", true, 301);
            //header('HTTP/1.0 401 Unauthorized');
      //echo 'Yetkisiz Giriş';
        }
    } catch (\Exception $e) {
        $text = addslashes(json_encode(['Exception' => 1,'get'=>$_GET,'post'=>$_POST,'e'=>$e], true));
        getdata("INSERT into full_data (full_text , created) values ('{$text}' , now())", 1);
    }
}
function write($id, $data, $old = array(), $ekle = true,  $changeTags = array())
{
    if (count($data) > 0) {
        if (!is_array($old)) {
            $old = array();
        }
        $replace = array_replace($old, $data);
        ksort($replace);
        // ob_flush();
        //  ob_start();
          foreach ($data as $key => $value) {
           if(isset($changeTags[$key + 1000]) && isset($old[$key]) && $old[$key] != $value  )
             $changeTags[$key + 1000] = date('Y-m-d H:i:s');
           }      
        if ($ekle) {
            $sql = "INSERT into device_datas (device_id,data_id,value,created_at) Values ";
            foreach ($data as $key => $value) {
                $sql .= "({$id},{$key},{$value},now() ),";
            }
            $sql = trim($sql, ",") ;
            getdata($sql, 1);

                //  var_dump('ekledi');
            getdata("UPDATE devices set last_data = '". addslashes(json_encode($replace, true)) ."' ,  tags_last_change = '". addslashes(json_encode($changeTags, true)) ."' , last_at = now(), updated_at = now() where id = {$id}", 1);
        } else {
                //  var_dump('*****eklemedi*****');
            getdata("UPDATE devices set last_data = '". addslashes(json_encode($replace, true)) ."' ,  tags_last_change = '". addslashes(json_encode($changeTags, true)) ."' , last_at = now() where id = {$id}", 1);
        }
       //   file_put_contents("dump.txt", ob_get_flush(), FILE_APPEND);
       // ob_end();
    }
}
function read($id, $data)
{
}
function getdata($q, $i = false)
{
    global $config;
    $servername = $config['DB_HOST'];
    $username = $config['DB_USERNAME'];
    $password = $config['DB_PASSWORD'];
    $dbname = $config['DB_DATABASE'];


    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Bağlantı Hatası: " . $conn->connect_error);
    }
    $result = $conn->query($q);
    if ($i) {
        $veri = $conn->insert_id;
    } elseif ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $veri[] = $row;
        }
    } else {
        $veri = false;
    }

    $conn->close();
    return $veri;
}


function pr($yaz)
{
    echo "\n<pre>\n";
    print_r($yaz);
    echo "\n</pre>\n";
}
