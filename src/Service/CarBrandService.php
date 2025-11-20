<?php
// src/Service/CarBrandService.php
namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class CarBrandService
{
    private HttpClientInterface $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function getAllBrands(): array
    {
        $response = $this->client->request('GET', 'https://www.carqueryapi.com/api/0.3/?cmd=getMakes&sold_in_us=1');
        $raw = $response->getContent(false);
        // Nettoyer la réponse JSON bizarre
        $raw = preg_replace('/^.*?\{/', '{', $raw);
        $data = json_decode($raw, true);

        $brands = [];
        foreach ($data['Makes'] ?? [] as $make) {
            $brands[$make['make_display']] = $make['make_display'];
        }

        ksort($brands);
        return $brands;
    }
}
