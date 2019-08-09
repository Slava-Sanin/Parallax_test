<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WordPress
 * @subpackage Twenty_Nineteen
 * @since 1.0.0
 */

session_start();
get_header();
?>

	<section id="primary" class="content-area">
		<main id="main" class="site-main">

			<?php

			/* Start the Loop */
			while ( have_posts() ) :
				the_post();

				get_template_part( 'template-parts/content/content', 'page' );

				// If comments are open or we have at least one comment, load up the comment template.
				if ( comments_open() || get_comments_number() ) {
					comments_template();
				}

			endwhile; // End of the loop.
			?>

		</main><!-- #main -->
	</section><!-- #primary -->

<?php
get_footer();

// ********* Sanin's code here **************
	$myObj = file_get_contents("./wp-content/uploads/Israel_cities.json"); 
	$cityJSON = json_decode($myObj);
	$count = 0;
	
	foreach ($cityJSON as $obj) {
		foreach ($obj as $key => $value) {
			if ($key == "שם_ישוב") { 
				$cleared_value = $value;
				$cleared_value = str_replace("(","]",$cleared_value);
				$cleared_value = str_replace(")","[",$cleared_value);
				$cleared_value = str_replace("'","`",$cleared_value);
				//$text.="<option dir=rtl value=".addslashes($cleared_value).">".addslashes($cleared_value)."</option>";			
				$text .= '<option dir=rtl value="' . $cleared_value . '">' . $cleared_value . '</option>';			
			}
		}
		$count++;
		//if ($count == 5) break;	// to limit count of cities in a list	
	}	
    
	$text ='<option dir=rtl value=""></option>' . $text;
	$_SESSION['nonce'] = wp_create_nonce('testimonial-nonce');
?>	
<head>
	<link href="./wp-content/themes/sanin/page.css" rel="stylesheet">
</head>
<script>

	var city_text = '<?= $text ?>';	// list of cities in Israel
	var nonce = "<?= $_SESSION['nonce'] ?>";  // nonce sequrity code for form
	var pic_product = document.querySelector('#product_picture'); // product picture preview box
	var fileToUpload = document.querySelector('#fileToUpload');  // selecting a product picture
	var lit_pic = new Image();	

	var category = "<?= $_SESSION['category'] ?>";
	var product = "<?= $_SESSION['product'] ?>";
	var price = "<?= $_SESSION['price'] ?>";
	var product_id = "<?= $_SESSION['product_id'] ?>";
	var city = "<?= $_SESSION['city'] ?>";
	var picture = "<?= $_SESSION['picture'] ?>";
		
	$('.js-example-basic-single').select2();
	
	// $('#category').val(null).trigger('change');   // reset selection
	document.querySelector('#category').value = category;	
	$("#category").select2({
    placeholder: "בחר קטגוריה",
    allowClear: false,
	theme: "classic",
	width: 'style'
	});

	// $('#city').val(null).trigger('change');   // reset selection
	document.querySelector('#city').innerHTML = city_text;
	document.querySelector('#city').value = city;
	$("#city").select2({
    placeholder: "בחר עיר",
    allowClear: false,
	theme: "classic",
	width: 'style'
	});

	// $(".select2").select2({
	  // theme: "classic"
	// });
	
	//$('.select2').width(300);
	//$('.select2-selection').height(40);
	//$('.product_form_wraper').width(300);
	
	//document.querySelector('#category').select2 = category;
	document.querySelector('#nonce').value = nonce;	
	document.querySelector('#product').value = product;
	document.querySelector('#price').value = price;
	document.querySelector('#product_id').value = product_id;
	//document.querySelector('#city').select2 = city;		
	//document.querySelector('#fileToUpload').files[0].name = picture;

	fileToUpload.addEventListener("change", showPic);
	document.querySelector('div.file-field').addEventListener("click", function (){fileToUpload.click();});

function showPic() {
	if (!fileToUpload.files[0].type.startsWith('image/')){ return; }
	//lit_pic.src = './wp-content/uploads/pc_user.gif';
	//lit_pic.src = fileToUpload.files[0];
	//pic_product.innerHTML = '<img border="0" src="" alt="product_picture" width="128" height="128">';
	pic_product.appendChild(lit_pic);
	
	const reader = new FileReader();
    reader.onload = (function(aImg) { return function(e) { aImg.src = e.target.result; }; })(lit_pic);
    reader.readAsDataURL(fileToUpload.files[0]); 
    document.querySelector('#product_picture > img').width = "128";
}
	
</script>
<?php
// ********* End Sanin's code **************