<?php
declare(strict_types=1);

if (!function_exists('klas_business')) {
    /** @return array<string, mixed> */
    function klas_business(): array
    {
        static $business = null;
        if (is_array($business)) {
            return $business;
        }

        $loaded = require dirname(__DIR__) . '/config/business.php';
        if (!is_array($loaded)) {
            throw new RuntimeException('Business configuration is invalid.');
        }

        $business = $loaded;
        return $business;
    }
}

if (!function_exists('klas_service_cities')) {
    /** @return array<int, string> */
    function klas_service_cities(): array
    {
        return ['Sohren', 'Simmern', 'Kirchberg', 'Bad Kreuznach', 'Bernkastel-Kues', 'Kastellaun', 'Idar-Oberstein'];
    }
}

if (!function_exists('klas_postal_address_schema')) {
    /**
     * @param array<string, mixed> $address
     * @return array<string, mixed>
     */
    function klas_postal_address_schema(array $address): array
    {
        return array_filter([
            '@type' => 'PostalAddress',
            'streetAddress' => $address['street'] ?? null,
            'addressLocality' => $address['city'] ?? null,
            'addressRegion' => $address['region'] ?? null,
            'postalCode' => $address['postal_code'] ?? null,
            'addressCountry' => $address['country_code'] ?? null,
        ]);
    }
}

if (!function_exists('klas_business_schema')) {
    /** @return array<string, mixed> */
    function klas_business_schema(): array
    {
        $business = klas_business();
        $address = $business['address'];
        $hours = [];

        foreach ($business['opening_hours'] as $entry) {
            $days = $entry['days'];
            $hours[] = [
                '@type' => 'OpeningHoursSpecification',
                'dayOfWeek' => count($days) === 1 ? $days[0] : $days,
                'opens' => $entry['opens'],
                'closes' => $entry['closes'],
            ];
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'HomeAndConstructionBusiness',
            'name' => $business['brand_name'],
            'legalName' => $business['legal_name'],
            'founder' => array_filter([
                '@type' => 'Person',
                'name' => $business['owner_name'],
                'sameAs' => $business['founder_sameas'] ?? [],
                'knowsAbout' => $business['founder_knows_about'] ?? [],
            ]),
            'image' => $business['schema_image'],
            'description' => $business['schema_description'],
            'url' => $business['schema_url'],
            'telephone' => $business['phone_e164'],
            'email' => $business['email'],
            'address' => klas_postal_address_schema($address),
            'geo' => ['@type' => 'GeoCoordinates', 'latitude' => 49.9358, 'longitude' => 7.3283],
            'openingHoursSpecification' => $hours,
            'areaServed' => array_merge(
                array_map(
                    static fn (string $city): array => ['@type' => 'City', 'name' => $city],
                    klas_service_cities()
                ),
                [['@type' => 'AdministrativeArea', 'name' => 'Hunsrück']]
            ),
            'sameAs' => [
                'https://www.instagram.com/klas.kuechen',
                'https://www.facebook.com/klaskuechen/',
            ],
            'priceRange' => '$$$$',
        ];
    }
}
