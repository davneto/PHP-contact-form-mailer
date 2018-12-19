<?php
	
	require_once "../lib/ValidatorSanitizer.php";  //Validator/Sanitizer class

	use PHPUnit\Framework\TestCase;

	final class ValidatorSanitizerTest extends TestCase
	{	

		//BASIC
	    /**
	     * @dataProvider sanitizeBasic
	     */
	    public function testSanitizeBasic($data, $expected)
	    {

	    	$sanitized_text = ValidatorSanitizer::sanitize_basic($data);
	    	// print_r($isLetters_WhiteSpace_only);
	    	$this->assertSame($sanitized_text, $expected);
	    }
	    public function sanitizeBasic()
	    {
	        return [
	        	//valid mails
	            ["", ""],
	            ["a", "a"],
	            [" ", ""],
	            ["  trimmable white space  ", "trimmable white space"],
	            ["	trimmable tabs	", "trimmable tabs"],
	            ["\slash", "slash"],
	            ["\gunsNroses", "gunsNroses"],
	            ["'singlequotes'", "&#039;singlequotes&#039;"],
	            ["line\nbreak", "line\nbreak"],
	            ['".break;."', "&quot;.break;.&quot;"], //injection
	            ["'.break;.'", '&#039;.break;.&#039;'], //injection
	            ["<h1>Hello WorldÆØÅ!</h1>", "&lt;h1&gt;Hello World&AElig;&Oslash;&Aring;!&lt;/h1&gt;"],
	            ["text<a href='https://www.undesirableUrl.com'>undesirableLink</a>text", "text&lt;a href=&#039;https://www.undesirableUrl.com&#039;&gt;undesirableLink&lt;/a&gt;text"], //injection
	            ["text <script>window.location.replace('http://undesirableRedirect.com');</script>text", 'text &lt;script&gt;window.location.replace(&#039;http://undesirableRedirect.com&#039;);&lt;/script&gt;text'] //injection
	        ];
	    }

		//EMAIL
		/**
	     * @dataProvider validateEmailAddressData
	     */
	    public function testValidateEmailAddress($data, $expected)
	    {

	    	$isEmail = ValidatorSanitizer::isValid_email_address($data);
	    	// print_r($isEmail);
	    	$this->assertSame($isEmail, $expected);
	    }

	    public function validateEmailAddressData()
	    {
	        return [
	        	//valid mails
	            ["johndoe@email.a", true],
	            ["email@email.a", true],
	            ["a@a.a", true],
	            
	            //invalid char mails
	            ["[hack]test.email@", false],
	            ["<hack>test.email@", false],
	            ["/hack.email@", false],

	            //invalid format mails
	            ["email.email@", false],
	            ["email.com", false],
	            ["email@email", false],
	            ["email@email.", false],
	            ["@email.com", false],
	            ["@.", false],
	            ["", false],
	        ];
	    }

	    //TEXT
	    /**
	     * @dataProvider sanitizeText
	     */
	    public function testValidateText($data, $expected)
	    {

	    	$sanitized_text = ValidatorSanitizer::sanitize_text($data);
	    	// print_r($isLetters_WhiteSpace_only);
	    	$this->assertSame($sanitized_text, $expected);
	    }
	    public function sanitizeText()
	    {
	        return [
	        	//valid mails
	            ["", ""],
	            ["a", "a"],
	            ["à", "à"],
	            [" ", " "],
	            ["<h1>Title</h1>", "Title"],
	            ['".break;."', "&#34;.break;.&#34;"], //injection
	            ["'.break;.'", '&#39;.break;.&#39;'], //injection
	            ["<h1>Hello WorldÆØÅ!</h1>", "Hello WorldÆØÅ!"],
	            ["text<a href='https://www.undesirableUrl.com'>undesirableLink</a>text", "textundesirableLinktext"], //injection
	            ["text <script>window.location.replace('http://undesirableRedirect.com');</script>text", 'text window.location.replace(&#39;http://undesirableRedirect.com&#39;);text'] //injection
	        ];
	    }

	    //LETTERS AnD WHITE SPACES
	    /**
	     * @dataProvider validateLettersWhiteSpaceOnlyData
	     */
	    public function testValidateLettersWhiteSpaceOnly($data, $expected)
	    {

	    	$isLetters_WhiteSpace_only = ValidatorSanitizer::isValid_letters_whiteSpace_only($data);
	    	// print_r($isLetters_WhiteSpace_only);
	    	$this->assertSame($isLetters_WhiteSpace_only, $expected);
	    }
	    public function validateLettersWhiteSpaceOnlyData()
	    {
	        return [
	        	//valid mails
	            ["", true],
	            ["a", true],
	            [" ", true],
	            ["a ", true],
	            [" abcd ", true],
	            
	            //invalid chars
	            [".", false],
	            ["<", false],
	            ["abc.", false],
	            [" abc.", false],
	            [" abc,", false],

	        ];
	    }
	}

?>