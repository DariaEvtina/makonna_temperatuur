<?php
require("maakonnaabiFunctsioonid.php");
session_start();
/*if(isset($_SESSION['tutvustamine'])){
    header("Location: registr.php");
    exit();
}*/

if(isSet($_REQUEST["makonnalisamine"])){
    if(!empty(trim($_REQUEST["uuemaakonnainimi"])) && !empty(trim($_REQUEST["uuemaakonnakeskus"]))) {
        lisaMakkona($_REQUEST["uuemaakonnainimi"], $_REQUEST["uuemaakonnakeskus"]);
        header("Location: haldusMT.php");
        exit();

    }
}
if(isSet($_REQUEST["temperatuurlisamine"])){
    if(!empty(trim($_REQUEST["aeg"])) && !empty(trim($_REQUEST["Lissatemperatuur"]))){
        lisaTemperatuur($_REQUEST["maakonna_id"], $_REQUEST["aeg"], $_REQUEST["Lissatemperatuur"]);
        header("Location: haldusMT.php");
        exit();
    }
}
if(isSet($_REQUEST["kustutusid"])){
    kustutaTemperatuur($_REQUEST["kustutusid"]);
}
if(isSet($_REQUEST["muutmine"])){
    muudaTemperatuur($_REQUEST["muudetudid"],$_REQUEST["maakonna_id"], $_REQUEST["temperatuur"], $_REQUEST["kupyaev_aeg"]);
}
$sorttulp="temperatuur";
$otsisona="";

if(isSet($_REQUEST["sort"])){
    $sorttulp=$_REQUEST["sort"];
}
if(isSet($_REQUEST["otsisona"])){
    $otsisona=$_REQUEST["otsisona"];
}
$MTid=kysiTemperatuurAndmed($sorttulp,$otsisona);
?>
<!DOCTYPE html>
<html lang="et">
<head>
    <title>Ilm leht</title>
    <link rel="stylesheet" href="css/style.css" type="text/css">
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
    <script src="modal.js"></script>
</head>
<body>

<div id="menuArea">
    <button onclick="window.location.href='registr.php'" >Loo uus kasutaja</button>
    <?php
    if(isset($_SESSION['unimi'])){
        ?>
        <h1>Tere, <?="$_SESSION[unimi]"?></h1>
        <button onclick="window.location.href='logout.php'" >Logi välja</button>
        <?php
    } else {
        ?>
        <button onclick="window.location.href='loginAB.php'" >Logi sisse</button>
        <?php
    }
    ?>
</div>

<div class="header">
    <h1>Halduse leht</h1>
</div>
<form action="haldusMT.php">
    <div class="column">
        <h2>Temperatuuri lisamine</h2>
        <dl>
            <dt>temperatuur</dt>
            <dd><input type="number" name="Lissatemperatuur" max="30" min="-26"/></dd>
            <dt>maakonnanimi</dt>
            <dd><?php
                echo looRippMenyy("SELECT id, maakonnanimi FROM maakondad",
                    "maakonna_id");
                ?>
            </dd>
            <dt>aeg</dt>
            <dd><input type="datetime-local" name="aeg" /></dd>
        </dl>
        <input type="submit" name="temperatuurlisamine" value="Lisa temperatuur" />
    </div>

        <?php if (isset($_SESSION['unimi'])){?>
        <div class="column">
        <h2>Makonnanimi lisamine</h2>
        <dl>
            <dt>Maakonnanimi</dt>
            <dd><input type="text" name="uuemaakonnainimi" /></dd>
            <dt>Maakonnakeskus</dt>
            <dd><input type="text" name="uuemaakonnakeskus"/></dd>
            <input type="submit" name="makonnalisamine" value="Lisa makonnanimi" />
        </dl>
        </div>
        <?php }?>

</form>
<div class="column">
    <form action="haldusMT.php">
        <h2>Ilm loetelu</h2>
        Otsi: <input type="text" name="otsisona" />
        <table>
            <tr>
                <?php if (isset($_SESSION['unimi'])){?>
                <th>Haldus</th>
                <?php }?>
                <th><a href="haldusMT.php?sort=kuupyaev_kellaaeg">Kupäev/Kellaaeg</a></th>
                <th><a href="haldusMT.php?sort=maakonnanimi">Maakonnanimi</a></th>
                <th><a href="haldusMT.php?sort=temperatuur">Temperatuur</a></th>
            </tr>
            <?php foreach($MTid as $MT): ?>
                <tr>
                    <?php if(isSet($_REQUEST["muutmisid"]) &&
                        intval($_REQUEST["muutmisid"])==$MT->id): ?>
                        <td>
                            <input type="submit" name="muutmine" value="Muuda" />
                            <input type="submit" name="katkestus" value="Katkesta" />
                            <input type="hidden" name="muudetudid" value="<?=$MT->id ?>" />
                        </td>

                        <!--<td><input type="text" name="maakonnanimi" value=" /></td>-->

                        <td><?php
                            echo looRippMenyy("SELECT id, maakonnanimi FROM maakondad",
                                "maakonna_id", $MT->id);
                            ?></td>
                        <td><input type="text" name="kupyaev_aeg" value="<?=$MT->kuupyaev_kellaaeg ?>" /></td>
                        <td><input type="number" name="temperatuur" max="30" min="-26" value="<?=$MT->temperatuur ?>" /></td>
                    <?php else: ?>
                        <?php
                        if (isset($_SESSION['unimi'])){?>
                        <td><a href="haldusMT.php?kustutusid=<?=$MT->id ?>"
                               onclick="return confirm('Kas ikka soovid kustutada?')">x</a>
                            <a href="haldusMT.php?muutmisid=<?=$MT->id ?>">m</a>
                        </td>
                    <?php }?>

                        <td><?=$MT->kuupyaev_kellaaeg?></td>
                        <td><?=$MT->maakonnanimi ?></td>
                        <td><?=$MT->temperatuur ?></td>
                    <?php endif ?>
                </tr>
            <?php endforeach; ?>
        </table>
    </form>
    </div>
</body>
</html>