<?php

namespace App\Http\Services;
use GuzzleHttp\Client;
use Illuminate\Http\JsonResponse;

class ViaCepService
{
    public function findCep(string $cep): JsonResponse
    {
        $client = new Client();

        try {
            $cep = preg_replace('/\D/', '', $cep);
            $validCep = preg_match('/^[0-9]{8}$/', $cep);

            if(!$validCep || $cep == "") {
                return response()->json(['message' => 'CEP inválido'], 400);
            }

            if ($cep != "") {
                $response = $client->get("https://viacep.com.br/ws/{$cep}/json/");

                $data = json_decode($response->getBody(), true);

                if (isset($data['erro']) && $data['erro'] === true) {
                    return response()->json(['message' => 'CEP inválido'], 400);
                }

                return response()->json($data, 200);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao consultar o CEP'], 500);
        }
    }
}
