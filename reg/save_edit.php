<?php 
include_once("db.php");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<title>Изменение данных <?php echo $login; ?></title>
</head>
<body>
<?php
 
////////Изменение аватары
 
if (isset($_FILES['fupload']['name'])){ //отправлялась ли переменная
 
    if (empty($_FILES['fupload']['name']) AND $_FILES['fupload']['name'] == ''){
        
        $noAvatar = "noAvatar.jpg";//изображение если пользователь не загрузил свое
        $result = mysql_query("SELECT avatar FROM users WHERE id='$id_user'");//извлекаем текущий аватар
        $avatarka = mysql_fetch_array($result);
        
        if ($avatarka['avatar'] != $noAvatar) {//если аватар был стандартный, то не удаляем его, ведь у на одна картинка на всех.
            unlink ('avatars/'.$avatarka['avatar']);
        }   
        
    }
 
    else{
        //иначе - загружаем изображение пользователя для обновления
        $path_to_90_directory = 'avatars/';//папка, куда будет загружаться начальная картинка и ее сжатая копия
            
        if(preg_match('/[.](JPG)|(jpg)|(jpeg)|(JPEG)|(gif)|(GIF)|(png)|(PNG)$/',$_FILES['fupload']['name']))//проверка формата исходного изображения
             {  
                    
                $filename = $_FILES['fupload']['name'];
                $source = $_FILES['fupload']['tmp_name'];   
                $target = $path_to_90_directory . $filename;
                move_uploaded_file($source, $target);//загрузка оригинала в папку $path_to_90_directory
 
            if(preg_match('/[.](GIF)|(gif)$/', $filename)) {
            $im = imagecreatefromgif($path_to_90_directory.$filename) ; //если оригинал был в формате gif
            }
            if(preg_match('/[.](PNG)|(png)$/', $filename)) {
            $im = imagecreatefrompng($path_to_90_directory.$filename) ;//если оригинал был в формате png
            }
            
            if(preg_match('/[.](JPG)|(jpg)|(jpeg)|(JPEG)$/', $filename)) {
                $im = imagecreatefromjpeg($path_to_90_directory.$filename); //если оригинал был в формате jpg
            }
            
        //СОЗДАНИЕ КВАДРАТНОГО ИЗОБРАЖЕНИЯ И ЕГО ПОСЛЕДУЮЩЕЕ СЖАТИЕ ВЗЯТО С САЙТА www.codenet.ru
        
        
        
        
        
        $w = 120;  // ширина изображения
        
        $quality = 100; //Качество создаваемого изображения max 100
                
        $w_src = imagesx($im); //вычисляем ширину
        $h_src = imagesy($im); //вычисляем высоту изображения
        
        //Создавать квадратное изображение $rezim = 1
        //Создать изображение пропорционально оригиналу $rezim = 2
        
        $rezim = 1;     
        
        switch ($rezim){
                //**************************** 1
            case "1" : 
                
                 // создаём пустую квадратную картинку 
                 // важно именно truecolor!, иначе будем иметь 8-битный результат 
                 $dest = imagecreatetruecolor($w,$w); 
 
                 // вырезаем квадратную серединку по x, если фото горизонтальное 
                 
                if ($w_src > $h_src){ 
                    imagecopyresampled($dest, $im, 0, 0, round((max($w_src,$h_src)-min($w_src,$h_src))/2), 0, $w, $w, min($w_src,$h_src), min($w_src,$h_src));
                }
                 // вырезаем квадратную верхушку по y, 
                if ($w_src < $h_src){
                    imagecopyresampled($dest, $im, 0, 0, 0, 0, $w, $w, min($w_src,$h_src), min($w_src,$h_src)); 
                }
                 // квадратная картинка масштабируется без вырезок 
                
                if ($w_src == $h_src){
                    imagecopyresampled($dest, $im, 0, 0, 0, 0, $w, $w, $w_src, $h_src); 
                }
 
            break;
                    //***************************** 2
            case "2" : 
                $prop = $w_src/$h_src;
                $h = $w/$prop;
                $dest = imagecreatetruecolor($w,$h); 
                imagecopyresampled($dest, $im, 0, 0, 0, 0, $w, $h, $w_src, $h_src); 
            break;
            
        }
        
        
        
 
                
        $random = rand(1000000, 9999999);
        imagejpeg($dest, $path_to_90_directory.$random.".jpg", $quality);//сохраняем изображение формата jpg в нужную папку
        
        $avatar = $random.".jpg";//заносим в переменную путь до аватара.
 
        $delfull = $path_to_90_directory.$filename; 
        unlink ($delfull);//удаляем оригинал загруженного изображения, он нам больше не нужен.
 
        $result = mysql_query("SELECT avatar FROM users WHERE id='$id_user'");//извлекаем текущий аватар пользователя
        $avatarka = mysql_fetch_array($result);
        
        if ($avatarka['avatar'] != $noAvatar) {//если аватар был стандартный, то не удаляем его, ведь у на одна картинка на всех.
            unlink ('avatars/'.$avatarka['avatar']);
        }
        }
        else{
            //в случае несоответствия формата, выдаем соответствующее сообщение
            exit ("Аватар должен быть в формате <strong>JPG,GIF или PNG</strong>");
        }
        
    }
    
 
    $up = mysql_query("UPDATE users SET avatar='$avatar' WHERE id='$id_user'");//обновляем аватар в базе
    if ($up == true) {//если верно, то отправляем на личную страничку
        echo "<meta http-equiv='Refresh' content='0; URL=profile.php?id=".$id_user."'>";
    }
 
}
 
