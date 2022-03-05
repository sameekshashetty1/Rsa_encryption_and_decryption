<?php

include 'connection.php';
//Function to encrypt the data from the file
$start1=microtime(true);
function encrypt_data($filename,$encrypt_file,$keys){
  $plaintext = "";
  $myfile = fopen($filename,"r");

  while(!feof($myfile)){
      $text = fgets($myfile)."<br>";
      $plaintext.=$text;
   }
  fclose($myfile);
 // echo "Text to be encypted is ".$plaintext ."<br><br>";

  openssl_public_encrypt($plaintext, $encrypted, $keys['public']);

  // Use base64_encode to make contents viewable/sharable
  $message = base64_encode($encrypted);

  $file = fopen($encrypt_file,"w");
  fwrite($file,$message);
 
  fclose($file);

 return $message;

  // Decode from base64 to get raw data
  // $ciphertext = base64_decode($message);
}


//Function to decryptthe data from the file

function decrypt_data($encrypt_file,$decrypt_file,$keys){

    $ciphertext = "";
  $plaintext = "";
  
  $myfile = fopen($encrypt_file,"r");

  while(!feof($myfile)){
      $text = fgets($myfile);
      $plaintext.=$text;
   }
  fclose($myfile);
  
  $ciphertext = base64_decode($plaintext);
  //  echo $ciphertext."Ciphertext<br><br>";
  //  echo $plaintext." Plaintext<br><br>";

  openssl_private_decrypt($ciphertext, $decrypted, $keys['private']);

  //echo $decrypted;
  $fil = fopen($decrypt_file,"w");
  fwrite($fil,$decrypted);

  fclose($fil);
  return $decrypted;

}



  // Create a private/public key pair
  $config = array(
      "digest_alg" => "sha512",
      "private_key_bits" => 2048,
      "private_key_type" => OPENSSL_KEYTYPE_RSA,
  );
  $resource = openssl_pkey_new($config);

  // Extract private key from the pair
  openssl_pkey_export($resource, $private_key);

  // Extract public key from the pair
  $key_details = openssl_pkey_get_details($resource);
  $public_key = $key_details["key"];

  $keys = array('private' => $private_key, 'public' => $public_key);
  
  if(isset($_POST['upload'])){
    $plaintext = "";

    $file_name=basename($_FILES['file']['name']);

   // $file_type=$_FILES['file']['type'];
    $file_size=$_FILES['file']['size'];
    $file_tem_loc=$_FILES['file']['tmp_name'];
    
    $file_store="upload/".$file_name;

    move_uploaded_file($file_tem_loc,$file_store);
    $myfile = fopen($file_store,"r");

    while(!feof($myfile)){
      $text = fgets($myfile);
      $plaintext.=$text;
    }
    fclose($myfile);

    $extension = pathinfo($file_name,PATHINFO_EXTENSION);
    
    if(!in_array($extension,['txt','pdf','doc'])){
      echo "Your file extension must be .txt";
    }elseif($file_size>256){
      echo "File is too large!";
    }else{
     $sql1 = mysqli_query($con,"INSERT INTO files(original_file) VALUES ('$plaintext')");
     if($sql1){
       echo "<script>
              alert('File uploaded successfully')
              </script>";
     }else{
       echo"failed to upload the file";
     }
    }

    $myfile = fopen("original_file.txt","w");
    fwrite($myfile,$plaintext);

    fclose($myfile);
   
  }

  $filename ="original_file.txt";
  $encrypt_file="encrypted.txt";
  $decrypt_file = "decrypted.txt";

if(isset($_POST['Encrypt'])){
    $encrypt = encrypt_data($filename,$encrypt_file,$keys);

    echo '<script>
            alert("'.$encrypt.'");
          </script>';
  }
  if(isset($_POST['Decrypt'])){
      $encrypt = encrypt_data($filename,$encrypt_file,$keys);
      $decrypt = decrypt_data($encrypt_file,$decrypt_file,$keys);
      echo "<p>Decrypted message is:<br>";
      echo $decrypt;
  }
$end1=microtime(true);
if(isset($_POST['Time'])){
    $d=$end1-$start1;
    echo "<p>Total Time: ".round($d,5);

}
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head >
    <meta charset="utf-8">
    <title>Uploading file</title>
    <style type="text/css">
    #form{
      padding: 20px;
      margin-top: 120px;
      margin-left: 500px;
      height: 180px;
      width: 500px;
      background: lightblue;
      float: left;
      line-height: 30px;
      border-style: ridge;
    }
    input{
      /* color: #ffffff; */
      margin-top:15px;
      padding :5px;
      border-radius :5px;
      margin-left:20px;
    }
/* #but1{
margin-left: 500px;
margin-top: 410px;
border: dotted;
}
#but2{
  margin-left: 800px;
  margin-top: -20px;
} */
.btn{
  margin-top: 425px;
  margin-left: 600px;
}
</style>
  </head>
  <body style="background-color:#B7E9F7">
    <h1 style="margin-left:520px">CLOUD COMPUTING PROJECT</h1>
    <div id="form">
    <form action="?" method="POST" enctype="multipart/form-data">
      <label style="font-size:23px">Uploading File</label>
      <div id="inputs">
      <p><input type="file" name="file"/></p>
      <p ><input type="submit" name="upload" value="Upload File" style="width:100px"></p>
    </div>


    </form>
      </div>

<div class="btn">
  <form method="POST">
  <button name='Encrypt' type='submit' value='ENCRYPT' style='padding:5px;border-radius:5px;' >ENCRYPT FILE</button>
    <button name="Decrypt" type="submit" value="DECRYPT" style="padding:5px;margin-left:25px;border-radius:5px">DECRYPT FILE</button>
      <button name="Time" type="submit" value="TIME" style="padding:5px;margin-left:25px;border-radius:5px">TIME</button>
    </form>
</div>

  </body>
</html>