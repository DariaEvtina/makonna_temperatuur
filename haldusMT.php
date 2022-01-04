<?php
require("maakonnaabiFunctsioonid.php");
if(isSet($_REQUEST["makonnalisamine"])){
    lisaMakkona($_REQUEST["uuemaakonnainimi"]);
    header("Location: haldusMT.php");
    exit();
}
if(isSet($_REQUEST["temperatuurlisamine"])){
    //empty=pusto
    //trim = delaet tak chto esli plohoi chelovek vedet probel vmesto nazvaniia, ona ne vodila eto v tablitsu
    if(!empty(trim($_REQUEST["aeg"])) && !empty(trim($_REQUEST["temperatuur"]))){
        lisaTemperatuur($_REQUEST["maakonna_id"], $_REQUEST["aeg"], $_REQUEST["temperatuur"]);
        header("Location: haldusMT.php");
        exit();
    }
}
if(isSet($_REQUEST["kustutusid"])){
    kustutaTemperatuur($_REQUEST["kustutusid"]);
}
if(isSet($_REQUEST["muutmine"])){
    muudaTemperatuur($_REQUEST["muudetudid"], $_REQUEST["maakonnanimi"],$_REQUEST["maakonna_id"], $_REQUEST["temperatuur"]);
}
$MTid=kysiTemperatuurAndmed();
?>
<!DOCTYPE html>
<html lang="et">
<head>
    <title>Kaupade leht</title>
    <link rel="stylesheet" href="style.css" type="text/css">
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
</head>
<body>
<div class="header">
    <h1>Halduse leht</h1>
</div>
<form action="haldusMT.php">
    <div class="column">
        <h2>Temperatuuri lisamine</h2>
        <dl>
            <dt>temperatuur:</dt>
            <dd><input type="text" name="temperatuur" /></dd>
            <dt>maakonnanimi:</dt>
            <dd><?php
                echo looRippMenyy("SELECT id, maakonnanimi FROM maakondad",
                    "maakonna_id");
                ?>
            </dd>
            <dt>aeg:</dt>
            <dd><input type="datetime-local" name="aeg" /></dd>
        </dl>
        <input type="submit" name="temperatuurlisamine" value="Lisa temperatuur" />
    </div>
    <div class="column">
        <h2>Makonnanimi lisamine</h2>
        <input type="text" name="uuemaakonnainimi" />
        <input type="submit" name="makonnalisamine" value="Lisa makonnanimi" />
</form>
<div class="column">
    <form action="haldusMT.php">
        <h2>Ilm loetelu</h2>
        <table>
            <tr>
                <th>Haldus</th>
                <!--<th>Kupäev/Kellaaeg</th>
                <th>Maakonnanimi</th>
                <th>Temperatuur</th>-->
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
                        <td><input type="text" name="maakonnanimi" value="<?=$MT->maakonnanimi ?>" /></td>
                        <td><?php
                            echo looRippMenyy("SELECT id, maakonnanimi FROM maakondad",
                                "maakonna_id", $MT->id);
                            ?></td>
                        <td><input type="text" name="kupyaev_aeg" value="<?=$MT->kuupyaev_kellaaeg ?>" /></td>
                        <td><input type="text" name="temperatuur" value="<?=$MT->temperatuur ?>" /></td>
                    <?php else: ?>
                        <td><a href="haldusMT.php?kustutusid=<?=$MT->id ?>"
                               onclick="return confirm('Kas ikka soovid kustutada?')">x</a>
                            <a href="haldusMT.php?muutmisid=<?=$MT->id ?>">m</a>
                        </td>
                        <td><?=$MT->kuupyaev_kellaaeg?></td>
                        <td><?=$MT->maakonnanimi ?></td>
                        <td><?=$MT->temperatuur ?></td>
                    <?php endif ?>
                </tr>
            <?php endforeach; ?>
        </table>
        </div>
    </form>

</body>
</html>
