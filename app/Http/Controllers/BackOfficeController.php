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

    public function statistics(Request $request): View
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

        $this->client = new Client();
        $accounts = [];
        $numberOffers = 0;
        $numberReservations = 0;
        $numberMessages = [];
        $prestations = [];

        //accounts
        try{
            $requestGetAccounts =$this->client->get(env('API_URL') . 'account?all=true', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $request->session()->get('token')
                ]
            ]);
            $data = json_decode($requestGetAccounts->getBody()->getContents());
            $accounts = $data->data;
            $result = [];
            foreach ($accounts as $account){
                if (array_key_exists($account->account_type, $result)){
                    $result[$account->account_type] += 1;
                }else{
                    $result[$account->account_type] = 1;
                }
            }
            $accounts = $result;

            $requestGetNameTypeAccount = $this->client->get(env('API_URL') . 'account_type?all=true', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $request->session()->get('token')
                ]
            ]);
            $data = json_decode($requestGetNameTypeAccount->getBody()->getContents());
            $data = $data->data;

            foreach ($accounts as $key => $value){
                foreach ($data as $account_type){
                    if ($key == $account_type->uuid){
                        $accounts[$account_type->type] = $value;
                        unset($accounts[$key]);
                    }
                }
            }
        }
        catch (\Exception $e){
            error_log($e->getMessage());
        }


        //numberMessages
        try {
            $requestGetMessages = $this->client->get(env('API_URL') . 'message?all=true', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $request->session()->get('token')
                ]
            ]);
            $data = json_decode($requestGetMessages->getBody()->getContents());
            $data = $data->data;
            $result = [];
            foreach ($data as $message){
                if (array_key_exists($message->creation_date, $result)){
                    $result[$message->creation_date] += 1;
                }else{
                    $result[$message->creation_date] = 1;
                }
            }
            $numberMessages = $result;

        }catch (\Exception $e){
            error_log($e->getMessage());
        }
        //dd($numberMessages);

        error_log("statistics");
        return view('main_backoffice', [
            'file_path' => $this->view_path . "statistics",
            'stack_css' => 'styles_statistics',
            'accounts' => json_encode($accounts, JSON_FORCE_OBJECT),
            'numberOffers' => $numberOffers,
            'numberReservations' => $numberReservations,
            'numberMessages' => json_encode($numberMessages, JSON_FORCE_OBJECT),
            'prestations' => $prestations
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

    public function users(Request $request): View
    {
        $request->validate([
            'page' => 'integer|min:1',
        ]);
        $page = $request->input('page');
        $page = $page ? $page : 1;

        $offset = ($page - 1) * 10;
        $limit = 10;
        $numberPages = 0;

        try{
            $this->client = new Client();
            $requestGetAccounts =$this->client->get(env('API_URL') . 'account?all=true&offset='.$offset.'&limit='.$limit, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $request->session()->get('token')
                ]
            ]);
            $data = json_decode($requestGetAccounts->getBody()->getContents());

            $numberPages = round($data->total / 10);
            $accounts = $data->data;

            $typeAccount = $this->client->get(env('API_URL') . 'account_type?all=true', [
                'headers' => [
                    "Authorization" => "Bearer " . $request->session()->get('token')
                ]
            ]);

            $typeAccount = json_decode($typeAccount->getBody()->getContents());
            $typeAccount = $typeAccount->data;

            foreach ($accounts as $account){
                foreach ($typeAccount as $type){
                    if ($account->account_type == $type->uuid){
                        $account->account_type = $type->type;
                    }
                }
            }

            return view('main_backoffice', [
                'file_path' => $this->view_path . "travelers",
                'stack_css' => 'styles_travelers',
                'accounts' => $accounts,
                'numberPages' => $numberPages,
                'account_type' => $typeAccount
            ]);
        }catch (\Exception $e) {
            error_log($e->getMessage());
            return view('main_backoffice', [
                'file_path' => $this->view_path . "travelers",
                'stack_css' => 'styles_travelers',
                'accounts' => [],
                'numberPages' => 1
            ]);
        }

    }

    public function staff(): View
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

    public function updateUser(Request $request, $id, $information)
    {
        $body = json_decode($information, true);

        try{
            $this->client = new Client();
            $response = $this->client->put(env('API_URL') . 'account?uuid='.$id, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $request->session()->get('token')
                ],
                'json' => $body
            ]);
            $data = json_decode($response->getBody()->getContents());
            if ($response->getStatusCode() === 200){
                return redirect('/backoffice/users', 302, [], false)->with('success', 'User updated');
            }
            return redirect('/backoffice/users', 302, [], false)->withErrors([
                "error" => $response->message
            ]);
        }catch (\Exception $e){
            error_log($e->getMessage());
            return redirect('/backoffice/users', 302, [], false)->withErrors([
                "error" => $e->getMessage()
            ]);
        }

    }

    public function deleteUser(Request $request, $id)
    {
        try{
            $this->client = new Client();
            $response = $this->client->delete(env('API_URL') . 'account?uuid='.$id, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $request->session()->get('token')
                ]
            ]);
            $data = json_decode($response->getBody()->getContents());
            if ($response->getStatusCode() === 200){
                return redirect('/backoffice/users', 302, [], false)->with('success', 'User deleted');
            }
            return redirect('/backoffice/users', 302, [], false)->withErrors([
                "error" => $response->message
            ]);
        }catch (\Exception $e){
            error_log($e->getMessage());
            return redirect('/backoffice/users', 302, [], false)->withErrors([
                "error" => $e->getMessage()
            ]);
        }
    }

    public function setAccountType(Request $request, $account, $account_type)
    {
        try {
            $this->client = new Client();
            $response = $this->client->put(env('API_URL') . 'account?uuid=' . $account, [
                'headers' => [
                    "Authorization" => "Bearer " . $request->session()->get('token')
                ],
                'json' => [
                    "account_type" => $account_type
                ]
            ]);
        }catch (\Exception $e){
            error_log($e->getMessage());
            return redirect('/backoffice/users', 302, [], false)->withErrors([
                "error" => $e->getMessage()
            ]);
        }

        return redirect('/backoffice/users', 302, [], false)->with('success', 'Changement de compte effectué');
    }

}
