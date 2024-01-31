const form = document.getElementById('form-inscription');
const nomInput = document.getElementById('nom');
const prenomInput = document.getElementById('prenom');
const emailInput = document.getElementById('email');
const identifiant_iutInput = document.getElementById('identifiant_iut');
const passwordInput = document.getElementById('password');


// NOM

form.addEventListener('submit', function(event) {
    const regexLettre = /^[a-zA-Z]+$/;
    const regexEmail = /@etu\.iut-tlse3\.fr$/;
    const regexIdentifiant_iut = /^[a-z]{3}[0-9]{4}[a-z]{1}$/

    if (nomInput.value.length < 2 || !regexLettre.test(nomInput.value)) {
        event.preventDefault();


        nomInput.style.border = '1px solid red';
        nomerror.textContent = "Doit contenir au moins 2 caractères et aucun nombre ou caractère spécial";
    }

    if (prenomInput.value.length < 2 || !regexLettre.test(prenomInput.value)) {
        event.preventDefault();


        prenomInput.style.border = '1px solid red';
        prenomerror.textContent = "Doit contenir au moins 2 caractères et aucun nombre ou caractère spécial";
    }

    if (!regexEmail.test(emailInput.value)) {
        event.preventDefault();


        emailInput.style.border = '1px solid red';
        emailerror.textContent = "L'adresse email doit se finir avec @etu.iut-tlse3.fr";
    }

    if (passwordInput.value.length < 6 ) {
        event.preventDefault();


        passwordInput.style.border = '1px solid red';
        passworderror.textContent = "Doit contenir au moins 6 caractères";
    }

    if (!regexIdentifiant_iut.test(identifiant_iutInput.value)) {
        event.preventDefault();


        identifiant_iutInput.style.border = '1px solid red';
        identifiant_iuterror.textContent = "Doit être de cette forme ex: djs2313a";
    }
});