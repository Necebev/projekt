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
            <li>blocks</li>
            <li>items</li>
            <li>mobs</li>
            <li>enchants</li>
            <li>toplist</li>
        </ul>
    </nav>
    <div id="sizeContainer">
        <button class="card">4x4</button>
        <button class="card">4x6</button>
        <button class="card">6x6</button>
        <button class="card">6x8</button>
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

    var sources = "<?php 
        $database = mysqli_connect("localhost", "root", null, "kartyamemoria"); 
        $asd = $database->query("SELECT * FROM kartyamemoria.images WHERE ID < 9;");
        $database->close();
        $i = 0;
        while ($row = $asd->fetch_assoc()){
            $i++;
            if ($i == 8){ // a képek száma
                echo "data:image/jpeg;base64," . base64_encode($row['image']);
            }
            else{
                echo "data:image/jpeg;base64," . base64_encode($row['image']) . "_";
            }
        }
    ?>";

    var BGImage = "<?php 
        $database = mysqli_connect("localhost", "root", null, "kartyamemoria");
        $das = $database->query("SELECT * FROM kartyamemoria.images WHERE ID = 9");
        $database->close();
        if ($row = $das->fetch_assoc()){
            echo "data:image/jpeg;base64," . base64_encode($row['image']);
        }
    ?>";

    var images = sources.split('_');
    var teacherImages = [];
    for (i = 0; i < images.length; i++){
        teacherImages[i] = images[i];
        teacherImages[i+images.length] = images[i];
    }

    var PlayButtons = [];
    var Pairs = 0;

    var Height;
    var Width;

    var Points = 1000;
    var LastClickedBtn = null;
    var LastClickedBtnSeconds;

    for (i = 0; i < SizeButtons.length; i++){
        let x = i;
        SizeButtons[i].addEventListener("click", () => SetupGame(SizeButtons[x].innerHTML));    
    }

    RestartBtn.addEventListener("click", Restart);

    Resize();
    window.onresize = Resize;

    function SetupGame(Size){
        Points = 1000;
        Pairs = 0;
        PointsDiv.innerHTML = `Pontszám:${Points}/1000`;
        PointsDiv.value = 1000;
        LastClickedBtn = null

        SizeContainer.style.visibility = "hidden";
        Container.style.visibility = "visible";

        Width = parseInt(Size.slice(0,Size.indexOf("x")));
        Height = parseInt(Size.slice(Size.indexOf("x") + 1, Size.length));

        PointsDiv.style.visibility = "visible";

        for (i = 0; i < images.length; i++){
            teacherImages[i] = images[i];
            teacherImages[i+images.length] = images[i];
        }

        CreateButtons(Width, Height);
    }

    function CreateButtons(width, height){
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
                randomBG = Math.floor(Math.random() * teacherImages.length);
                card_back.src = teacherImages[randomBG];
                teacherImages.splice(randomBG, 1);
                console.log(teacherImages);
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
            }
            if (Pairs == Width * Height / 2) {
                setTimeout(EndGame, 200);
            }
        }
        else if (div.children[1].src != LastClickedBtn.children[1].src) { // ha nem egyezik meg a két -||-
            Points -= 20;
            Points = Points < 0 ? 0 : Points;
            PointsDiv.innerHTML = `Pontszám:${Points}/1000`;
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
            },1500);
        }
    }

    function EndGame(){
        alert("Nyertél! ");
        RestartBtn.style.visibility = "visible";
        <?php 
            
        ?>;
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
    }


</script>
</html>