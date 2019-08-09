<?php

function parse_json() {	
	$myObj = file_get_contents("../uploads/Israel_cities.json");
	$cityJSON = json_decode($myObj);
	
	//echo <pre>
	//var_dump($cityJSON);
	//echo </pre>
	
	foreach ($cityJSON as $obj) {
		foreach ($obj as $key => $value) {
			//if ($key == "שם_ישוב") echo "<option value=".$value.">".$value."</option>";						
			if ($key == "שם_ישוב") $text.="<option dir=\"rtl\" value=".$value.">".$value."</option>";						
		}
	}
	
	return $text;
}	

?>	

<script>
	var city_text = "<?php echo parse_json(); ?>";	
	document.querySelector('#city').innerHTML = city_text;
</script>