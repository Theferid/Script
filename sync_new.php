
<?php

//logs
$logs = $conn->prepare("SELECT * FROM serviceapi_alert ORDER BY id DESC ");
$logs->execute(array());
$logs = $logs->fetchAll(PDO::FETCH_ASSOC);

if( $logs ): 

    foreach($logs as $log):
        $extra = json_decode($log["servicealert_extra"],true);

        $service_id =  $log["service_id"];
        $change_desc1 = $log["serviceapi_alert"];
        
        
      
        
        $api_old_value = $extra["old"];
        $api_new_value = $extra["new"];
   
       
        $services       = $conn->prepare("SELECT * FROM services WHERE service_id=:service_id ");
        $services       -> execute(array("service_id"=>$service_id));
        $services       = $services->fetchAll(PDO::FETCH_ASSOC);
        $services_extra = json_decode($services[0]["api_detail"] , true);
        
        
         foreach($services as $service):
         
        
         
         if( strpos(  $change_desc1 , "price has been changed") ):
         //profit percentage 
     $new_profit = $service["service_price"]-$api_old_value;
         $new_profit = $new_profit*100;
$new_profit = $new_profit/$api_old_value;
$service_price = $api_new_value;


$final_price = $service_price +($service_price * ($new_profit/100));  

$final_price = round($final_price,3);

//update
$update = $conn->prepare("UPDATE services SET  service_price=:price WHERE service_id=:service ");
            $update->execute(array("service"=>$service_id , "price"=> $final_price ));

  
        elseif(strpos(  $change_desc1 , "minimum amount changed" ) ):

            
            $update = $conn->prepare("UPDATE services SET  service_min=:min WHERE service_id=:service ");
            $update->execute(array("service"=>$service_id , "min"=>$api_new_value ));



        
        
        
        elseif(strpos(  $log["serviceapi_alert"] , "service maximum amount has been changed." ) ):

            
            $update = $conn->prepare("UPDATE services SET  service_max=:max WHERE service_id=:service ");
            $update->execute(array("service"=>$service_id , "max"=>$api_new_value ));
       

        elseif(strpos($change_desc1 , "Re-activated by number service provider"  ) ):

            // 1 - inactive , 2 - active



        $update = $conn->prepare("UPDATE services SET  service_type=:type WHERE service_id=:service ");
        $update->execute(array("service"=>$service_id , "type"=> 2 ));

        elseif(strpos( $change_desc1 , "removed by the number service provider" ) ):

                // 1 - inactive , 2 - active
    
   
            $active = 1;
            $update = $conn->prepare("UPDATE services SET  service_type=:type WHERE service_id=:service ");
        $update->execute(array("service"=>$service_id , "type"=>$active ));
                 

       
        endif;


    endforeach; 
endforeach; 
    
    
     $delete = $conn->prepare("DELETE FROM serviceapi_alert");
        $delete->execute();

    else :
        
    endif;
