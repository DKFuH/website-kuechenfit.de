<?php
declare(strict_types=1);

/**
 * Seitenbezogene JSON-LD-Auszeichnung fuer Rich Results.
 *
 * Ergaenzt das globale HomeAndConstructionBusiness-Schema aus
 * partials/head.php um FAQPage, BreadcrumbList und Service je Seite.
 * Muster uebernommen aus dem Hauptprojekt kuechen-klas.de
 * (app/faq-schema.php, partials/breadcrumb.php).
 */

require_once __DIR__ . '/business.php';

if (!function_exists('kk_seo_json_flags')) {
    function kk_seo_json_flags(): int
    {
        return JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT
            | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT;
    }
}

if (!function_exists('kk_seo_emit')) {
    /** @param array<string, mixed> $schema */
    function kk_seo_emit(array $schema): void
    {
        echo '<script type="application/ld+json">' . "\n";
        echo json_encode($schema, kk_seo_json_flags());
        echo "\n" . '</script>' . "\n";
    }
}

if (!function_exists('kk_render_faq_schema')) {
    /** @param array<int, array{question: string, answer: string}> $items */
    function kk_render_faq_schema(array $items): void
    {
        $mainEntity = [];
        foreach ($items as $item) {
            $question = trim(strip_tags((string) ($item['question'] ?? '')));
            $answer   = trim(strip_tags((string) ($item['answer'] ?? '')));
            if ($question === '' || $answer === '') {
                continue;
            }
            $mainEntity[] = [
                '@type' => 'Question',
                'name' => $question,
                'acceptedAnswer' => ['@type' => 'Answer', 'text' => $answer],
            ];
        }
        if ($mainEntity === []) {
            return;
        }
        kk_seo_emit(['@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => $mainEntity]);
    }
}

if (!function_exists('kk_render_breadcrumb_schema')) {
    /**
     * @param array<int, array{name: string, url: string}> $trail  Ohne "Home";
     *        das wird als Position 1 vorangestellt.
     */
    function kk_render_breadcrumb_schema(array $trail): void
    {
        $items = [[
            '@type' => 'ListItem',
            'position' => 1,
            'name' => 'Home',
            'item' => 'https://kuechenfit.de/',
        ]];
        $position = 2;
        foreach ($trail as $step) {
            $name = trim((string) ($step['name'] ?? ''));
            $url  = trim((string) ($step['url'] ?? ''));
            if ($name === '' || $url === '') {
                continue;
            }
            $items[] = ['@type' => 'ListItem', 'position' => $position++, 'name' => $name, 'item' => $url];
        }
        if (count($items) < 2) {
            return;
        }
        kk_seo_emit(['@context' => 'https://schema.org', '@type' => 'BreadcrumbList', 'itemListElement' => $items]);
    }
}

if (!function_exists('kk_render_webpage_schema')) {
    /**
     * WebPage-Auszeichnung fuer Ratgeber-/Info-Seiten ohne eigene Leistung.
     */
    function kk_render_webpage_schema(string $name, string $description, string $url): void
    {
        $business = klas_business();
        kk_seo_emit([
            '@context' => 'https://schema.org',
            '@type' => 'WebPage',
            'name' => $name,
            'description' => trim(strip_tags($description)),
            'url' => $url,
            'isPartOf' => ['@type' => 'WebSite', 'name' => $business['brand_name'], 'url' => 'https://kuechenfit.de/'],
            'publisher' => [
                '@type' => 'HomeAndConstructionBusiness',
                'name' => $business['brand_name'],
                'url' => 'https://kuechenfit.de/',
            ],
        ]);
    }
}

if (!function_exists('kk_render_service_schema')) {
    /**
     * Service-Auszeichnung fuer eine Leistungsseite (Provider = KuechenFit).
     */
    function kk_render_service_schema(string $name, string $description, string $url, string $serviceType = ''): void
    {
        $business = klas_business();
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Service',
            'name' => $name,
            'description' => trim(strip_tags($description)),
            'url' => $url,
            'provider' => [
                '@type' => 'HomeAndConstructionBusiness',
                'name' => $business['brand_name'],
                'url' => 'https://kuechenfit.de/',
                'telephone' => $business['phone_e164'],
            ],
            'areaServed' => array_merge(
                array_map(
                    static fn (string $city): array => ['@type' => 'City', 'name' => $city],
                    klas_service_cities()
                ),
                [['@type' => 'AdministrativeArea', 'name' => 'Hunsrück']]
            ),
        ];
        if ($serviceType !== '') {
            $schema['serviceType'] = $serviceType;
        }
        kk_seo_emit($schema);
    }
}
