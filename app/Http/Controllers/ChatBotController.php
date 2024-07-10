<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class ChatBotController extends Controller
{
    public function handle(Request $request)
    {
        $message = $request->input('keyword');

        $keywords = $this->extractKeywords($message);
        Log::info('Mots-clés extraits: ' . implode(', ', $keywords));

        $responseText = 'Pas de réponse';

        if (!empty($keywords)) {
            $client = new Client();
            foreach ($keywords as $keyword) {
                try {
                    $response = $client->get(env("API_URL") . 'chatbot', [
                        'query' => ['keyword' => $keyword],
                        'verify' => false
                    ]);
                    $responseBody = json_decode($response->getBody(), true);
                    Log::info('Réponse de l\'API:', ['body' => $responseBody]);

                    if ($response->getStatusCode() === 200 && isset($responseBody['data']) && count($responseBody['data']) > 0) {
                        $responseText = $responseBody['data'][0]['text'];
                        break;
                    }
                } catch (RequestException $e) {
                    Log::error('Erreur lors de la communication avec l\'API du chatbot', [
                        'message' => $e->getMessage(),
                        'response' => $e->hasResponse() ? (string) $e->getResponse()->getBody() : 'Aucune réponse'
                    ]);
                }
            }
        }

        return response()->json([
            'messages' => [
                ['text' => $responseText]
            ]
        ]);
    }

    public function extractKeywords($message)
    {
        $sentence = strtolower($message);
        $sentence = preg_replace('/[^\p{L}\p{N}\s]/u', '', $sentence);
        $words = explode(' ', $sentence);
        $commonWords = array(
            'je', 'tu', 'il', 'elle', 'nous', 'vous', 'ils', 'elles', 'le', 'la', 'les', 'un', 'une', 'des',
            'et', 'à', 'de', 'en', 'du', 'pour', 'avec', 'sur', 'par', 'au', 'aux', 'dans', 'qui', 'que',
            'quoi', 'dont', 'est', 'sont', 'serait', 'avoir', 'être', 'comment', 'faire', 'où', 'quand', 'pourquoi', 'combien',
            'quel', 'quelle', 'quels', 'quelles', 'ce', 'cette', 'ces', 'cet', 'ceux', 'celles', 'ceci', 'cela', 'ça', 'ici', 'là',
        );
        $filteredWords = array_filter($words, function ($word) use ($commonWords) {
            return !in_array($word, $commonWords) && strlen($word) > 2;
        });
        $wordFrequency = array_count_values($filteredWords);
        arsort($wordFrequency);
        $totalWords = count($filteredWords);
        $tfIdfScores = [];
        foreach ($wordFrequency as $word => $count) {
            $tf = $count / $totalWords;
            $tfIdfScores[$word] = $tf;
        }
        arsort($tfIdfScores);
        $keywords = array_keys($tfIdfScores);

        return $keywords;
    }

    public function adminIndex(Request $request)
    {
        try {
            $client = new Client();
            $response = $client->get(env("API_URL") . 'chatbot?all=true', [
                'headers' => [
                    'Accept' => 'application/json',
                ],
                'verify' => false,
            ]);

            if ($response->getStatusCode() === 200) {
                $responseData = json_decode($response->getBody(), true);
                $keywords = $responseData['data'] ?? [];
            } else {
                $keywords = [];
            }

            return view('backoffice.chatbot.index', compact('keywords'));
        } catch (RequestException $e) {
            Log::error('Erreur lors de la récupération des mots-clés', [
                'message' => $e->getMessage(),
                'response' => $e->hasResponse() ? (string) $e->getResponse()->getBody() : 'Aucune réponse'
            ]);

            return redirect('/login')->with('error', 'Erreur lors de la récupération des mots-clés : ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $dataInput = $request->validate([
            'keyword' => 'required|string',
            'text' => 'required|string',
        ]);

        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $request->session()->get('token'),
        ];
        $body = [
            'keyword' => $dataInput['keyword'],
            'text' => $dataInput['text']
        ];

        try {
            $client = new Client();

            $response = $client->post(env("API_URL") . 'chatbot', [
                'headers' => $headers,
                'json' => $body,
                'verify' => false,
            ]);

            $responseBody = json_decode($response->getBody()->getContents(), true);

            Log::info('API Response: ', ['response' => $responseBody]);

            if ($response->getStatusCode() === 201) {
                return redirect('/chatbot/admin')->with('success', 'Chatbot created');
            }
            return redirect('/chatbot/admin')->with('error', 'Erreur lors de la création du mot-clé');
        } catch (RequestException $e) {
            Log::error('Erreur lors de la création du mot-clé', [
                'message' => $e->getMessage(),
                'response' => $e->hasResponse() ? (string) $e->getResponse()->getBody() : 'Aucune réponse'
            ]);
            return redirect('/chatbot/admin')->with('error', 'Erreur lors de la création du mot-clé : ' . $e->getMessage());
        }
    }

    public function update(Request $request)
    {
        $request->validate([
            'uuid' => 'required|string',
            'text' => 'required|string',
        ]);

        $token = $request->session()->get('token');

        if (!$token) {
            return response()->json(['error' => 'Token manquant. Veuillez vous authentifier.'], 401);
        }

        try {
            $client = new Client();
            $response = $client->put(env("API_URL") . 'chatbot?uuid=' . $request->uuid, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'text' => $request->text,
                ],
                'verify' => false,
            ]);

            if ($response->getStatusCode() === 200) {
                return response()->json(['success' => 'Chatbot updated'], 200);
            } else {
                return response()->json(['error' => 'Erreur lors de la mise à jour du mot-clé'], $response->getStatusCode());
            }
        } catch (RequestException $e) {
            return response()->json([
                'error' => 'Erreur lors de la mise à jour du mot-clé : ' . $e->getMessage()
            ], 500);
        }
    }




    public function destroy(Request $request, $uuid)
    {
        $token = $request->session()->get('token');

        if (!$token) {
            return redirect('/chatbot/admin')->with('error', 'Token manquant. Veuillez vous authentifier.');
        }

        Log::info('Jeton utilisé pour l\'authentification', ['token' => $token]);

        try {
            $client = new Client();
            $response = $client->delete(env("API_URL") . 'chatbot?uuid=' . $uuid, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Accept' => 'application/json',
                ],
                'verify' => false,
            ]);

            if ($response->getStatusCode() === 200) {
                return redirect('/chatbot/admin')->with('success', 'Chatbot deleted');
            } else {
                Log::error('Erreur lors de la suppression du mot-clé', [
                    'response_status' => $response->getStatusCode(),
                    'response_body' => (string) $response->getBody()
                ]);
                return redirect('/chatbot/admin')->with('error', 'Erreur lors de la suppression du mot-clé');
            }
        } catch (RequestException $e) {
            Log::error('Erreur lors de la suppression du mot-clé', [
                'message' => $e->getMessage(),
                'response' => $e->hasResponse() ? (string) $e->getResponse()->getBody() : 'Aucune réponse'
            ]);
            return redirect('/chatbot/admin')->with('error', 'Erreur lors de la suppression du mot-clé : ' . $e->getMessage());
        }
    }
}
