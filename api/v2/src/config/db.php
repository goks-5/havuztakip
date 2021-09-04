<?php


class Db {


    public function connect(){
        $config = parse_ini_file(dirname(dirname(dirname(dirname(__DIR__))))."/.env");
        $dbhost = $config['DB_HOST'];
        $dbuser = $config['DB_USERNAME'];
        $dbpass = $config['DB_PASSWORD'];
        $dbname = $config['DB_DATABASE'];
        $mysql_connection = "mysql:host=$dbhost;dbname=$dbname;charset=utf8";
        $connection = new PDO($mysql_connection,$dbuser,$dbpass);
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $connection;
    }

    public function query($sql,$param = array(),$type=0 ){
        try{
            $connect = $this->connect();
            $qery= $connect->prepare($sql);
            $return = $qery->execute($param);
            switch ($type) {
                case '0':
                    return $return;
                break;
                case '1':
                    return $qery->fetch(PDO::FETCH_OBJ);;
                break;
                case '2':
                    return $qery->fetchAll(PDO::FETCH_OBJ);;
                break;
      }
          }catch(PDOException $e){
              return $response->withJson(
                  array(
                      "error" => array(
                          "text"  => $e->getMessage(),
                          "code"  => $e->getCode()
                      )
                  )
              );
          }
    }
}
