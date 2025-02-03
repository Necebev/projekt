const RestartBtn = document.getElementById("restart");
const PointsDiv = document.getElementById("points");
const SizeContainer = document.getElementById("sizeContainer");
const Container = document.getElementById("container");
const SizeButtons = document.getElementsByClassName("card");
var ImageCollections = { "teachers": ["angela.jpg", "erzsi.jpg", "geri.jpg", "isti.jpg", "kaci.jpg", "orgo.jpg", "trieb.jpg", "varadi.jpg", "angela.jpg", "erzsi.jpg", "geri.jpg", "isti.jpg", "kaci.jpg", "orgo.jpg", "trieb.jpg", "varadi.jpg"]};
// asd
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
    // eltüntetem a pályaméret kiválasztást
    SizeContainer.style.visibility = "hidden";
    Container.style.visibility = "visible";

    Width = parseInt(Size.slice(0,Size.indexOf("x")));
    Height = parseInt(Size.slice(Size.indexOf("x") + 1, Size.length));

    RestartBtn.style.visibility = "visible";
    PointsDiv.style.visibility = "visible";

    ImageCollections = { "teachers": ["angela.jpg", "erzsi.jpg", "geri.jpg", "isti.jpg", "kaci.jpg", "orgo.jpg", "trieb.jpg", "varadi.jpg", "angela.jpg", "erzsi.jpg", "geri.jpg", "isti.jpg", "kaci.jpg", "orgo.jpg", "trieb.jpg", "varadi.jpg"] };

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
            let card_front = document.createElement("div");
            let card_back = document.createElement("div");

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
            card_front.classList.add("card_face");
            card_front.classList.add("front");
            card_front.style.backgroundImage = "url('./images/hatlap.png')";
            card_inner.appendChild(card_front);

            // back of the card
            randomBG = Math.floor(Math.random() * ImageCollections["teachers"].length);
            card_back.style.backgroundImage = `url("./images/${Object.keys(ImageCollections)[0]}/${ImageCollections[Object.keys(ImageCollections)[0]][randomBG]}")`;
            ImageCollections[Object.keys(ImageCollections)[0]].splice(randomBG, 1);
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
    else if (div.children[1].style.backgroundImage == LastClickedBtn.children[1].style.backgroundImage){ // ha megegyezik a két kiválasztott kártya
        div.flippable = false;
        LastClickedBtn.flippable = false;
        LastClickedBtn = null;
        Pairs++;
        if (Math.floor(Date.now()/1000) > LastClickedBtnSeconds + 3){
            Points -= 10;
            PointsDiv.innerHTML = `Pontszám:${Points}/1000`;
            PointsDiv.value = Points;
        }
        if (Pairs == Width * Height / 2) {
            setTimeout(EndGame, 200);
        }
    }
    else if (div.children[1].style.backgroundImage != LastClickedBtn.children[1].style.backgroundImage) { // ha nem egyezik meg a két -||-
        Points -= 20;
        Points = Points < 0 ? 0 : Points;
        PointsDiv.innerHTML = `Pontszám:${Points}/1000`;
        PointsDiv.value = Points;
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
    Points = 1000;
    Pairs = 0;
    PointsDiv.innerHTML = `Pontszám:${Points}/1000`;
    PointsDiv.value = 1000;
    LastClickedBtn = null
}