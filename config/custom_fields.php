<?php

    //Customizable fields
    $custom_mail_subject = "You have recieved a new message on your site!";
    
    function custom_generate_html_mail_body($name, $email, $message_body){
        return 
            "New message from <strong>" . $name . "</strong>" . " (<a>" . $email . "</a>).<br/><br/>" .
            "\"" . $message_body . "\"<br/><br/>" .
            "<hr/>" . 
            "<span style='font-size: 12px; color: #b9b9b9;'>This message was automatically generated. Please don't answer directly to this email. Thank you.</span>";
    }

?>
