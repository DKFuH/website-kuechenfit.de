<?php
declare(strict_types=1);

/**
 * Zentrale Stammdaten der Website.
 *
 * Rechtsperson und Kontaktdaten sind identisch mit kuechen-klas.de/
 * kuechenfolieren.de (dieselbe Firma, dieselbe Person) -- nur schema_url/
 * schema_image/schema_description sind bewusst auf diese eigenstaendige
 * Domain bezogen, damit das JSON-LD nicht auf kuechen-klas.de zeigt.
 */
return [
    'brand_name' => 'KüchenFit',
    'legal_name' => 'Daniel Klas',
    'owner_name' => 'Daniel Klas',
    'email' => 'kontakt@kuechen-klas.de',
    'phone_display' => '06763 5189970',
    'phone_e164' => '+4967635189970',
    'schema_url' => 'https://kuechenfit.de',
    'schema_image' => 'https://kuechenfit.de/assets/og-image.jpg',
    'schema_description' => 'Küchenmodernisierung in Sohren und Umgebung: Fronten, Arbeitsplatte, Spüle, Licht und Stauraum vom Tischlermeister – ein Service von Klas Küchen.',
    'address' => [
        'street' => 'Hauptstraße 31A',
        'postal_code' => '55487',
        'city' => 'Sohren',
        'region' => 'Rheinland-Pfalz',
        'country_code' => 'DE',
    ],
    'opening_hours' => [
        [
            'label' => 'Donnerstag & Freitag',
            'days' => ['Thursday', 'Friday'],
            'opens' => '10:00',
            'closes' => '18:00',
        ],
        [
            'label' => 'Samstag',
            'days' => ['Saturday'],
            'opens' => '09:00',
            'closes' => '13:00',
        ],
    ],
    'founder_sameas' => [
        'https://www.linkedin.com/in/daniel-klas-465199224/',
        'https://www.facebook.com/KuechenKlas',
    ],
    'founder_knows_about' => [
        'Küchenmodernisierung',
        'Tischlerhandwerk',
        'Küchenrenovierung',
        'Küchenmontage',
    ],
];
