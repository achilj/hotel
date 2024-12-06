<?php
$sql = "SELECT * FROM tblbedrijf
        LEFT JOIN tblgemeente ON tblbedrijf.GemeenteFK = tblgemeente.PKGemeente";
$result = $conn->query($sql);
$info = mysqli_fetch_assoc($result);
?>