////////Изменение имени
 
else if (isset($_POST['name'])){//Если существует имя
    $name = $_POST['name'];
    $name = stripslashes($name);
    $name = htmlspecialchars($name);
    $name = trim($name);//удаляем все лишнее
    
    if ($name == '') {
        exit("Вы не ввели имя<br><a href='edit.html'>Вернуться назад</a>");
    }
 
    $up = mysql_query("UPDATE users SET name_user='$name' WHERE id='$id_user'");//обновляем имя
    if ($up == true) {
        echo "<meta http-equiv='Refresh' content='0; URL=profile.html?id=".$id_user."'>";
    }
}
 
 
////////Изменение фамилии
 
else if (isset($_POST['lastname'])){//Если существует фамилия
    $lastname = $_POST['lastname'];
    $lastname = stripslashes($lastname);
    $lastname = htmlspecialchars($lastname);
    $lastname = trim($lastname);//удаляем все лишнее
    
    if ($lastname == '') {
        exit("Вы не ввели фамилию<br><a href='edit.html'>Вернуться назад</a>");
    }
 
    $up = mysql_query("UPDATE users SET lastname='$lastname' WHERE id='$id_user'");//обновляем фамилию
    if ($up=='TRUE') {//если верно, то обновляем его в сессии
        $_SESSION['lastname'] = $lastname;
        echo "<meta http-equiv='Refresh' content='0; URL=profile.html?id=".$id_user."'>";
    }
}
 
////////Изменение города
 
else if (isset($_POST['phone'])){//Если существует фамилия
    $phone = $_POST['phone'];
    $phone = stripslashes($phone);
    $phone = htmlspecialchars($phone);
    $phone = trim($phone);//удаляем все лишнее
    
    if ($phone == '') {
        exit("Вы не ввели город<br><a href='edit.html'>Вернуться назад</a>");
    }
 
    $up = mysql_query("UPDATE users SET phone='$phone' WHERE id='$id_user'");//обновляем город
    if ($up == true) {//если верно, то обновляем его в сессии
        $_SESSION['phone'] = $phone;
        echo "<meta http-equiv='Refresh' content='0; URL=profile.html?id=".$id_user."'>";
    }
}
 
////////Изменение города
 
else if (isset($_POST['address'])){//Если существует фамилия
    $address = $_POST['address'];
    $address = stripslashes($address);
    $address = htmlspecialchars($address);
    $address = trim($address);//удаляем все лишнее
    
    if ($address == '') {
        exit("Вы не ввели город<br><a href='edit.html'>Вернуться назад</a>");
    }
 
    $up = mysql_query("UPDATE users SET address='$address' WHERE id='$id_user'");//обновляем город
    if ($up == true) {//если верно, то обновляем его в сессии
        $_SESSION['address'] = $address;
        echo "<meta http-equiv='Refresh' content='0; URL=profile.html?id=".$id_user."'>";
    }
}
 
////////Изменение числа, месяца и года
 
else if (isset($_POST['birthdate_day']) AND isset($_POST['birthdate_month']) AND isset($_POST['birthdate_year'])){//Если существует день рождения
    $birthdate_day = $_POST['birthdate_day'];
    $birthdate_day = stripslashes($birthdate_day);
    $birthdate_day = htmlspecialchars($birthdate_day);
    $birthdate_day = trim($birthdate_day);//удаляем все лишнее
    
    if ($birthdate_day == '' OR $birthdate_day > 31) {
        exit("Вы не ввели день рождения<br><a href='edit.html'>Вернуться назад</a>");
    }
    
    $birthdate_month = $_POST['birthdate_month'];
    $birthdate_month = stripslashes($birthdate_month);
    $birthdate_month = htmlspecialchars($birthdate_month);
    $birthdate_month = trim($birthdate_month);//удаляем все лишнее
    
    if ($birthdate_month == '' OR $birthdate_month > 12) {
        exit("Вы не ввели месяц рождения<br><a href='edit.html'>Вернуться назад</a>");
    }
    
    $birthdate_year = $_POST['birthdate_year'];
    $birthdate_year = stripslashes($birthdate_year);
    $birthdate_year = htmlspecialchars($birthdate_year);
    $birthdate_year = trim($birthdate_year);//удаляем все лишнее
    
    if ($birthdate_year == '' OR $birthdate_year < 1940 OR $birthdate_year > 2005) {
        exit("Вы не ввели год рождения<br><a href='edit.html'>Вернуться назад</a>");
    }
 
    $up = mysql_query("UPDATE users SET birthdate_day='$birthdate_day',birthdate_month='$birthdate_month',birthdate_year='$birthdate_year' WHERE id='$id_user'");//обновляем день
    if ($up == true) {
        echo "<meta http-equiv='Refresh' content='0; URL=profile.html?id=".$id_user."'>";
    }
}
 
 
 
////////Изменение пола
 
else if (isset($_POST['sex'])){//Если существует фамилия
    $sex = $_POST['sex'];
    $sex = stripslashes($sex);
    $sex = htmlspecialchars($sex);
    $sex = trim($sex);//удаляем все лишнее
    
    if ($sex == '') {
        exit("Вы не ввели пол<br><a href='edit.html'>Вернуться назад</a>");
    }
 
    $up = mysql_query("UPDATE users SET sex='$sex' WHERE id='$id_user'");//обновляем пол
    if ($up == true) {
        echo "<meta http-equiv='Refresh' content='0; URL=profile.html?id=".$id_user."'>";
    }
}
?>
</body>
</html>
