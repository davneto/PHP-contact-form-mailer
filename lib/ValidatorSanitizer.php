    
<?php 
    
    // Validate: Ensure that the input matches your business rules. If it doesn't, you reject the input.
    // Sanitize: Ensure that the format of the input doesn't break its container. Sanitization is also used to escape any attempt to cause data corruption when dealing with database based on user input.
    class ValidatorSanitizer{
        
        //BASIC ESCAPE / TRIM
        //Basic sanitize: replaces special html characters, strips unnecessary space, tab and newline and strips backslashes
        static function sanitize_basic($data) {
            $data = trim($data); //strip unnecessary characters (extra space, tab, newline)
            $data = stripslashes($data); //remove backslashes (\)
            $data = htmlspecialchars($data); //converts special characters to HTML entities
            $data = filter_var($data, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            return $data;
        }

        //EMAIL
        //Remove all characters except letters, digits and !#$%&'*+-=?^_`{|}~@.[].
        static function sanitize_email($data) {
            return filter_var($data, FILTER_SANITIZE_EMAIL);
        }

        //Check if input is a valid email address using filter_var()
        static function isValid_email_address($data){
            return (bool)filter_var($data, FILTER_VALIDATE_EMAIL);
        }

        //TEXT
        //remove tags, encode quotes and remove un-necessary ascii chars
        static function sanitize_text($data) {
            return filter_var($data, FILTER_SANITIZE_STRING);
        }

        //Check if input contains only letters and white space using Regular Expression match 
        static function isValid_letters_whiteSpace_only($data){
            return (bool)preg_match("/^[a-zA-Z ]*$/", $data);
        }

    }

?>