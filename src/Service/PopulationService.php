<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

    class PopulationService
    {
        private HttpClientInterface $client;

        public function __construct(HttpClientInterface $client)
        {
            $this->client = $client;
        }

        public function getCommunes(): array
        {
            $response = $this->client->request(
                'GET',
                'https://geo.api.gouv.fr/communes?fields=nom,population'
            );

            return $response->toArray(); // tableau associatif PHP
        }

        public function getCommuneByName(string $name): ?array
        {
            $response = $this->client->request(
                'GET',
                'https://geo.api.gouv.fr/communes',
                [
                    'query' => [
                        'nom' => $name,
                        'fields' => 'nom,population'
                    ]
                ]
            );

            $data = $response->toArray();

            return $data[0] ?? null; // on prend la première commune trouvée
        }
    }
