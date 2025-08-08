<?php

if(isset($_POST['First_Name'])) {
	
	include 'rsvpformsettings.php';

	function clean_string($string) {
	  $bad = array("content-type","bcc:","to:","cc:");
	  return str_replace($bad,"",$string);
	}
	
	function died($error) {
		echo "<br>Sorry but there were error(s) in the form you submitted:<br><ul>";
		echo "<i>".$error."</i>";
		echo "</ul>Please go back to fix the error(s). Thank you!<br /><br />";
		die();
	}
	
	$first_name = $_POST['First_Name']; // required
	$email_from = $_POST['Email']; // required
	$will_attend = $_POST['will_you_attend']; // required
	$number_of_guests = $_POST['Number_of_Guests']; // required
	$further_details = $_POST['Further_Details']; // not-required
	
	$error_message = "";
	
	if(!isset($_POST['will_you_attend'])) {
		$error_message .='<li>Please tell us if you are attending</li>';		
	}

	
	if(strlen($first_name)==0) {
		$error_message .= '<li>Please enter your name</li>';		
	} else if(strlen($first_name) < 2) {
		$error_message .= '<li>Your name does not appear to be valid.</li>';
	} 

	$email_exp = '/^[A-Za-z0-9._%-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,4}$/';
	if(strlen($email_from)==0) {
		$error_message .= '<li>Please enter your email</li>';		
	}else if(preg_match($email_exp,$email_from)==0) {
		$error_message .= '<li>The email address you entered does not seem to be valid</li>';
	}
	
	if((!strcmp(clean_string($number_of_guests),"0")) && (!strcmp(clean_string($will_attend[0]),"Yes"))) {
		$error_message .= '<li>You indicated that you will attend but entered 0 guests</li>';
	}

	$int = intval(clean_string($number_of_guests));
	if(($int>0) && (!strcmp(clean_string($will_attend[0]),"No"))) {
		$error_message .= '<li>You indicated that you will not attend but entered '.$int.' guest(s)</li>';
	}

	
	if(strlen($error_message) > 0) {
		died($error_message);
	}
	$email_message = "\r\n";
		
	$email_message .= "First Name: ".clean_string($first_name)."\r\n";
	$email_message .= "Email: ".clean_string($email_from)."\r\n";
	$email_message .= "Will Attend: ".clean_string($will_attend[0])."\r\n";
	$email_message .= "Number of Guests: ".clean_string($number_of_guests)."\r\n";
	$email_message .= "Further Details: ".clean_string($further_details)."\r\n";
	
$headers = 'From: '.$email_from."\r\n".
'Reply-To: '.$email_from."\r\n" .
'X-Mailer: PHP/' . phpversion();
mail($email_to, $email_subject, $email_message, $headers);
header("Location: $thankyou");
?>
<script>location.replace('<?php echo $thankyou;?>')</script>
<?php
}
die();
?>