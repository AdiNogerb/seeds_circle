// Constantes
const spans = document.querySelectorAll('span[data-id]');
const titleEvent = document.getElementById('titleEvent');
const dateEvent = document.getElementById('dateEvent');
const descriptionDiv = document.getElementById('descriptionDiv');
const cityEvent = document.getElementById('cityEvent');
const descriptionEvent = document.getElementById('descriptionEvent');
const monthTab = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
const mapDiv = document.getElementById('mapDiv');
const greenIcon = L.icon({
    iconUrl: '../public/assets/img/leaf-green.png',
    shadowUrl: '../public/assets/img/leaf-shadow.png',
    iconSize:     [38, 95], // size of the icon
    shadowSize:   [50, 64], // size of the shadow
    iconAnchor:   [22, 94], // point of the icon which will correspond to marker's location
    shadowAnchor: [4, 62],  // the same for the shadow
    popupAnchor:  [-3, -76] // point from which the popup should open relative to the iconAnchor
});


// Variables
let events = [];


// Fetch sur le fichier JSON
fetch('../events.json')
    .then((response) => {
        return response.json();
    })
    .then((datas) => {
        events = datas.evenements;
        console.log(events);
});


// Fonctions
const showMap = (e) => {
    if (document.querySelector('.leaflet-container')) {
        mapDiv.innerHTML = '<div id="map"></div>';
    }
    let id = e.target.getAttribute('data-id');
    let lat = events[id]['latitude'];
    let lng = events[id]['longitude'];
    mapDiv.classList.remove('d-none');
    var map = L.map('map').setView([lat, lng], 12);
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(map);
    var marker = L.marker([lat, lng], {icon: greenIcon}).addTo(map)
}

const resumEvent = (e) => {
    window.scrollTo(0, 500);
    showMap(e);
    descriptionDiv.classList.remove('d-none');
    let id = e.target.getAttribute('data-id');
    if (id == 'zero') {
        id = 0;
    }
    let title = events[id]['titre'];
    titleEvent.innerHTML = title;
    let city = events[id]['ville'];
    cityEvent.innerHTML = city;
    let description = events[id]['description'];
    descriptionEvent.innerHTML = description;
    let date = new Date(events[id]['date']);
    let day = date.getDate();
    let monthIndex = date.getMonth();
    let year = date.getFullYear();
    let dateFrench = day + ' ' + monthTab[monthIndex] + ' ' + year;
    dateEvent.innerHTML = dateFrench;
};


// Écouteur d'événement
spans.forEach(span => {
    span.addEventListener('click', resumEvent)
});