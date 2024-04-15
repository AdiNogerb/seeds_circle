document.addEventListener('DOMContentLoaded', function() {
    const barter = document.getElementById('barter');
    const need = document.getElementById('need');
    const picturesForm = document.querySelector('#picturesForm');
    const decriptionForm = document.querySelector('#decriptionForm');

    function hideforNeed() {
        picturesForm.classList.add('d-none');
        decriptionForm.classList.add('d-none');
    }

    function showforBarter() {
        picturesForm.classList.remove('d-none');
        decriptionForm.classList.remove('d-none');
    }

    need.addEventListener('click', hideforNeed);
    barter.addEventListener('click', showforBarter);
});
