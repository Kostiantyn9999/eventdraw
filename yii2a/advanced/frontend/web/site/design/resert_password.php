<!DOCTYPE html>
<html>
<head>
	<title></title>
	<!-- <link rel="stylesheet" type="text/css" href="login.css"> -->

</head>
<body>
<div id="reset_password_form">
	<div class="form_inner">
		<div><img src="./images/form-image-1.png" class="image-smile"></div>

		<div>
			<h1 class="priava-floor-plannin">priava floor planning</h1>
			<h4 class="request_pass_reset">Request password reset</h4>
		</div>

		<div>
  	    	<form>
	
    				<label for="email" class="input_lable">Please fill out your email.<br> 
											A link to reset password will be sent there.</label>
    				<input type="email" name="email" required>
					<button type="submit"> <img src="./images/login-arrow.png">Send</button>
    				
			</form>
		</div>
		
	</div>
</body>

<style type="text/css">
body
{
	background-image: url(./images/bg-image@2x.png);
	background-size: contain;
	background-repeat: no-repeat;
	background-position: center center;
}

#reset_password_form .form_inner {

    margin: 0 auto;
    max-width: 400px;
    padding: 40px 40px 80px;
    background-color: #ffffff;
    box-shadow: 0 2px 20px 0 rgba(0,0,0,0.15);
    margin-top: 5%;
}
#reset_password_form .priava-floor-plannin {
  color: #333333;
      margin: 0;
  font-size: 28px;
  font-weight: 600;
  letter-spacing: 0;
  line-height: 45px;
  text-transform: capitalize;
  font-family: 'muliregular';
}
#reset_password_form .request_pass_reset {
  color: #999999;
      margin: 0;
  font-size: 25px;
  letter-spacing: 0;
  line-height: 25px;
  font-family: 'muliregular';
    font-weight: 500;
}

#reset_password_form .input_lable {
  color: #333333;
  font-size: 14px;
  letter-spacing: 0;
  line-height: 23px;
  display: block;
  font-family: 'muliregular';
}
#reset_password_form button {
    display: block;
    border-radius: 49.38px;
    background-color: #AD76B3;
    color: #FFFFFF;
    font-size: 18px;
    letter-spacing: 0;
    padding: 10px 28px 10px 18px;
    border: 0;
    font-family: 'muliregular';
    cursor: pointer;
}
#reset_password_form button img {
	vertical-align: middle;
	    margin-top: -5px;
    margin-right: 10px;
}
#reset_password_form input {
box-shadow: none !important;
    box-sizing: border-box;
    padding: 13px 5px;
    width: 100%;
    border: 1px solid #CBD6E2;
    border-radius: 3px;
    background-color: #F3F6F8;
    margin-bottom: 23px;
    margin-top: 10px;
    
}
#reset_password_form img.image-smile {
    width: 55px;
}
#reset_password_form form {
    margin-top: 24px;
}

@font-face {
    font-family: 'muliregular';
    src: url('./font/muli-webfont.woff2') format('woff2'),
         url('./font/muli-webfont.woff') format('woff');
    font-weight: normal;
    font-style: normal;

}
	</style>
</html>