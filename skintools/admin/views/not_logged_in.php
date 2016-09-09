<?php
// show potential errors / feedback (from login object)
if (isset($login)) {
    if ($login->errors) {
        foreach ($login->errors as $error) {
            echo $error;
        }
    }
    if ($login->messages) {
        foreach ($login->messages as $message) {
            echo $message;
        }
    }
}
?>
<!DOCTYPE html>
<html>
   <head>
      <title>Aurelia Admin Area</title>
      <link rel="stylesheet" href="http://yui.yahooapis.com/pure/0.6.0/pure-min.css">
      <style>
         html, body {
             background-image: url(views/img/bg.jpg);
             margin-top: 50px;
             width:100%;
             height:100%;
             text-align: center;
         }
         input.pure-button.pure-input-1-2.pure-button-primary {
            background-color: pink;
         }
         #login_input_username, #login_input_password, #login_input_button{
             margin-left: auto;
             margin-right: auto;
             width: 400px;
         }
         img {
            display: block;
            margin-left: auto;
            margin-right: auto;
            margin-bottom: 30px;
        }
      </style>
   </head>
   <body>
      <img src="adminlogo.png">
      <div id="loginwrapper">
         <!-- login form box -->
         <form method="post" action="index.php" name="loginform" class="pure-form">
            <fieldset class="pure-group">
               <input id="login_input_username" class="pure-input-1-2 login_input" placeholder="Username" type="text" name="user_name" required />
               <input id="login_input_password" class="pure-input-1-2 login_input" placeholder="Password" type="password" name="user_password" autocomplete="off" required />
               <input id="login_input_button" class="pure-button pure-input-1-2 pure-button-primary" type="submit" name="login" value="Log in" />
            </fieldset>
         </form>
      </div>
      <!-- <a href="register.php">Register new account</a> -->
   </body>
</html>