<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="./projekt/style.css">
</head>
<body>
    <nav>
        <ul>
            <li class="theme" id="blocks">blocks
                <span class="tooltiptext">Kattints rá a téma kiválasztásához!</span>
            </li>
            <li class="theme" id="items">items
                <span class="tooltiptext">Kattints rá a téma kiválasztásához!</span>
            </li>
            <li class="theme" id="mobs">mobs
                <span class="tooltiptext">Kattints rá a téma kiválasztásához!</span>
            </li>
            <li class="theme" id="foods">foods
                <span class="tooltiptext">Kattints rá a téma kiválasztásához!</span>
            </li>
            <li>toplist</li>
        </ul>
    </nav>
    <div id="sizeContainer">
        <button class="card" id="4x4">4x4
            <span class="tooltiptext">Kattints rá a méret kiválasztásához!</span>
        </button>
        <button class="card" id="4x6">4x6
            <span class="tooltiptext">Kattints rá a méret kiválasztásához!</span>
        </button>
        <button class="card" id="6x6">6x6
            <span class="tooltiptext">Kattints rá a méret kiválasztásához!</span>
        </button>
        <button class="card" id="6x8">6x8
            <span class="tooltiptext">Kattints rá a méret kiválasztásához!</span>
        </button>
    </div>
    <div id="container"></div>
    <button id="restart">Új játék</button>
    <div id="points">Pontszám:1000/1000</div>
