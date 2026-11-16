/* Maison Al Jazeera – petits comportements d'interface (le site fonctionne aussi sans JavaScript). */
(function () {
  'use strict';

  /* ---------- Recherche de l'en-tête : la loupe ouvre le champ, puis lance la recherche ---------- */
  document.querySelectorAll('.recherche').forEach(function (formulaire) {
    var champ = formulaire.querySelector('input');
    formulaire.addEventListener('submit', function (evenement) {
      if (champ.value.trim() === '') {
        evenement.preventDefault();
        formulaire.classList.add('ouverte');
        champ.focus();
      }
    });
    champ.addEventListener('blur', function () {
      if (champ.value.trim() === '') { formulaire.classList.remove('ouverte'); }
    });
  });

  /* ---------- Catalogue : envoi automatique du formulaire quand un filtre ou le tri change ---------- */
  var filtres = document.querySelector('form[data-envoi-auto]');
  if (filtres) {
    document.querySelectorAll('#filtres input[type="checkbox"], select[form="filtres"]').forEach(function (element) {
      element.addEventListener('change', function () { filtres.submit(); });
    });
  }

  /* ---------- Confirmation avant une suppression ---------- */
  document.querySelectorAll('form[data-confirmer]').forEach(function (formulaire) {
    formulaire.addEventListener('submit', function (evenement) {
      if (!window.confirm(formulaire.dataset.confirmer)) { evenement.preventDefault(); }
    });
  });

  /* ---------- Back-office : formulaire parfum ---------- */
  var formulaireParfum = document.getElementById('formulaire-parfum');
  if (formulaireParfum) {
    // 1. N'afficher que les fragrances regroupées par la famille choisie
    var carte = JSON.parse(formulaireParfum.dataset.carte || '{}');
    var famille = document.getElementById('idFamille');
    var aide = document.getElementById('aide-fragrances');

    var filtrerFragrances = function () {
      var autorisees = carte[famille.value] || [];
      aide.hidden = famille.value !== '';
      document.querySelectorAll('#fragrances [data-fragrance]').forEach(function (bloc) {
        var visible = autorisees.indexOf(Number(bloc.dataset.fragrance)) !== -1;
        bloc.classList.toggle('masquee', !visible);
        if (!visible) { bloc.querySelector('input').checked = false; }
      });
    };
    famille.addEventListener('change', filtrerFragrances);
    filtrerFragrances();

    // 2. Activer le champ prix seulement pour les contenances cochées
    formulaireParfum.querySelectorAll('input[data-prix]').forEach(function (cocher) {
      var prix = formulaireParfum.querySelector(cocher.dataset.prix);
      cocher.addEventListener('change', function () {
        prix.disabled = !cocher.checked;
        if (cocher.checked) { prix.focus(); }
      });
    });

    // 3. Aperçu de la photo choisie
    var photo = document.getElementById('photo');
    var apercu = document.getElementById('apercu-photo');
    photo.addEventListener('change', function () {
      if (photo.files && photo.files[0]) { apercu.src = URL.createObjectURL(photo.files[0]); }
    });
  }
})();
