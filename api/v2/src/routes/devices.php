<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;



// Tüm Cihazları dönüyor
$app->get('/devices', function (Request $request, Response $response) {
   $devices = $request->getAttribute('devices');
  return $response
      ->withStatus(200)
      ->withHeader("Content-Type", 'application/json')
      ->withJson($devices);

});

// cihaz detayi..
$app->get('/device/{id}', function (Request $request, Response $response) {
     $devices = $request->getAttribute('devices');
       $id = $request->getAttribute("id");
     foreach ($devices as  $device) {
       if($device->device_id  == $id)
       return $response
           ->withStatus(200)
           ->withHeader("Content-Type", 'application/json')
           ->withJson($device);
     }
});

// cihaz tag detayi..
$app->get('/device/{id}/{tag}', function (Request $request, Response $response) {
     $devices = $request->getAttribute('devices');
       $id = $request->getAttribute("id");
         $tag = $request->getAttribute("tag");
     foreach ($devices as  $device) {
       if($device->device_id  == $id){
         if(isset($device->tags[$tag])){
           return $response
               ->withStatus(200)
               ->withHeader("Content-Type", 'application/json')
               ->withJson($device->tags[$tag]);
         }else{
          return $response->withStatus(403);
         }

       }

     }
});
// ekle...
$app->post('/devicedatas', function (Request $request, Response $response) {

    $title      = $request->getParam("title");
    $couponCode = $request->getParam("couponCode");
    $price      = $request->getParam("price");

    $db = new Db();
    try{
        $db = $db->connect();
        $statement = "INSERT INTO courses (title,couponCode, price) VALUES(:title, :couponCode, :price)";
        $prepare = $db->prepare($statement);

        $prepare->bindParam("title", $title);
        $prepare->bindParam("couponCode", $couponCode);
        $prepare->bindParam("price", $price);

        $course = $prepare->execute();

        if($course){
            return $response
                ->withStatus(200)
                ->withHeader("Content-Type", 'application/json')
                ->withJson(array(
                    "text"  => "Kurs başarılı bir şekilde eklenmiştir.."
                ));

        } else {
            return $response
                ->withStatus(500)
                ->withHeader("Content-Type", 'application/json')
                ->withJson(array(
                    "error" => array(
                        "text"  => "Ekleme işlemi sırasında bir problem oluştu."
                    )
                ));
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
    $db = null;
});

//  güncelle..
$app->put('/device/{id}', function (Request $request, Response $response) {

    $id = $request->getAttribute("id");


});

// sil..
$app->delete('/course/{id}', function (Request $request, Response $response) {

    $id      = $request->getAttribute("id");


});
