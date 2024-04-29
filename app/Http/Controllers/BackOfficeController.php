<?php

//require_once "../../../vendor/autoload.php";
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use GuzzleHttp\Client;

class BackOfficeController extends Controller
{
    /*
     * Controller du backoffice qui gère les routes du backoffice
     * et les données à afficher sur les pages du backoffice
     */

    var string $view_path = "backoffice.";
    var Client $client;
    /*
     * Certaines méthodes sont accessibles uniquement par les administrateurs
     * d'autres sont accessibles par les administrateurs et les moderateurs
     * ou accessible via restriction de certaines actions
     * les utilisateurs lambda n'ont pas accès au backoffice
     * Voir pour créer des roles pour les utilisateurs
     * et pourvoir les customiser
     */
    public function index(): View
    {
        /*
         * Ce commentaire est un placeholder pour expliquer ce que fait la méthode index
         * à la personne qui implémentera la méthode index
         *
         * Pour le moment la méthode index renvoie une vue de la page d'accueil du backoffice
         * Le but est de rajouter des requêtes pour récupérer des données à afficher sur la page d'accueil
         * certaines données peuvent être utilisé dans un graphe via chart.js
         *
         * Accessible pour tous le staff du backoffice
         *
         * Ne pas oublier de rajouter des commentaires pour expliquer le code
         * ne pas oublier le clean Code, en cas de question => discord du Riri
         * @return View
         */
        error_log("index");

        return view('main_backoffice', [
            'file_path' => $this->view_path . "index",
            'stack_css' => 'styles_index'
        ]);
    }

    public function statistics(): View
    {
        /*
         * Ce commentaire est un placeholder pour expliquer ce que fait la méthode statistics
         * à la personne qui implémentera la méthode statistics
         *
         * Pour le moment la méthode statistics renvoie une vue vide
         * Le but est de rajouter des requêtes pour récupérer des données à afficher sur la page
         * certaines données peuvent être utilisé dans un graphe via chart.js
         * Le but si possible est d'avoir afficher toutes les données importantes de la DBB
         * tout en effectuant des traitements comme des moyennes, des sommes, des pourcentages
         *
         * Accessible pour tous le staff du backoffice
         *
         * Ne pas oublier de rajouter des commentaires pour expliquer le code
         * ne pas oublier le clean Code, en cas de question => discord du Riri
         * @return View
         */
        error_log("statistics");
        return view('main_backoffice', [
            'file_path' => $this->view_path . "statistics",
            'stack_css' => 'styles_statistics'
        ]);
    }

    public function suggests(): View
    {
        /*
         * Ce commentaire est un placeholder pour expliquer ce que fait la méthode suggests
         * à la personne qui implémentera la méthode suggests
         *
         * Pour le moment la méthode suggests renvoie une vue vide
         * Le but est de rajouter des requêtes pour récupérer les suggestions des utilisateurs
         * et de les afficher sur la page
         *
         * Les suggestions peuvent être des suggestions de fonctionnalités, de design, de contenu
         * et sont soumises via un formalaire sur le site, cette fonction n'est pas là pour vérifié
         * le contenu des suggestions mais pour les afficher
         *
         * Accessible pour tous le staff du backoffice
         *
         * Ne pas oublier de rajouter des commentaires pour expliquer le code
         * ne pas oublier le clean Code, en cas de question => discord du Riri
         * @return View
         */
        return view('backoffice.suggests');
    }

    public function travelers(Request $request): View
    {
        /*
         * Ce commentaire est un placeholder pour expliquer ce que fait la méthode travelers
         * à la personne qui implémentera la méthode travelers
         *
         * Pour le moment la méthode travelers renvoie une vue avec des données sous JSON
         * Le but est de rajouter des requêtes pour récupérer les voyageurs et les afficher sur la page
         * tout en ajoutant une pagination pour afficher les voyageurs par page
         * et aussi on ajoutant des méthodes pour filtrer les voyageurs par date, par nom, par prénom
         * et des actions d'édition, de suppression, de création de voyageurs
         *
         * Accessible pour tous les administrateurs et les moderateurs
         * les moderateurs ne peuvent pas supprimer les voyageurs ni les éditer
         * seulement en ajouter si besoin il faut un administrateur pour effectuer ces actions
         *
         * Ne pas oublier de rajouter des commentaires pour expliquer le code
         * ne pas oublier le clean Code, en cas de question => discord du Riri
         * @return View

        error_log("travelers");
        return view('main_backoffice', [
            'file_path' => $this->view_path . "travelers",
            'stack_css' => 'styles_travelers'
        ]);
         */

        $request->validate([
            'page' => 'required|integer',
        ]);
        $page = $request->input('page');


        $this->client = new Client();
        $requestGetAccount = new Request("GET",$_ENV['API_URL'] . '/account_type?type=voyageur', [
            'headers' => [
                "Authorization" => "Bearer" . $request->session()->get('token')
            ]
        ]);

        $response = $this->client->send($requestGetAccount);
        $data = json_decode($response->getBody()->getContents());
        $account_type_id = $data->data[0]->id;

        //attention limit et offset
        $requestGetTravelers = new Request("GET",$_ENV['API_URL'] . '/account?account_type_id=' . $account_type_id, [
            'headers' => [
                "Authorization" => "Bearer " . $request->session()->get('token')
            ]
        ]);

        $promise = $this->client->sendAsync($requestGetTravelers)->then(function ($response) {
            $data = json_decode($response->getBody()->getContents());
            return view('backoffice.travelers', [
                'travelers' => $data->data,
                //'pagination' => $data->meta->pagination
            ]);
        },
        function ($exception) {
            error_log($exception->getMessage());
        });
        $promise->wait();

    }

