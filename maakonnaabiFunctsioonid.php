<pre>
<?php
//print_r(kysiTemperatuurAndmed());
?>
</pre>

<?php
//lisamine
$yhendus=new mysqli('localhost','dariaevtina','12345parool','dariaevtina');
//$otsisona - otsingularity
function kysiTemperatuurAndmed($sorttulp="kuupyaev_kellaaeg", $otsisona=""){
    global $yhendus;
    $lubatudtulbad=array("kuupyaev_kellaaeg", "maakonnanimi", "temperatuur");
    if(!in_array($sorttulp, $lubatudtulbad)){
        return "lubamatu tulp";
    }
    //addslashes stripslashes lisab langioone kustutamine
    $otsisona=addslashes(stripslashes($otsisona));
    $kask=$yhendus->prepare("SELECT ilmatemperatuur.id, temperatuur, maakonnanimi, kuupyaev_kellaaeg
    FROM ilmatemperatuur, maakondad
    WHERE ilmatemperatuur.maakonna_id=maakondad.id
    AND (temperatuur LIKE '%$otsisona%' OR kuupyaev_kellaaeg LIKE '%$otsisona%' OR maakonnanimi LIKE '%$otsisona%')
       ORDER BY $sorttulp");
    //echo $yhendus->error;
    $kask->bind_result($id, $temperatuur, $maakonnanimi, $kuupyaev_kellaaeg);
    $kask->execute();
    $hoidla=array();
    while($kask->fetch()){
        $kaup=new stdClass();
        $kaup->id=$id;
        $kaup->temperatuur=htmlspecialchars($temperatuur);
        $kaup->maakonnanimi=htmlspecialchars($maakonnanimi);
        $kaup->kuupyaev_kellaaeg=htmlspecialchars($kuupyaev_kellaaeg);
        array_push($hoidla, $kaup);
    }
    return $hoidla;
}


// dropdownlist
function looRippMenyy($sqllause, $valikunimi, $valitudid=""){
    global $yhendus;
    $kask=$yhendus->prepare($sqllause);
    $kask->bind_result($id, $sisu);
    $kask->execute();
    $tulemus="<select name='$valikunimi'>";
    while($kask->fetch()){
        $lisand="";
        if($id==$valitudid){$lisand=" selected='selected'";}
        $tulemus.="<option value='$id' $lisand >$sisu</option>";
    }
    $tulemus.="</select>";
    return $tulemus;
}


//lisab uuekaubagrupi
function lisaMakkona($maakonnanimi,$maakonnakeskus){
    global $yhendus;
    $kask=$yhendus->prepare("INSERT INTO maakondad (maakonnanimi,maakonnakeskus) VALUES (?,?)");
    $kask->bind_param("ss", $maakonnanimi,$maakonnakeskus);
    $kask->execute();
}

function lisaTemperatuur($maakonna_id, $kuupyaev_kellaaeg, $temperatuur){
    global $yhendus;
    $kask=$yhendus->prepare("INSERT INTO
       ilmatemperatuur ( maakonna_id, temperatuur, kuupyaev_kellaaeg) VALUES (?, ?, ?)");
    $kask->bind_param("iis", $maakonna_id,$temperatuur , $kuupyaev_kellaaeg);
    $kask->execute();
}
//kustuta
function kustutaTemperatuur($temperatuur_id){
    global $yhendus;
    $kask=$yhendus->prepare("DELETE FROM ilmatemperatuur WHERE id=?");
    $kask->bind_param("i", $temperatuur_id);
    $kask->execute();
}
//muudab andmed tabelis
function muudaTemperatuur($temperatuur_id, $maakonna_id, $temperatuur, $kuupyaev_kellaaeg)
{
    global $yhendus;
    $kask = $yhendus->prepare("UPDATE ilmatemperatuur SET maakonna_id=?, temperatuur=?, kuupyaev_kellaaeg=? WHERE id=?");
    $kask->bind_param("iisi",  $maakonna_id, $temperatuur, $kuupyaev_kellaaeg, $temperatuur_id);
    $kask->execute();
}