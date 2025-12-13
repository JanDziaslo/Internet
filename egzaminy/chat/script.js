function wyslij()
{
    var tresc = document.getElementById("wiadomosc").value;
    
    var nowawiadomosc = document.createElement("div");
    nowawiadomosc.className = "jolanta";
    nowawiadomosc.innerHTML = '<img src="img/jolka.jpg"><p>'  +tresc+'</p>';
    
    document.querySelector(".chat").appendChild(nowawiadomosc);
    
}
function generuj()
{
    var odpowiedzi = ["ciekawe czy ktos na to zerknie", "jak tam mija dzien?","nudzi mi sie", "ciekawe ile osob podwali ten kod z githuba?", "strone zrobil Jan Dziąsło", "Dziapko", "niedlugo wyjdzie winter mod do ets2", "okropnie wyglada ta strona ale przynajmniej dziala (chyba)", "dziwnie dziala to losowanie w javascript", "br jest jak trytytka"];
    
    var losowa = Math.floor(Math.random() * odpowiedzi.length);
    // console.log(losowa) juz nie potrzebne
    
    var nowawiadomosc = document.createElement("div");
    nowawiadomosc.className = "krzysiek";
    nowawiadomosc.innerHTML = '<img src="img/krzysiek.jpg"><p>'  +odpowiedzi[losowa]+'</p>';
    
    document.querySelector(".chat").appendChild(nowawiadomosc);
    nowawiadomosc.scrollIntoView()
}