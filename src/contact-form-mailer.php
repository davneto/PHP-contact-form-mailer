<?php
    
    // SET necessary CORS:
    // header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: POST, OPTIONS");
    header("Access-Control-Allow-Headers", "X-Requested-With, content-type");

    require_once "Mail.php";  //Pear Mail library
    require_once "../lib/ValidatorSanitizer.php";  //Validator/Sanitizer for some of the imports

    //location of json file with credentials to access gmail SMTP server. Also containing "to" and "from" directions
    $credentials_location = "../config/gmail_credentials_and_directions.json";
    $gmail_credentials_and_directions = json_decode(file_get_contents($credentials_location));

    //set customizable fields from a customized file if provided, else set from the default file.
    include "./default_fields.php";  //default file
    include "../config/custom_fields.php";   //optional file which overlaps custom data

    $mail_subject = isset($custom_mail_subject) ? $custom_mail_subject : $default_mail_subject; //customizable field source

    function generate_html_mail_body($name, $email, $message_body){  //customizable generator source
        if(function_exists('custom_message')){
            return custom_generate_html_message_body($name, $email, $message_body);
        }else{
            return default_generate_html_message_body($name, $email, $messsage_body);
        }
    }

    //VALIDATION: In case of contact form, every field is optional, except not providing neither a message nor an email address. If this is the case, reject request at this point
    if(!isset($_POST["email_address"]) && !isset($_POST["message_body"])){
        http_response_code(400);   //Bad request
        exit('Please provide all the required fields.');
    }   

    //SANITIZING: initialize remaining undefined _POST superglobal arguments as empty strings 
    if(!isset($_POST["name"])){ $_POST["name"] = ""; }
    if(!isset($_POST["email_address"])){ $_POST["email_address"] = ""; }
    // if(!isset($_POST["subject"])){$_POST["subject"] = "";}
    if(!isset($_POST["message_body"])){ $_POST["message_body"] = ""; }

    //SANITIZING: collect sanitized data from _POST superglobal
    $name = ValidatorSanitizer::sanitize_text($_POST['name']);
    $email = ValidatorSanitizer::sanitize_email($_POST['email_address']);
    // $subject = ValidatorSanitizer::sanitize_text($_POST['subject']);
    $message_body = ValidatorSanitizer::sanitize_text($_POST['message_body']);

    
    //VALIDATION: further validation
    $is_data_valid = 
        ValidatorSanitizer::isValid_email_address($email);
    if(!$is_data_valid){
        http_response_code(400);   //Bad request, message successfully sent
        echo('Couldn\'t send message. Please provide all necessary and adequate inputs.');
        exit();
    }

    //SETUP: headers
    $headers = array(
        'From' => '<' . $gmail_credentials_and_directions->from . '>',
        'To' => '<' . $gmail_credentials_and_directions->to . '>',
        'Subject' => $mail_subject,
        'MIME-Version' => 1,
        'Content-type' => 'text/html;charset=iso-8859-1'
    );

    //SETUP: SMTP connection
    $smtp = Mail::factory('smtp', array(
        'host' => 'ssl://smtp.gmail.com',
        'port' => '465',
        'auth' => true,
        'username' => $gmail_credentials_and_directions->username,
        'password' => $gmail_credentials_and_directions->password
    ));

    //Generate HTML message body
    $mail_body = generate_html_mail_body($name, $email, $message_body);

    //Attempt to send mail
    $mail = $smtp->send($gmail_credentials_and_directions->to, $headers, $mail_body);

    //Feedback with HTTP Response code
    if (PEAR::isError($mail)) {
        http_response_code(500);    //Internal Server Error
        echo($mail->getMessage());
    } else {
        http_response_code(200);   //Accepted, message successfully sent
        echo('Message successfully sent!');
    }

?>
