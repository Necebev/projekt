<?php
    $database = mysqli_connect("localhost", "root", null, "kartyamemoria");
    $database->query("INSERT INTO kartyamemoria.points (name, points, date) VALUES ('" . $_COOKIE["nev"] . "', " . $_COOKIE["points"] . ", '" . date("Y-m-d_his") . "');");
    $database->close();
?>