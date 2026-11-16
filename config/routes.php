<?php
/**
 * Table des routes : URL réécrite => Contrôleur@action.
 * {id} = nombre, {slug} = nom dans l'URL, {ressource} = marques | familles | fragrances | eaux | contenances.
 */

/* ---------- Front ---------- */
$router->get('',                              'HomeController@index');
$router->get('parfums',                       'ParfumController@index');
$router->get('parfums/{id}',                  'ParfumController@show');
$router->get('parfums/{id}-{slug}',           'ParfumController@show');
$router->post('parfums/{id}/avis',            'AvisController@enregistrer');
$router->post('parfums/{id}/avis/supprimer',  'AvisController@supprimer');

$router->get('la-maison',                     'PageController@maison');
$router->get('univers',                       'PageController@univers');
$router->get('contact',                       'PageController@contact');
$router->post('contact',                      'PageController@envoyerContact');

$router->get('inscription',                   'AuthController@formulaireInscription');
$router->post('inscription',                  'AuthController@inscription');
$router->get('connexion',                     'AuthController@formulaireConnexion');
$router->post('connexion',                    'AuthController@connexion');
$router->post('deconnexion',                  'AuthController@deconnexion');

/* ---------- Back-office (rôle admin) ---------- */
$router->get('admin',                              'AdminDashboardController@index');

$router->get('admin/parfums',                      'AdminParfumController@index');
$router->get('admin/parfums/creer',                'AdminParfumController@creer');
$router->post('admin/parfums/creer',               'AdminParfumController@enregistrer');
$router->get('admin/parfums/{id}/modifier',        'AdminParfumController@modifier');
$router->post('admin/parfums/{id}/modifier',       'AdminParfumController@mettreAJour');
$router->post('admin/parfums/{id}/supprimer',      'AdminParfumController@supprimer');

$router->get('admin/avis',                         'AdminAvisController@index');
$router->post('admin/avis/{id}/{id}/supprimer',    'AdminAvisController@supprimer');

$router->get('admin/{ressource}',                  'AdminReferenceController@index');
$router->get('admin/{ressource}/creer',            'AdminReferenceController@creer');
$router->post('admin/{ressource}/creer',           'AdminReferenceController@enregistrer');
$router->get('admin/{ressource}/{id}/modifier',    'AdminReferenceController@modifier');
$router->post('admin/{ressource}/{id}/modifier',   'AdminReferenceController@mettreAJour');
$router->post('admin/{ressource}/{id}/supprimer',  'AdminReferenceController@supprimer');
