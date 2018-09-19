<?php
$name = $emailAddress = $message = $recaptcha =  "";
$nameErr = "Only letters and white space allowed<br>";
$emailErr = "Invalid email format<br>";
$msgErr = " Please enter a message<br>";
$recaptchaErr = "Please check the the captcha form.<br>";
$errString = "";

if($_SERVER['REQUEST_METHOD'] == 'POST'){

    $name = cleanData($_POST['name']);
    $name = replaceCarriageReturn($name);

    if (empty($name) || !preg_match("/^[a-zA-Z ]*$/",$name)) {
        $errString .= $nameErr; 
    }
    
    $emailAddress = cleanData($_POST['email']);
    $emailAddress = replaceCarriageReturn($emailAddress);

    if (empty($emailAddress) || !filter_var($emailAddress, FILTER_VALIDATE_EMAIL)) {
        $errString .= $emailErr;
    }
    $message = cleanData($_POST['message']);
    if(empty($message)){
        $errString .= $msgErr;
    }

    $recaptcha = $_POST['g-recaptcha-response'];
    if((!$recaptcha) || empty($recaptcha)){
        $errString .= $recaptchaErr;
    }
    
    
    if(!empty($errString)) {
        // Set a 400 (bad request) response code and exit.
        http_response_code(400);
        echo "Oops! There was a problem with your submission. Please complete the form and try again.";
        exit;
    } else {
        sendMail($name, $emailAddress, $message);
    }
} else {
        // Not a POST request, set a 403 (forbidden) response code.
        http_response_code(403);
        echo "There was a problem with your submission, please try again.";
    }

function replaceCarriageReturn($data){
    return str_replace(array("\n\r", "\n", "\r"), "", $data);
}

function cleanData($data){
     $data = trim($data);
     $data = stripslashes($data);
     $data = htmlspecialchars($data);
     return $data; 
}

function sendMail ($name, $emailAddress, $message) {

    $privateKey = "hide from git";
    $response = $_POST['g-recaptcha-response'];
    $remoteip = $_SERVER['REMOTE_ADDR'];
    $url = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$privateKey&response=$response&remoteip=$remoteip");

    $result = json_decode($url, true);

    if(intval($result["success"]) == 1){
        // Set the recipient email address.
        $recipient = "info@capoeiradetroit.org";

        // Set the email subject.
        $subject = "Class Inquiry from $name";

        // Build the email content.
        $email_content = "Name: $name\n";
        $email_content .= "Email: $emailAddress\n\n";
        $email_content .= "Message:\n$message\n";

        // Build the email headers.
        $email_headers = "From: $name <$emailAddress>";

        // Send the email.
        if (mail($recipient, $subject, $email_content, $email_headers)) {
            // Set a 200 (okay) response code.
            http_response_code(200);
            echo "Thank You! Your message has been sent.";
        } else {
            // Set a 500 (internal server error) response code.
            http_response_code(500);
            echo "Oops! Something went wrong and we couldn't send your message.";
        }
    }
}


?>