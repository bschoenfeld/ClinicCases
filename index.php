<?php 
include 'db.php';
$login_error = $_GET['login_error'];
if ($login_error == "1")
{
$toggle_display = "block";
}
else
{$toggle_display = "none";}

if ($login_error == "2")
{
$toggle_display2 = "block";
}
else
{$toggle_display2 = "none";}

if ($login_error == "3")
{
$toggle_display3 = "block";
}
else
{$toggle_display3 = "none";}


?>
<html>
<head>
<title>ClinicCases - Online Case Management Software for Law School Clinics</title>
<link rel="stylesheet" href="cm.css" type="text/css">
<link rel="stylesheet" href="cm_tabs.css" type="text/css">
<script src="./javascripts/ajax_scripts.js" type="text/javascript"></script>
<script src="scriptaculous/lib/prototype.js" type="text/javascript"></script>
<script src="scriptaculous/src/scriptaculous.js" type="text/javascript"></script>

</head>
<body>
<div id="content" style="margin-top:25px;">
<div style="width:30%;height:100%;background-color:rgb(255,255,204);float:left;">

<div id="status" style="color:red;display:<?PHP ECHO $toggle_display; ?>">Your username or password is incorrect.  Please try again.</div>

<div id="status2" style="color:red;display:<?PHP ECHO $toggle_display2; ?>">Your account is currently inactive.  Please contact your clinic's adminstrator for more information.</div>

<div id="status3" style="color:red;display:<?PHP ECHO $toggle_display3; ?>">Your session has expired.  Please log in again.</div>

<form name = "getin" id="getin" action="login.php" method="post" style="margin-top:30px;">

<label for "username">Username</label><br><input type="text" id = "username" name="username" style="color:black;" value = "<?php if (isset($_COOKIE['cc_user'])){$cookie_value = $_COOKIE['cc_user'];echo $cookie_value;} ?>"><br>
<label for "password">Password</label><br><input type="password" id = "password" name="password" style="color:black;"><br>
<label for "remember">Remember My Username</label><input type="checkbox" name="remember"  style="margin-top:8px;color:red;"><br>
<a href="#" onClick = "document.getin.submit();return false;"><img src="./images/check_yellow.png" border="0" style="margin-top:15px;"></a>
</form>
<div style="margin-top:30px"><a href="#" onClick="Effect.Appear('forgot');return false">Forgot username or password?</a></div>


<div id="forgot" style="background-color:rgb(255, 255, 204);margin-top:15px;width:99%;height:40%;display:none;">
<p>Please provide your email address and your information will be sent to you.</p>
<input type = "text" id = "email" name="email" style="color:black;"><br>
<a href="#" onClick = "createTargets('forgot','forgot');sendDataGet('forgot_password.php?email=' + document.getElementById('email').value);return false;"><img src="images/check_yellow.png" border="0" style="margin-top:15px;"></a>

</div>
<div style="margin-top:30px"><a href="./newaccount">Students: New Account</a></div>

</div>
<div style="width:70%;height:100%;float:right;">


<h1 style="text-align:center;">Welcome to ClinicCases<span style="color:gray;font-style:italic;">Beta</span><span style="color:red;font-style:italic;font-weight:bold;">3</span></h1><center>
<div style="width:92%;height:90%;margin-top:5%;">
<div style="width:29%;height:95%;float:left;">
<ul style="list-style-type: circle;">
<li class="fr">Online case management specially designed for law school clinics.</li>
<li class="fr">Students, professors and administrators can access case information from any internet-connected computer.</li>
<li class="fr">AJAX-powered application, easy to use.</li>
</ul>
<a href="http://www.cliniccases.com/forums">Community Forum</a><br>
<a href="Release_Notes_Beta_3.txt" target="_new">Release Notes, Beta3</a>
</div>
<div style="width:70%;height:95%;float:right;">
<img src="images/collage.jpg" border="0">
</div>
</div>
</center>
</div>
</div>

<?php include 'footer.php'; ?>


</body>
</html>