    public function prestations(): View
    {
        /*
        * Ce commentaire est un placeholder pour expliquer ce que fait la méthode prestations
        * à la personne qui implémentera la méthode prestations
        *
        * Pour le moment la méthode prestations renvoie une vue vide
        * Le but est de rajouter des requêtes pour récupérer les prestataires et les afficher sur la page
        * tout en ajoutant une pagination pour afficher les prestataires par page
        * et aussi on ajoutant des méthodes pour filtrer les prestataires par date, par nom, par prénom
        * et des actions d'édition, de suppression, de création de prestataires, vérification des prestataires
        * aussi on affichera si le prestataire est rataché à une entreprise ou non
        *
        * Accessible pour tous les administrateurs et les moderateurs
        * les moderateurs ne peuvent pas supprimer les voyageurs ni les éditer
        * seulement en ajouter si besoin il faut un administrateur pour effectuer ces actions
        *
        * Ne pas oublier de rajouter des commentaires pour expliquer le code
        * ne pas oublier le clean Code, en cas de question => discord du Riri
        * @return View
        */
        return view('backoffice.prestations');
    }

    public function prestationsCompanies(): View
    {
        /*
        * Ce commentaire est un placeholder pour expliquer ce que fait la méthode prestationsCompanies
        * à la personne qui implémentera la méthode prestationsCompanies
        *
        * Pour le moment la méthode prestationsCompanies renvoie une vue vide
        * Le but est de rajouter des requêtes pour récupérer les sociétés de prestations et les afficher sur la page
        * tout en ajoutant une pagination pour afficher les sociétés de prestations par page
        * et aussi on ajoutant des méthodes pour filtrer les sociétés de prestations par date, par nom, par prénom
        * et des actions d'édition, de suppression, de création de sociétés de prestations, vérification des sociétés
        * aussi on affichera si la société est bien associé à une maison du commerce
        *
        * Accessible pour tous les administrateurs et les moderateurs
        * les moderateurs ne peuvent pas supprimer les voyageurs ni les éditer
        * seulement en ajouter si besoin il faut un administrateur pour effectuer ces actions
        *
        * Ne pas oublier de rajouter des commentaires pour expliquer le code
        * ne pas oublier le clean Code, en cas de question => discord du Riri
        * @return View
        */
        return view('backoffice.prestations-companies');
    }

    public function providers(): View
    {
        /*
         * Ce commentaire est un placeholder pour expliquer ce que fait la méthode providers
         * à la personne qui implémentera la méthode providers
         *
         * Pour le moment la méthode providers renvoie une vue vide
         * Le but est de rajouter des requêtes pour récupérer les bailleurs et les afficher sur la page
         * tout en ajoutant une pagination pour afficher les bailleurs par page
         * et aussi on ajoutant des méthodes pour filtrer les bailleurs par date, par nom, par prénom
         * et des actions d'édition, de suppression, de création de sociétés de prestations, vérification des bailleurs
         * et aussi voir si le bailleur est rataché à une entreprise ou non
         *
        * Accessible pour tous les administrateurs et les moderateurs
        * les moderateurs ne peuvent pas supprimer les voyageurs ni les éditer
        * seulement en ajouter si besoin il faut un administrateur pour effectuer ces actions
         *
         * Ne pas oublier de rajouter des commentaires pour expliquer le code
         * ne pas oublier le clean Code, en cas de question => discord du Riri
         * @return View
         */
        return view('backoffice.donors');
    }

    public function supports(): View
    {
        /*
         * Pas encore d'idée de ce que fait la méthode supports
         *
         * Ne pas oublier de rajouter des commentaires pour expliquer le code
         * ne pas oublier le clean Code, en cas de question => discord du Riri
         * @return View
         */
        return view('backoffice.supports');
    }

    public function permissions(): View
    {
        /*
         * Ce commentaire est un placeholder pour expliquer ce que fait la méthode permissions
         * à la personne qui implémentera la méthode permissions
         *
         * Pour le moment la méthode permissions renvoie une vue vide
         * Le but est de rajouter des requêtes pour récupérer tout les utilisateurs et les afficher sur la page
         * tout en ajoutant une pagination pour afficher les utilisateurs par page
         * et aussi on ajoutant des méthodes pour filtrer les utilisateurs par date, par nom, par prénom, etc ...
         * et des actions d'édition des rôles de chaque utilisateur,
         * également on pourra créer des rôles personnalisés pour chaque utilisateur
         * avec des permissions personnalisées un peu à la linux (rwx)
         *
         * Seul les administrateurs ont accès à cette page pour gérer les permissions des utilisateurs
         * Les administrateurs ne peuvent pas modifier les permissions des autres administrateurs
         *
         * Ne pas oublier de rajouter des commentaires pour expliquer le code
         * ne pas oublier le clean Code, en cas de question => discord du Riri
         * @return View
         */
        return view('backoffice.permissions');
    }

    public function settings(): View
    {
        /*
         * Ce commentaire est un placeholder pour expliquer ce que fait la méthode settings
         * à la personne qui implémentera la méthode settings
         *
         * Aucune idée de ce que fait la méthode settings
         *
         * Ne pas oublier de rajouter des commentaires pour expliquer le code
         * ne pas oublier le clean Code, en cas de question => discord du Riri
         * @return View
         */
        return view('backoffice.settings');
    }

    public function __invoke() : View
    {
        /*
         * Méthode __invoke qui renvoie la vue du backoffice par défaut si on appelle le controller
         * pour éviter une page blanche en cas de pépin
         */
        return view('backoffice');
    }

}
