const SizeContainer = document.getElementById("sizeContainer");
const Container = document.getElementById("container");
const SizeButtons = document.getElementsByClassName("card");
const ImageCollections = { "teachers": ["angela.jpg", "erzsi.jpg", "geri.jpg", "isti.jpg", "kaci.jpg", "orgo.jpg", "trieb.jpg", "varadi.jpg", "angela.jpg", "erzsi.jpg", "geri.jpg", "isti.jpg", "kaci.jpg", "orgo.jpg", "trieb.jpg", "varadi.jpg"]};

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

Resize();
window.onresize = Resize;

function SetupGame(Size){
    // eltüntetem a pályaméret kiválasztást
    SizeContainer.style.visibility = "hidden";
    Container.style.visibility = "visible";

    Width = parseInt(Size.slice(0,Size.indexOf("x")));
    Height = parseInt(Size.slice(Size.indexOf("x") + 1, Size.length));

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
    if(LastClickedBtn == null){
        LastClickedBtn = div;
        LastClickedBtnSeconds = Math.floor(Date.now()/1000);
    }
    else if (div.children[1].style.backgroundImage == LastClickedBtn.children[1].style.backgroundImage){
        div.style.visibility = "hidden";
        LastClickedBtn.style.visibility = "hidden";
        LastClickedBtn = null;
        Pairs++;
        if (Math.floor(Date.now()/1000) > LastClickedBtnSeconds + 3){
            Points -= 10;
        }
        if (Pairs == Width * Height / 2) {
            setTimeout(EndGame, 200);
        }
    }
    else if (div.children[1].style.backgroundImage != LastClickedBtn.children[1].style.backgroundImage) {
        Points -= 20;
        Points = Points < 0 ? 0 : Points;
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
    alert("Nyertél! " + Points);
}