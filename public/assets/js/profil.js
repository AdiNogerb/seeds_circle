const map = document.getElementById('map');
const spinner = document.getElementById('spinner');
const greenIcon = L.icon({
    iconUrl: '../public/assets/img/leaf-green.png',
    shadowUrl: '../public/assets/img/leaf-shadow.png',
    iconSize:     [38, 95], // size of the icon
    shadowSize:   [50, 64], // size of the shadow
    iconAnchor:   [22, 94], // point of the icon which will correspond to marker's location
    shadowAnchor: [4, 62],  // the same for the shadow
    popupAnchor:  [-3, -76] // point from which the popup should open relative to the iconAnchor
});

// Variable pour URL de requête API
let url = 'https://opendata.agencebio.org/api/gouv/operateurs/?activite=Production&produit=Graines&nb=100';


// Fonction pour rechercher sur l'API les producteurs de graines dans un rayon de 200km
getSellers = (lat, lng) => {
    console.log(lat, lng);

    // Condition : si l'utilisateur s'est déplacé alors supprimer le tableau datas du localStorage
    if (localStorage.getItem('position')) {
        let position = JSON.parse(localStorage.getItem('position'));
        if (Math.abs(lat - position[0]) > 0.1 || Math.abs(lng - position[1]) > 0.1) {
            localStorage.removeItem('datas');
            let position = [lat, lng];
            localStorage.setItem('position', JSON.stringify(position));    
        }
    } else {
        let position = [lat, lng];
        localStorage.setItem('position', JSON.stringify(position));
    }

    // Spécification de la latitude et longitude de l'opérateur dans la requête URL
    url += `&lat=${lat}`;
    url += `&lng=${lng}`;

    // Génération de la map sur le lieu de l'utilisateur
    var map = L.map('map').setView([lat, lng], 13);

    // Appel du modèle de tuile
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);

    // Placement de la première tuile sur le lieu de l'utilisateur
    var marker = L.marker([lat, lng]).addTo(map);

    // Fonction pour les taches à effectuer sur les items du tableau suivant
    const itemList = (item) => {
        let adresseOperateur = item.adressesOperateurs;
        let name = item.denominationcourante;
        if (name == null) {
            name = '';
        }
        let mail = item.email;
        let adresseObj = adresseOperateur[0];
        let site = item.siteWebs;
        let latSell = adresseObj.lat;
        let lngSell = adresseObj.long;
        let place = adresseObj.lieu;
        let postal = adresseObj.codePostal;
        let city = adresseObj.ville;
        if (mail != null && site.length != 0) {
            let siteObj = site[0];
            let siteUrl = siteObj.url;                
            var marker = L.marker([latSell, lngSell], {icon: greenIcon}).addTo(map).bindPopup(`
            <p class="fw-bold">${name}</p>
            <p>${place}, ${postal}, ${city}</p>
            <p><a href="mailto: ${mail}">${mail}</a></p>
            <a href="${siteUrl}" target="_blank">${siteUrl}</a>`);
        } else if (mail != null && site.length == 0){
            var marker = L.marker([latSell, lngSell], {icon: greenIcon}).addTo(map).bindPopup(`
            <p class="fw-bold">${name}</p>
            <p>${place}, ${postal}, ${city}</p>
            <a href="mailto: ${mail}">${mail}</a>`);
        } else if (mail == null && site.length != 0){
            let siteObj = site[0];
            let siteUrl = siteObj.url;                
            var marker = L.marker([latSell, lngSell], {icon: greenIcon}).addTo(map).bindPopup(`
            <p class="fw-bold">${name}</p>
            <p>${place}, ${postal}, ${city}</p>
            <a href="${siteUrl}" target="_blank">${siteUrl}</a>`);
        } else {
            var marker = L.marker([latSell, lngSell], {icon: greenIcon}).addTo(map).bindPopup(`
            <p class="fw-bold">${name}</p>
            <p>${place}, ${postal}, ${city}</p>`);
        }
    }    

    // Condition : si le localStorage contient déjà les données, ne pas executer de requête URL
    if (localStorage.getItem('datas')) {
        datas = JSON.parse(localStorage.getItem('datas'));
        // Boucle sur le tableau d'objet pour récupérer latitude et longitude des commercants
            datas.forEach(item => {
                itemList(item);
        });
        spinner.classList.add('d-none');
    } else {
        // Requête URL
        fetch(url)
        .then(response => {
            return response.json();
        })
        .then(datas =>{
            // Boucle sur le tableau d'objet pour récupérer latitude et longitude des commercants
            localStorage.setItem('datas', JSON.stringify(datas.items));
            datas.items.forEach(item => {
                itemList(item);
            });
            spinner.classList.add('d-none');
        })
    }
}


// Une fois la géolocalisation de l'utilisateur récupérée, appel de la fonction getSellers avec latitude et longitude de l'utilisateur en paramètre
navigator.geolocation.getCurrentPosition((position) => {
getSellers(position.coords.latitude, position.coords.longitude);
});
