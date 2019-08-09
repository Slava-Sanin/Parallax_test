<?php
session_start();

if(isset($_POST["submit"])) {
	if ($_SESSION['nonce'] != $_POST['nonce']) exit;
	
	$target_dir = "../uploads/";
	$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
	$uploadOk = 1;
	$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
	// Check if image file is a actual image or fake image

    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
    if($check !== false) {
        echo "File is an image - " . $check["mime"] . ".";
        $uploadOk = 1;
    } else {
        echo "File is not an image.";
        $uploadOk = 0;
    }

	// Allow certain file formats
	if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
		echo " Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
		$uploadOk = 0;
	}
	// Check if $uploadOk is set to 0 by an error
	if ($uploadOk == 0) {
		echo " Sorry, your file was not uploaded.";
		$target_file = "";
	// if everything is ok, try to upload file
	} else  {
				if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
					echo " The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
				} else {
					echo " Sorry, there was an error uploading your file.";
					$target_file = "";
				}
			} 
	 /*	if (! DOING_AJAX || ! check_ajax_referer('testimonial-nonce','nonce')) {
			return $this->return_json('error');
		}*/

		// for Debuging only ---------------------
		echo "<br>-------------------------<br>";
		echo "נתונים לפני סינון";
		echo "<br>-------------------------<br>";
		echo $_POST['category']."<br>";
		echo $_POST['product']."<br>";
		echo $_POST['price']."<br>";
		echo $_POST['product_id']."<br>";
		echo $_POST['city']."<br>";
		echo $target_file."<br>";
		echo "-------------------------<br>";
		//----------------------------------------
			
		//$blocked_chars = "\ '@#~`!%&|.,+?(){}[]^$:;*<>=/";
		// Attention! There are different filters! (Not the same!)
		$category = (!empty($_POST['category'])) ? my_filter($_POST['category'], "\'@#~`!%&|.,+?(){}[]^$:;*<>=/") : '';          // Allow ' ' (space) 
		$product = (!empty($_POST['product'])) ? my_filter($_POST['product'], "\'@#~`!%&|.,+?(){}[]^$:;*<>=/") : '';			 // Allow ' ' (space) 
		$price = (!empty($_POST['price'])) ? my_filter($_POST['price'], "\ '@#~`!%&|,+?(){}[]^$:;*<>=/") : '';                   // Allow . and only numbers
		$price = (!empty($price) && is_numeric($price)) ? $price : '';
		$product_id = (!empty($_POST['product_id'])) ? my_filter($_POST['product_id'], "\ '@#~`!%&|.,+?(){}[]^$:;*<>=/") : '';   // Allow letters and numbers
		$city = (!empty($_POST['city'])) ? my_filter($_POST['city'], "\'@#~!%&|.,+?(){}[]^$:;*<>=/") : '';                       // Allow `
		$picture = $target_file;
		
		$_SESSION['category']   = $category;
		$_SESSION['product']    = $product;
		$_SESSION['price']      = $price;
		$_SESSION['product_id'] = $product_id;
		$_SESSION['city']       = $city;
		// $_SESSION['picture']    = $picture;

		// for Debuging only ---------------------
		echo "<br>-------------------------<br>";
		echo "נתונים אחרי סינון";
		echo "<br>-------------------------<br>";
		echo $category."<br>";
		echo $product."<br>";
		echo $price."<br>";
		echo $product_id."<br>";
		echo $city."<br>";
		echo $picture."<br>";
		echo "-------------------------<br>";
		//----------------------------------------
	

	
	put_new_product_into_DB('sanin_products', array(
													'category' => $category,
													'product' => $product,
													'price' => $price,
													'product_id' => $product_id,
													'city' => $city,
													'picture' => $picture
												   ));
	
	//$location = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	$location = $_SERVER['HTTP_REFERER'];
	//echo <pre>;
	//var_dump ($_SERVER);
	//echo </pre>;
	
	echo "<br><br>לאחר לחיצה על המשך אעבור לדף הבית ".$location;	
	//header("Location: $location");
	
	echo "<script>
			setTimeout(function(){ alert('המשך'); window.location.href = '$location'; }, 3000);
		  </script>";  // window.location.replace('$location');
	exit;  
}

