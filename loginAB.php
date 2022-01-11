<?php
//LOGIN VORM KODIS ADMEBAASIS SALVESTATUD KIRJUTAJANIMEGA JA PAROOLIGA
session_start();
if(isset($_SESSION['tuvastamine'])){
    header('Location: haldusMT.php');
    exit();
}
$yhendus=new mysqli('localhost','dariaevtina','12345parool','dariaevtina');
//kontroll kas login vorm on  täitetud
if(isset($_REQUEST['knimi']) && isset($_REQUEST['pasw'])){
    $login=htmlspecialchars($_REQUEST['knimi']);
    $pass=htmlspecialchars($_REQUEST['pasw']);
    $sool='vagavagatekst';
    $krypt=crypt($pass, $sool);
    //kontrollime kas admebaasis on selline kasutaja
    $kask=$yhendus->prepare("SELECT id, unimi, isadmin, psw FROM uuedkasutajad WHERE unimi=? ");
    $kask->bind_param("s",$login);
    $kask->bind_result($id,$kasutajanimi, $isadmin, $psw);
    $kask->execute();
    if($kask->fetch() && $krypt===$psw){
        $_SESSION['unimi']=$login;
        $_SESSION['isadmin']=$isadmin;
        if ($isadmin===1) {
            $_SESSION['tuvastamine'] = 'niilithne';
            header("Location: haldusMT.php");
            exit();
        }
        echo "kasutaja $login või parool $krypt on vale";
    }

}
?>
<!DOCTYPE html>
<html lang="et">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" type="text/css" href="css/login.css">
</head>
<body>
<button onclick="document.getElementById('id01').style.display='block'">ava</button>
<div id="id01" class="modal">
    <span onclick="window.location.href='haldusMT.php'" class="close" title="logi vorm">×</span>
    <h1 class="modal-header">Login vorm</h1>
    <form action="loginAB.php" class="modal-content">
        <div class="container">
        <label for="knimi">Kasutajanimi:</label>
        <input type="text" placeholder="Sissetaja kasutajanimi" id="knimi" name="knimi" >
        <label for="pasw">Parool:</label>
        <input type="password" placeholder="Sissetaja parool" id="pasw" name="pasw" >
        <div class="clearfix">
        <!--<input type="submit" value="Logi sisse" name="Logi kasutaja" class="cancelbtn">-->
        <button type="submit" name="Logi kasutaja" class="deletebtn">Logi sisse</button>
        <button type="button" onclick="window.location.href='haldusMT.php'" class="cancelbtn">Loobu</button>
        </div>
        </div>
    </form>
</div>
<script src="modal.js"></script>
</body>
</html>