// Variable pour URL de requête API
let url = 'https://opendata.agencebio.org/api/gouv/operateurs/?activite=Production&produit=Graines&nb=100';
let map = document.getElementById('map');
let spinner = document.getElementById('spinner');
let greenIcon = L.icon({
    iconUrl: 'public/assets/img/leaf-green.png',
    shadowUrl: 'public/assets/img/leaf-shadow.png',

    iconSize:     [38, 95], // size of the icon
    shadowSize:   [50, 64], // size of the shadow
    iconAnchor:   [22, 94], // point of the icon which will correspond to marker's location
    shadowAnchor: [4, 62],  // the same for the shadow
    popupAnchor:  [-3, -76] // point from which the popup should open relative to the iconAnchor
});

// Fonction pour rechercher sur l'API les producteurs de graines dans un rayon de 200km
getSellers = (lat, lng) => {
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

    // Requête URL
    fetch(url)
    .then(response => {
        return response.json();
    })
    .then(datas =>{
        // Console.log pour voir le tableau d'objet récupéré
        console.log(datas.items);

        // Boucle sur le tableau d'objet pour récupérer latitude et longitude des commercants
        datas.items.forEach(item => {
            let adresseOperateur = item.adressesOperateurs;
            let adresseObj = adresseOperateur[0];
            let latSell = adresseObj.lat;
            let lngSell = adresseObj.long;
            var marker = L.marker([latSell, lngSell], {icon: greenIcon}).addTo(map).bindPopup("I am a green leaf.");
        });

        spinner.classList.add('d-none');
    })
}


// Une fois la géolocalisation de l'utilisateur récupérée, appel de la fonction getSellers avec latitude et longitude de l'utilisateur en paramètre
navigator.geolocation.getCurrentPosition((position) => {
getSellers(position.coords.latitude, position.coords.longitude);
});
