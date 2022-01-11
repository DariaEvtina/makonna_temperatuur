<?php
//lisamine
$yhendus=new mysqli('localhost','dariaevtina','12345parool','dariaevtina');
//$otsisona - otsingularity
/*CREATE TABLE Uuedkasutajad(
	id int primary key AUTO_INCREMENT,
    unimi varchar(100),
    psw varchar(100),
    isadmin int
);*/
session_start();
//uue kasutaja lissamine admetabeli sisse

function puhastaAndmed($data): string
{
    //trim()- eemaldab tühikud
    $data=trim($data);
    //htmlspecialchars - ignoreerib <käsk>
    $data=htmlspecialchars($data);
    //stripslashes - eemaldab \
    return stripslashes($data);
}
if(isset($_REQUEST["knimi"])&& isset($_REQUEST["pasw"])) {
    $LOGIN = puhastaAndmed($_REQUEST["knimi"]);
    $PSW = puhastaAndmed($_REQUEST["pasw"]);
    $sool = 'vagavagatekst';
    $krypt = crypt($PSW, $sool);
    $kask = $yhendus->prepare("SELECT id, unimi,psw FROM uuedkasutajad WHERE unimi=?");
    $kask->bind_param("s",  $LOGIN);
    $kask->bind_result($id, $loginK, $pass);
    $kask->execute();
    if($kask->fetch()){
        $_SESSION["error"]="Kasutaja on juba olimas";
        header("Location: $_SERVER[PHP_SELF]");
        $yhendus->close();
        exit();
    }
    else{
        $_SESSION["error"]=" ";
        header("Location: haldusMT.php");
    }
    $kask = $yhendus->prepare("INSERT INTO uuedkasutajad(unimi,psw,isadmin) VALUES (?,?,?)");
    $kask->bind_param("ssi", $LOGIN, $krypt, $_REQUEST["adm"]);
    $kask->execute();
    $_SESSION['unimi']=$LOGIN;
    $_SESSION['admin']=true;
//header("Location: haldusMT.php ");
    $yhendus->close();
    exit();
}
$error=$_SESSION["error"] ?? "";
?>
<!DOCTYPE html>
<html lang="et">
<head>
    <title>Registreerimis Vorm</title>
    <link rel="stylesheet" href="css/login.css" type="text/css">
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
</head>
<body>
<button onclick="document.getElementById('id02').style.display='block'">ava</button>
<div id="id02" class="modal">
<div class="modal-header">
    <h1>Uue kasutamine registreerimine</h1>
</div>
<form action="registr.php" class="modal-content">
    <div class="container">
        <table>
            <tr>
                <td><label for="knimi">Kasutajanimi:</label></td>
                <td><input type="text" placeholder="Sissetaja kasutajanimi" id="knimi" name="knimi" required>    <strong><?=$error?></strong></td>
            </tr>
            <tr>
                <td><label for="pasw">Parool:</label></td>
                <td><input type="password" placeholder="Sissetaja parool" id="pasw" name="pasw" required></td>
            </tr>
            <tr>
                <td><label for="adm">Kas teha admin?</label></td>
                <td><input type="checkbox" id="adm" name="adm" value="1"></td>
            </tr>
        </table>
        <div class="clearfix">
            <button type="submit" name="Logi kasutaja" class="deletebtn">Logi sisse</button>
            <button type="button" onclick="window.location.href='haldusMT.php'" class="cancelbtn">Loobu</button>
        </div>
    </div>

</form>
</div>
</body>
</html>