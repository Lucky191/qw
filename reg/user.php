<?php
// вся процедура работает на сессиях. Именно в ней хранятся данные пользователя, пока он находится на сайте. Очень важно запустить их в самом начале странички!!!
session_start();
 
include ("db.php");// файл bd.php должен быть в той же папке, что и все остальные, если это не так, то просто измените путь 
 
if (!empty($_SESSION['login']) and !empty($_SESSION['password']))
{
//если существует логин и пароль в сессиях, то проверяем, действительны ли они
$login = $_SESSION['login'];
$password = $_SESSION['password'];
$result2 = mysql_query("SELECT id FROM users WHERE login='$login' AND password='$password' AND activation='1'",$db); 
$myrow2 = mysql_fetch_array($result2); 
if (empty($myrow2['id']))
   {
   //если данные пользователя не верны
    exit("Вход на эту страницу разрешен только зарегистрированным пользователям!");
   }
}
else {
//Проверяем, зарегистрирован ли вошедший
exit("Вход на эту страницу разрешен только зарегистрированным пользователям!"); }
?>
<html>
<head>
<title>Список пользователей</title>
</head>
<body>
<h2>Список пользователей</h2>
 
 
<?php
if(isset($login) AND isset($password)){
    $resultat = mysql_query("SELECT * FROM users");
    $array = mysql_fetch_array($resultat);
    
    do{
    if($array['avatar'] == ''){
        $avatar = "noAvatar.jpg";
    }else{
        $avatar = $array['avatar'];
    }
    printf("$array[login]<br><a href='page.php?id=$array[id]'><img src='".$avatar."'></a><br><br>");
 
    }
    while($array = mysql_fetch_array($resultat));
    
}else{
print <<<HERE
<table>
Вход:
<br>
<br>
 
      <form action="login.html" method="POST">
      <tr>
      <td>Логин:</td>
      <td><input type="text" name="login" ></td>
      </tr>
      
      <tr>
      <td>Пароль:</td>
      <td><input type="password" name="password" ></td>
      </tr>
      
      <tr>
      <td colspan="2"><input type="submit" value="OK" name="submit" ></td>
      </tr>
      </form>
      </table>
<a href="registration.html">Регистрация</a>
HERE;
}
 
?>
 
</body>
</html>