function my_filter($string, $regex_chars)
{
	if ( is_object( $str ) || is_array( $str ) ) {
        return '';
    }
	
    for ($i=0; $i<strlen($regex_chars); $i++)
    {
        $char = substr($regex_chars, $i, 1);
        $string = str_replace($char, '', $string);
    }
   return $string;
}

function _sanitize_text_fields( $str, $keep_newlines = false ) {
    if ( is_object( $str ) || is_array( $str ) ) {
        return '';
    }
 
    $str = (string) $str;
    $filtered = wp_check_invalid_utf8( $str );
	
    if ( strpos( $filtered, '<' ) !== false ) {
        $filtered = wp_pre_kses_less_than( $filtered );
        // This will strip extra whitespace for us.
        $filtered = wp_strip_all_tags( $filtered, false );
 
        // Use html entities in a special case to make sure no later
        // newline stripping stage could lead to a functional tag
        //$filtered = str_replace( "<\n", "&lt;\n", $filtered );
    }
 
    if ( ! $keep_newlines ) {
        $filtered = preg_replace( '/[\r\n\t ]+/', ' ', $filtered );
    }
    $filtered = trim( $filtered );
 
    $found = false;
    while ( preg_match( '/%[a-f0-9]{2}/i', $filtered, $match ) ) {
        $filtered = str_replace( $match[0], '', $filtered );
        $found    = true;
    }
 
    if ( $found ) {
        // Strip out the whitespace that may now exist after removing the octets.
        $filtered = trim( preg_replace( '/ +/', ' ', $filtered ) );
    }
 
    return $filtered;
}


function put_new_product_into_DB($table, $data) {

		$data_missing = array();
		
		foreach($data as $key => $value){				
				if(empty($data[$key])){
					// Adds key to array
					$data_missing[] = $key;
				} else {
						// Trim white space from the data
						$data[$key] = trim($data[$key]);						
					   }				
			}
	  
		if (empty($data_missing)){
			echo '<br>I am in ...'. dirname( __FILE__ ) . "<br><br>";
			
			//require '/wp-config.php';
			
			/** The name of the database for WordPress */
			define( 'DB_NAME', 'user5_db' );

			/** MySQL database username */
			define( 'DB_USER', 'user5_user' );

			/** MySQL database password */
			define( 'DB_PASSWORD', 'm49dYwZ5YQyV' );

			/** MySQL hostname */
			define( 'DB_HOST', 'localhost' );

			$dbc = @mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) OR die('Could not connect to MySQL: ' . mysqli_connect_error());
			
			// $data['category']="cat";
			// $data['product']="prod";
			// $data['price']=15;
			// $data['product_id']="123DFG";
			// $data['city']="Hulon";
			// $data['picture']="pic";

			//  $query = "INSERT INTO `$table`(`id`, `category`, `product`, `price`, `product_id`, `city`, `picture`) VALUES (NULL,'cat','prod',15,'123QWE','Hulon','pic')";			
			    $query = "INSERT INTO `$table`(`id`, `category`, `product`, `price`, `product_id`, `city`, `picture`) VALUES (NULL,'".$data['category']."','".$data['product']."',".$data['price'].",'".$data['product_id']."','".$data['city']."','".$data['picture']."')";
			
			if ($dbc->query($query) === TRUE) {
				$last_id = $dbc->insert_id;
				echo "New record created successfully. Last inserted ID is: " . $last_id ."<br><br>";
				foreach ($data as $key => $value) {
					echo $_SESSION[$key]."<br>";
					$_SESSION[$key] = "";
					echo $_SESSION[$key]."<br>";
				}
			} else  {
						echo "Error: " . $query . "<br>" . $dbc->error;
					}

			 $dbc->close();
			
		} else  {			
					echo '<br>You need to enter the following data:<br />';			
					foreach($data_missing as $missing){				
						echo "$missing<br>";				
					}	
				}					
	 
	}

?>