</body>
<script>
    const RestartBtn = document.getElementById("restart");
    const PointsDiv = document.getElementById("points");
    const SizeContainer = document.getElementById("sizeContainer");
    const Container = document.getElementById("container");
    const SizeButtons = document.getElementsByClassName("card");
    const ThemeButtons = document.getElementsByClassName("theme");

    var sources = [];

    var BGImage = "<?php 
        $database = mysqli_connect("localhost", "root", null, "kartyamemoria");
        $das = $database->query("SELECT * FROM kartyamemoria.images WHERE ID = 1");
        $database->close();
        if ($row = $das->fetch_assoc()){
            echo "data:image/jpeg;base64," . base64_encode($row['image']);
        }
    ?>";

    var AllImages = [];
    var Images = [];

    var PlayButtons = [];
    var Pairs = 0;

    var Height;
    var Width;

    var playing = false;
    var Points = 1000;
    var LastClickedBtn = null;
    var LastClickedBtnSeconds;

    var theme = "blocks";
    ThemeButtons[0].style.color = "white";

    document.cookie = `points=${Points}`;
    document.cookie = `nev=${theme}`;

    for (i = 0; i < ThemeButtons.length; i++){
        let x = i;
        ThemeButtons[i].addEventListener("click", () => {
            if (!playing){
                theme = ThemeButtons[x].id;
                document.cookie = `nev=${theme}`;
                for (j = 0; j < ThemeButtons.length; j++){
                    ThemeButtons[j].style.color = "black";
                }
                ThemeButtons[x].style.color = "white";
            }
        });
    }

    for (j = 0; j < SizeButtons.length; j++){
        let x = j;
        SizeButtons[j].addEventListener("click", () => SetupGame(SizeButtons[x].innerHTML));    
    }

    RestartBtn.addEventListener("click", Restart);

    Resize();
    window.onresize = Resize;

    function SetupGame(Size){
        playing = true;
        Points = 1000;
        document.cookie = `points=${Points}`;
        Pairs = 0;
        PointsDiv.innerHTML = `Pontszám:${Points}/1000`;
        PointsDiv.value = 1000;
        LastClickedBtn = null

        SizeContainer.style.visibility = "hidden";
        Container.style.visibility = "visible";

        Width = parseInt(Size.slice(0,Size.indexOf("x")));
        Height = parseInt(Size.slice(Size.indexOf("x") + 1, Size.length));

        PointsDiv.style.visibility = "visible";

        AllImages = [...sources[theme]]; // getting every image of the current theme
        Images = [];

        for (i = 0; i < Width * Height / 2; i++){
            randomImageIND = Math.floor(Math.random() * AllImages.length);
            Images[i] = AllImages[randomImageIND];
            Images[i + Width * Height / 2] = AllImages[randomImageIND];
            AllImages.splice(randomImageIND, 1);
        }
        

        CreateButtons(Width, Height, theme);
    }

    function CreateButtons(width, height, theme){
        grid = "";
        for (y = 0; y < height; y++){
            PlayButtons[y] = []
            for (x = 0; x < width; x++){
                // card parts
                let card = document.createElement("div");
                let card_inner = document.createElement("div");
                let card_front = document.createElement("img");
                let card_back = document.createElement("img");

                // card
                card.style.height = parseFloat(Container.style.height.slice(Container.style.height, -2)) / (height+1) + "px";
                card.style.width = parseFloat(card.style.height.slice(card.style.height, -2)) / 1.5 + "px";
                card.id = "card";
                PlayButtons[y][x] = card;
                Container.appendChild(card);

                // inner card
                card_inner.addEventListener('click', () => BtnClick(card_inner));
                card_inner.classList.add("card_inner");
                card_inner.flipped = false;
                card_inner.flippable = true;
                card.appendChild(card_inner);

                // front of the card
                card_front.src = BGImage;
                card_front.classList.add("card_face");
                card_front.classList.add("front");
                card_inner.appendChild(card_front);

                // back of the card
                randomBG = Math.floor(Math.random() * Images.length);
                card_back.src = Images[randomBG];
                Images.splice(randomBG, 1);
                card_back.classList.add("card_face");
                card_back.classList.add("back");
                card_inner.appendChild(card_back);

                if (y == 0){
                    grid += "auto ";
                }
            }
        }
        Container.style.gridTemplateColumns = grid;
    }

    function BtnClick(div){
        // check if the button is pressable
        if (div.flipped == false && div.flippable){
            div.classList.add("flipped");
            div.flipped = true;
            div.flippable = false;
            CheckPair(div);
        }
    }

    function Resize(){
        Container.style.width = window.innerWidth * 0.8 + "px";
        Container.style.height = window.innerHeight * 0.8 + "px";
        Container.style.left = window.innerWidth / 2 - (parseFloat(Container.style.width.slice(Container.style.width, -2)) / 2) + "px";
        Container.style.top = window.innerHeight / 2 - (parseFloat(Container.style.height.slice(Container.style.height, -2)) / 2) + "px";

        for (i = 0; i < SizeButtons.length; i++){
            SizeButtons[i].style.width = window.innerWidth * 0.1 + "px";
            SizeButtons[i].style.height = window.innerWidth * 0.15 + "px";
        }

        if (PlayButtons.length > 0){
            for (y = 0; y < PlayButtons.length; y++){
                for (x = 0; x < PlayButtons[y].length; x++){
                    PlayButtons[y][x].style.height = parseFloat(Container.style.height.slice(Container.style.height, -2)) / (PlayButtons.length+1) + "px";
                    PlayButtons[y][x].style.width = parseFloat(PlayButtons[y][x].style.height.slice(PlayButtons[y][x].style.height, -2)) / 1.5 + "px";
                }
            }
        }
    }

    function CheckPair(div){
        if(LastClickedBtn == null){ // ha ez az első megnyomott gomb
            LastClickedBtn = div;
            LastClickedBtnSeconds = Math.floor(Date.now()/1000);
        }
        else if (div.children[1].src == LastClickedBtn.children[1].src){ // ha megegyezik a két kiválasztott kártya
            div.flippable = false;
            LastClickedBtn.flippable = false;
            LastClickedBtn = null;
            Pairs++;
            if (Math.floor(Date.now()/1000) > LastClickedBtnSeconds + 3){
                Points -= 10;
                PointsDiv.innerHTML = `Pontszám:${Points}/1000`;
                document.cookie = `points=${Points}`;
            }
            if (Pairs == Width * Height / 2) {
                setTimeout(EndGame, 200);
            }
        }
        else if (div.children[1].src != LastClickedBtn.children[1].src) { // ha nem egyezik meg a két -||-
            Points -= 20;
            Points = Points < 0 ? 0 : Points;
            PointsDiv.innerHTML = `Pontszám:${Points}/1000`;
            document.cookie = `points=${Points}`;
            for (y = 0; y < PlayButtons.length; y++){
                for (x = 0; x < PlayButtons[y].length; x++){
                    PlayButtons[y][x].children[0].flippable = false;
                }
            }
            setTimeout(() => {
                div.flipped = false;
                div.classList.remove("flipped");
                LastClickedBtn.flipped = false;
                LastClickedBtn.classList.remove("flipped");
                LastClickedBtn = null;
                for (y = 0; y < PlayButtons.length; y++) {
                    for (x = 0; x < PlayButtons[y].length; x++) {
                        PlayButtons[y][x].children[0].flippable = true;
                    }
                }
            },1000);
        }
    }

    function EndGame(){
        fetch("savePoints.php", {
            method: "POST",
        })
        .then(response => response.text())
        .then(data => console.log(data))
        .catch(error => console.error("Error:", error));

        alert("Nyertél! ");
        RestartBtn.style.visibility = "visible";
    }

    function Restart(){
        for (y = 0; y < PlayButtons.length; y++) {
            for (x = 0; x < PlayButtons[y].length; x++) {
                Container.removeChild(PlayButtons[y][x]);
            }
        }
        SizeContainer.style.visibility = "visible";
        Container.style.visibility = "hidden";
        RestartBtn.style.visibility = "hidden";
        PointsDiv.style.visibility = "hidden";
        playing = false;
    }

    sources["blocks"] = "<?php
    $database = mysqli_connect("localhost", "root", null, "kartyamemoria");
    $asd = $database->query("SELECT * FROM kartyamemoria.images WHERE theme = 'blocks'");
    $database->close();
    $i = 0;
    while ($row = $asd->fetch_assoc()) {
        $i++;
        if ($i == 24) { // a képek száma
            echo "data:image/jpeg;base64," . base64_encode($row['image']);
        } else {
            echo "data:image/jpeg;base64," . base64_encode($row['image']) . "_";
        }
    }
    ?>".split("_");
    sources["foods"] = "<?php
    $database = mysqli_connect("localhost", "root", null, "kartyamemoria");
    $asd = $database->query("SELECT * FROM kartyamemoria.images WHERE theme = 'foods'");
    $database->close();
    $i = 0;
    while ($row = $asd->fetch_assoc()) {
        $i++;
        if ($i == 24) { // a képek száma
            echo "data:image/jpeg;base64," . base64_encode($row['image']);
        } else {
            echo "data:image/jpeg;base64," . base64_encode($row['image']) . "_";
        }
    }
    ?>".split("_");
    sources["items"] = "<?php
    $database = mysqli_connect("localhost", "root", null, "kartyamemoria");
    $asd = $database->query("SELECT * FROM kartyamemoria.images WHERE theme = 'items'");
    $database->close();
    $i = 0;
    while ($row = $asd->fetch_assoc()) {
        $i++;
        if ($i == 24) { // a képek száma
            echo "data:image/jpeg;base64," . base64_encode($row['image']);
        } else {
            echo "data:image/jpeg;base64," . base64_encode($row['image']) . "_";
        }
    }
    ?>".split("_");
    sources["mobs"] = "<?php
    $database = mysqli_connect("localhost", "root", null, "kartyamemoria");
    $asd = $database->query("SELECT * FROM kartyamemoria.images WHERE theme = 'mobs'");
    $database->close();
    $i = 0;
    while ($row = $asd->fetch_assoc()) {
        $i++;
        if ($i == 24) { // a képek száma
            echo "data:image/jpeg;base64," . base64_encode($row['image']);
        } else {
            echo "data:image/jpeg;base64," . base64_encode($row['image']) . "_";
        }
    }
    ?>".split("_");
</script>
<?php 
    // $database = mysqli_connect("localhost", "root", null, "mysql");

    // $database->query("CREATE DATABASE IF NOT EXISTS kartyamemoria CHARACTER SET utf8 COLLATE utf8_general_ci;");
    // $database->query("CREATE TABLE IF NOT EXISTS kartyamemoria.images(id INT NOT NULL PRIMARY KEY AUTO_INCREMENT, theme VARCHAR(100) NOT NULL, image mediumblob NOT NULL);");
    // $database->query("CREATE TABLE IF NOT EXISTS kartyamemoria.points(name VARCHAR(100) NOT NULL, points INT(11) NOT NULL, date VARCHAR(100) NOT NULL);");

    // foreach (scandir("./projekt/images/images") as $imagedir){
    //     if ($imagedir != "." && $imagedir != ".."){
    //         foreach (scandir("./projekt/images/images/$imagedir") as $image){
    //             if ($image != "." && $image != ".."){
    //                 $database->query("INSERT INTO kartyamemoria.images (theme, image) VALUES ('$imagedir', LOAD_FILE('I:/II.projekt/projekt/images/images/$imagedir/$image'));");
    //             }
    //         }
    //     }
    // }

    // $database->close();
?>
</html>