<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru">
<head>
<style>
.form-container {
   border: 0px solid #f2e3d2;
   background: #c9b7a2;
   background: -webkit-gradient(linear, left top, left bottom, from(#f2e3d2), to(#c9b7a2));
   background: -webkit-linear-gradient(top, #f2e3d2, #c9b7a2);
   background: -moz-linear-gradient(top, #f2e3d2, #c9b7a2);
   background: -ms-linear-gradient(top, #f2e3d2, #c9b7a2);
   background: -o-linear-gradient(top, #f2e3d2, #c9b7a2);
   background-image: -ms-linear-gradient(top, #f2e3d2 0%, #c9b7a2 100%);
   -webkit-border-radius: 22px;
   -moz-border-radius: 22px;
   border-radius: 22px;
   -webkit-box-shadow: rgba(000,000,000,0.9) 0 1px 2px, inset rgba(255,255,255,0.4) 0 0px 0;
   -moz-box-shadow: rgba(000,000,000,0.9) 0 1px 2px, inset rgba(255,255,255,0.4) 0 0px 0;
   box-shadow: rgba(000,000,000,0.9) 0 1px 2px, inset rgba(255,255,255,0.4) 0 0px 0;
   font-family: 'Helvetica Neue',Helvetica,sans-serif;
   text-decoration: none;
   vertical-align: middle;
   min-width:300px;
   padding:20px;
   width:300px;
   }
.form-field {
   border: 1px solid #c9b7a2;
   background: #e4d5c3;
   -webkit-border-radius: 14px;
   -moz-border-radius: 14px;
   border-radius: 14px;
   color: #c9b7a2;
   -webkit-box-shadow: rgba(255,255,255,0.4) 0 1px 0, inset rgba(000,000,000,0.7) 0 1px 1px;
   -moz-box-shadow: rgba(255,255,255,0.4) 0 1px 0, inset rgba(000,000,000,0.7) 0 1px 1px;
   box-shadow: rgba(255,255,255,0.4) 0 1px 0, inset rgba(000,000,000,0.7) 0 1px 1px;
   padding:8px;
   margin-bottom:20px;
   width:280px;
   }
.form-field:focus {
   background: #fff;
   color: #725129;
   }
.form-container h2 {
   text-shadow: #fdf2e4 0 1px 0;
   font-size:18px;
   margin: 0 0 10px 0;
   font-weight:bold;
   text-align:center;
    }
.form-title {
   margin-bottom:10px;
   color: #725129;
   text-shadow: #fdf2e4 0 1px 0;
   }
.submit-container {
   margin:8px 0;
   text-align:right;
   }
.submit-button {
   border: 1px solid #447314;
   background: #6aa436;
   background: -webkit-gradient(linear, left top, left bottom, from(#8dc059), to(#6aa436));
   background: -webkit-linear-gradient(top, #8dc059, #6aa436);
   background: -moz-linear-gradient(top, #8dc059, #6aa436);
   background: -ms-linear-gradient(top, #8dc059, #6aa436);
   background: -o-linear-gradient(top, #8dc059, #6aa436);
   background-image: -ms-linear-gradient(top, #8dc059 0%, #6aa436 100%);
   -webkit-border-radius: 5px;
   -moz-border-radius: 5px;
   border-radius: 5px;
   -webkit-box-shadow: rgba(255,255,255,0.4) 0 0px 0, inset rgba(255,255,255,0.4) 0 0px 0;
   -moz-box-shadow: rgba(255,255,255,0.4) 0 0px 0, inset rgba(255,255,255,0.4) 0 0px 0;
   box-shadow: rgba(255,255,255,0.4) 0 0px 0, inset rgba(255,255,255,0.4) 0 0px 0;
   text-shadow: #addc7e 0 1px 0;
   color: #31540c;
   font-family: helvetica, serif;
   padding: 8.5px 18px;
   font-size: 14px;
   text-decoration: none;
   vertical-align: middle;
   }
.submit-button:hover {
   border: 1px solid #447314;
   text-shadow: #addc7e 0 1px 0;
   background: #6aa436;
   background: -webkit-gradient(linear, left top, left bottom, from(#8dc059), to(#6aa436));
   background: -webkit-linear-gradient(top, #8dc059, #6aa436);
   background: -moz-linear-gradient(top, #8dc059, #6aa436);
   background: -ms-linear-gradient(top, #8dc059, #6aa436);
   background: -o-linear-gradient(top, #8dc059, #6aa436);
   background-image: -ms-linear-gradient(top, #8dc059 0%, #6aa436 100%);
   color: #31540c;
   }
.submit-button:active {
   text-shadow: #31540c 0 1px 0;
   border: 1px solid #447314;
   background: #8dc059;
   background: -webkit-gradient(linear, left top, left bottom, from(#6aa436), to(#6aa436));
   background: -webkit-linear-gradient(top, #6aa436, #8dc059);
   background: -moz-linear-gradient(top, #6aa436, #8dc059);
   background: -ms-linear-gradient(top, #6aa436, #8dc059);
   background: -o-linear-gradient(top, #6aa436, #8dc059);
   background-image: -ms-linear-gradient(top, #6aa436 0%, #8dc059 100%);
   color: #31540c;
   }
</style>
</head>
<body>
<div>
<?php
if($_POST["email"]) { ?>
<div ><img src="http://masterholiday.net/images/logo404.png" height="150" style="  display: block;  margin-left: auto; margin-right: auto;"></div>
<div style=" width:500px;display: block;  margin-left: auto; margin-right: auto; margin-top:25px; font: 16px Arial, Tahoma, Verdana, sans-serif; color:#6b4480; text-align:centered;"><i>Почтовый ящик <span style="color:#e73e5a; "><i><strong><? echo htmlspecialchars($_POST["email"]); ?></strong></i></span> отключен от рассылки. Спасибо.</i><div>
<div style=" width:600px;display: block;  margin-left: auto; margin-right: auto; margin-top:25px; font: 16px Arial, Tahoma, Verdana, sans-serif; color:#6b4480; text-align:centered;"><i>Если вы передумаете, мы будем очень рады.
<br>Более подробная информация на странице <a href="http://masterholiday.net/faq/iventor"><b>“Для ивенторов!”</b></a></i><div>
<?
}
else {
?>

<div ><img src="http://masterholiday.net/images/logo404.png" height="150" style="  display: block;  margin-left: auto; margin-right: auto;"></div>
<div style=" width:600px;display: block;  margin-left: auto; margin-right: auto; font: 16px Arial, Tahoma, Verdana, sans-serif; color:#6b4480; text-align:centered;"><i>Если Вы хотите отписаться от рассылки <span style="color:#e73e5a; "><i><strong>“Мастерская праздников”</strong></i></span>, пожалуйста, введите ваш e-mail:</i><div>
<form class="form-container" style="display: block;  margin-left: auto; margin-right: auto; margin-top:15px;" action="<?=$_SERVER['PHP_SELF']?>" method="POST">
<div class="form-title"><h2>Отписатся от рассылки</h2></div>
<input class="form-field" type="text" name="email" /><br />
<div class="submit-container">
<input class="submit-button" type="submit" value="Отписаться" />
</div>
</form>
<div style=" width:600px;display: block;  margin-left: auto; margin-right: auto; margin-top:25px; font: 16px Arial, Tahoma, Verdana, sans-serif; color:#6b4480; text-align:centered;"><i>Если вы передумаете, мы будем очень рады.
<br>Более подробная информация на странице <a href="http://masterholiday.net/faq/iventor"><b>“Для ивенторов!”</b></a></i><div>
</div>
<? } ?>



</body>
</html>