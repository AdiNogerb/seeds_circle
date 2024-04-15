// Variable pour URL de requête API
let url = 'https://opendata.agencebio.org/api/gouv/operateurs/?activite=Production&produit=Graines&nb=100';


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
            var marker = L.marker([latSell, lngSell]).addTo(map);
        });
    })
}


// Une fois la géolocalisation de l'utilisateur récupérée, appel de la fonction getSellers avec latitude et longitude de l'utilisateur en paramètre
navigator.geolocation.getCurrentPosition((position) => {
getSellers(position.coords.latitude, position.coords.longitude);
});