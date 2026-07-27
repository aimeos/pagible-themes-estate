<?php

/**
 * @license MIT, https://opensource.org/license/mit
 */

namespace Tests;

use Aimeos\Cms\JsonSchema;
use Aimeos\Cms\Models\File;
use Aimeos\Cms\Models\Page;
use Aimeos\Cms\Validation;
use Carbon\CarbonImmutable;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;


class PropertyRenderingTest extends ThemeTestAbstract
{
    public function testCardsUseSingleImageWhileDetailsUseSlideshow(): void
    {
        $card = (string) file_get_contents( dirname( __DIR__ ) . '/views/property-item.blade.php' );
        $detail = (string) file_get_contents( dirname( __DIR__ ) . '/views/property.blade.php' );

        $this->assertStringContainsString( "cms::pic", $card );
        $this->assertStringNotContainsString( "cms::slideshow", $card );
        $this->assertStringContainsString( "cms::slideshow", $detail );
    }


    public function testFallbackAndExplicitMetadataRenderOnceInLayout(): void
    {
        view()->addNamespace( 'estate-test', __DIR__ . '/views' );
        $nav = new class {
            public function ancestors(): Collection
            {
                return collect();
            }

            public function items(): Collection
            {
                return collect();
            }
        };
        $page = $this->page();
        $page->setRelation( 'ancestorsAndSelf', collect() );
        $data = $this->property( ['text' => 'Fallback property description'] );
        $files = collect();

        $fallback = view( 'estate-test::property-layout', compact( 'data', 'files', 'nav', 'page' ) )->render();
        preg_match( '#<head>(.*?)</head>#s', $fallback, $matches );
        $fallbackHead = $matches[1] ?? '';

        $this->assertSame( 1, substr_count( $fallbackHead, '<meta name="description"' ) );
        $this->assertSame( 1, substr_count( $fallbackHead, '<meta property="og:title"' ) );
        $this->assertSame( 1, substr_count( $fallbackHead, '<meta name="twitter:title"' ) );
        $this->assertStringContainsString( 'Fallback property description', $fallbackHead );

        $page = $this->page();
        $page->forceFill( ['meta' => [
            Validation::entry( 'meta-tags', [
                'description' => 'Explicit property description',
                'keywords' => 'explicit',
            ], 'meta' ),
            Validation::entry( 'social-media', [
                'title' => 'Explicit social title',
                'description' => 'Explicit social description',
            ], 'meta' ),
        ]] );
        $page->setRelation( 'ancestorsAndSelf', collect() );

        $explicit = view( 'estate-test::property-layout', compact( 'data', 'files', 'nav', 'page' ) )->render();
        preg_match( '#<head>(.*?)</head>#s', $explicit, $matches );
        $explicitHead = $matches[1] ?? '';

        $this->assertSame( 1, substr_count( $explicitHead, '<meta name="description"' ) );
        $this->assertSame( 1, substr_count( $explicitHead, '<meta property="og:title"' ) );
        $this->assertSame( 1, substr_count( $explicitHead, '<meta name="twitter:title"' ) );
        $this->assertStringContainsString( 'Explicit property description', $explicitHead );
        $this->assertStringContainsString( 'Explicit social title', $explicitHead );
        $this->assertStringNotContainsString( 'Fallback property description', $explicitHead );
    }


    public function testAvailableFromUsesExplicitPropertyDate(): void
    {
        $html = $this->renderProperty( ['available_from' => '2026-09-01'] );
        $available = $this->json( $html, 'RealEstateListing' );
        $unscheduled = $this->json( $this->renderProperty(), 'RealEstateListing' );
        $item = $this->item( 'Available property', ['available_from' => '2026-09-01'] );
        $card = view( 'estate::property-item', [
            'item' => $item,
            'layout' => 'cards',
            'property' => $item->content[0],
        ] )->render();

        $this->assertSame( '2026-09-01', $available['availabilityStarts'] );
        $this->assertArrayNotHasKey( 'availabilityStarts', $unscheduled );
        $this->assertStringContainsString( '<time datetime="2026-09-01">', $html );
        $this->assertStringContainsString( '<time datetime="2026-09-01">', $card );
    }


    public function testContactUsesPropertySourceWithoutContactPageSchema(): void
    {
        $page = $this->page();
        $html = $this->renderProperty();
        $sold = $this->renderProperty( ['status' => 'sold'] );

        $this->assertStringContainsString( 'name="source" value="' . cmsroute( $page ) . '"', $html );
        $this->assertStringContainsString( 'id="name-property-' . $page->id . '"', $html );
        $this->assertStringContainsString( 'aria-describedby="contact-errors-property-' . $page->id . '"', $html );
        $this->assertStringContainsString( 'role="alert" aria-live="polite"', $html );
        $this->assertStringNotContainsString( '"@type": "ContactPage"', $html );
        $this->assertStringContainsString( 'href="#property-contact-' . $page->id . '"', $html );
        $this->assertStringContainsString( 'Ask about similar properties', $sold );
    }


    public function testCurrencySchemaRequiresUppercaseCode(): void
    {
        $schema = JsonSchema::build( 'content', 'property' );
        $variants = $schema['properties']['contents']['items']['anyOf'];
        $variant = collect( $variants )->first(
            fn( $item ) => ( $item['properties']['type']['enum'][0] ?? null ) === 'property'
        );
        $currency = $variant['properties']['data']['properties']['currency'];
        $raw = json_decode( (string) file_get_contents( dirname( __DIR__ ) . '/schema.json' ), true, flags: JSON_THROW_ON_ERROR );

        $this->assertSame( 3, $currency['minLength'] );
        $this->assertSame( 3, $currency['maxLength'] );
        $this->assertSame( '^[A-Z]{3}$', $currency['pattern'] );
        $this->assertTrue( $raw['content']['property']['fields']['currency']['uppercase'] );
        $this->assertContains( 'villa', array_column( $raw['content']['property']['fields']['property_type']['options'], 'value' ) );
        $this->assertContains( 'land', array_column( $raw['content']['property']['fields']['property_type']['options'], 'value' ) );
        $this->assertContains( 'warehouse', array_column( $raw['content']['property']['fields']['property_type']['options'], 'value' ) );
        $this->assertContains( 'industrial', array_column( $raw['content']['property']['fields']['property_type']['options'], 'value' ) );
    }


    public function testGalleryImagesAreIncludedInPropertyJson(): void
    {
        $files = collect( [
            'image-1' => ( new File() )->forceFill( [
                'id' => 'image-1',
                'name' => 'First image',
                'path' => 'images/first.jpg',
                'previews' => (object) [],
                'description' => (object) ['en' => 'First image'],
            ] ),
            'image-2' => ( new File() )->forceFill( [
                'id' => 'image-2',
                'name' => 'Second image',
                'path' => 'images/second.jpg',
                'previews' => (object) [],
                'description' => (object) ['en' => 'Second image'],
            ] ),
        ] );
        $html = $this->renderProperty( [
            'files' => [
                ['id' => 'image-1', 'type' => 'file'],
                ['id' => 'image-2', 'type' => 'file'],
                ['id' => 'missing-image', 'type' => 'file'],
            ],
        ], $files );
        $schema = $this->json( $html, 'RealEstateListing' );
        $gallery = $this->json( $html, 'ImageGallery' );

        $this->assertSame( [
            cmsurl( 'images/first.jpg' ),
            cmsurl( 'images/second.jpg' ),
        ], $schema['image'] );
        $this->assertCount( 2, $gallery['image'] );
    }


    public function testMissingImagesUseEstatePlaceholder(): void
    {
        $item = $this->item( 'Property without image' );
        $card = view( 'estate::property-item', [
            'item' => $item,
            'layout' => 'cards',
            'property' => $item->content[0],
        ] )->render();

        $this->assertStringContainsString( 'property-gallery-empty', $this->renderProperty() );
        $this->assertStringContainsString( 'property-image-empty', $card );
        $this->assertStringContainsString( 'No image available', $card );
        $this->assertStringNotContainsString( 'aria-label="Open property', $card );
        $this->assertStringContainsString( 'aria-labelledby="property-title-', $card );
        $this->assertStringContainsString( 'aria-describedby="property-meta-', $card );
    }


    public function testPropertyDocumentsRenderAsDownloads(): void
    {
        $files = collect( [
            'document-1' => ( new File() )->forceFill( [
                'id' => 'document-1',
                'name' => 'Floor plan.pdf',
                'mime' => 'application/pdf',
                'path' => 'documents/floor-plan.pdf',
                'previews' => (object) [],
                'description' => (object) [],
            ] ),
        ] );
        $html = $this->renderProperty( [
            'documents' => [[
                'title' => 'Floor plan',
                'file' => ['id' => 'document-1', 'type' => 'file'],
            ]],
        ], $files );

        $this->assertStringContainsString( '<h2>Documents</h2>', $html );
        $this->assertStringContainsString( 'href="' . cmsurl( 'documents/floor-plan.pdf' ) . '" download', $html );
        $this->assertStringContainsString( 'Floor plan', $html );
        $this->assertStringContainsString( '<span class="property-document-type">PDF</span>', $html );
    }


    public function testMarkdownIsRemovedFromExcerptsAndMetadata(): void
    {
        $text = '## Bright **home** in [Berlin](https://example.com)';
        $item = $this->item( 'Markdown property', ['text' => $text] );
        $card = view( 'estate::property-item', [
            'item' => $item,
            'layout' => 'cards',
            'property' => $item->content[0],
        ] )->render();
        $schema = $this->json( $this->renderProperty( ['text' => $text] ), 'RealEstateListing' );

        $this->assertStringContainsString( 'Bright home in Berlin', $card );
        $this->assertStringNotContainsString( '**home**', $card );
        $this->assertSame( 'Bright home in Berlin', (string) $schema['description'] );
    }


    public function testGermanNumberFormatting(): void
    {
        app()->setLocale( 'de' );

        $html = $this->renderProperty( ['price' => 3250000, 'area' => 310] );

        $this->assertStringContainsString( '3.250.000 EUR', $html );
        $this->assertStringContainsString( '310 m²', $html );
    }


    public function testItemListJsonContainsOnlyPaginatorItems(): void
    {
        $action = new LengthAwarePaginator(
            [$this->item( 'First property' ), $this->item( 'Second property' )],
            12,
            2,
            2,
            ['path' => '/properties']
        );
        $data = (object) [
            'id' => 'properties',
            'layout' => 'cards',
            'order' => '-created_at',
            'title' => 'Properties',
            'parent-page' => (object) ['value' => null],
        ];

        $html = view( 'estate::properties', [
            'action' => (object) [
                'items' => $action,
                'filters' => (object) [
                    'sort' => (string) request('sort', '-created_at'),
                    'type' => strtolower(trim((string) request('type', ''))),
                    'offer' => strtolower(trim((string) request('offer', ''))),
                    'status' => strtolower(trim((string) request('status', ''))),
                    'city' => trim((string) request('city', '')),
                    'available_by' => trim((string) request('available_by', '')),
                    'rooms_min' => request()->filled('rooms_min') ? (int) request('rooms_min') : null,
                ],
                'options' => (object) [
                    'property_types' => [['value' => 'apartment', 'label' => 'Apartment']],
                    'offer_types' => [
                        ['value' => 'sale', 'label' => 'Sale'],
                        ['value' => 'rent', 'label' => 'Rent'],
                    ],
                    'statuses' => [['value' => 'available', 'label' => 'Available']],
                ],
            ],
            'data' => $data,
            'page' => $this->page( 'Properties', 'properties' ),
        ] )->render();
        $schema = $this->json( $html, 'ItemList' );

        $this->assertCount( 2, $schema['itemListElement'] );
        $this->assertSame( [3, 4], array_column( $schema['itemListElement'], 'position' ) );
        $this->assertSame( ['First property', 'Second property'], array_column( array_column( $schema['itemListElement'], 'item' ), 'name' ) );
        $this->assertStringContainsString( 'name="status"', $html );
        $this->assertStringNotContainsString( 'property-filter-more', $html );
        $this->assertStringContainsString( 'property-status-available', $html );

        $data->filters = false;
        $withoutFilters = view( 'estate::properties', [
            'action' => (object) [
                'items' => $action,
                'filters' => (object) [
                    'sort' => (string) request('sort', '-created_at'),
                    'type' => strtolower(trim((string) request('type', ''))),
                    'offer' => strtolower(trim((string) request('offer', ''))),
                    'status' => strtolower(trim((string) request('status', ''))),
                    'city' => trim((string) request('city', '')),
                    'available_by' => trim((string) request('available_by', '')),
                    'rooms_min' => request()->filled('rooms_min') ? (int) request('rooms_min') : null,
                ],
                'options' => (object) [
                    'property_types' => [['value' => 'apartment', 'label' => 'Apartment']],
                    'offer_types' => [
                        ['value' => 'sale', 'label' => 'Sale'],
                        ['value' => 'rent', 'label' => 'Rent'],
                    ],
                    'statuses' => [['value' => 'available', 'label' => 'Available']],
                ],
            ],
            'data' => $data,
            'page' => $this->page( 'Properties', 'properties' ),
        ] )->render();
        $this->assertStringNotContainsString( 'property-list-toolbar', $withoutFilters );
    }


    public function testPropertyListUsesCanonicalFilterValues(): void
    {
        $items = new LengthAwarePaginator(
            [$this->item( 'Filtered property' )],
            1,
            10,
            1,
            ['path' => '/properties']
        );
        $html = view( 'estate::properties', [
            'action' => (object) [
                'items' => $items,
                'filters' => (object) [
                    'sort' => '-created_at',
                    'type' => '',
                    'offer' => 'sale',
                    'status' => '',
                    'city' => 'Berlin',
                    'available_by' => '',
                    'rooms_min' => null,
                ],
                'options' => (object) [
                    'property_types' => [['value' => 'apartment', 'label' => 'Apartment']],
                    'offer_types' => [
                        ['value' => 'sale', 'label' => 'Sale'],
                        ['value' => 'rent', 'label' => 'Rent'],
                    ],
                    'statuses' => [['value' => 'available', 'label' => 'Available']],
                ],
            ],
            'data' => (object) [
                'id' => 'properties',
                'layout' => 'cards',
                'order' => '-created_at',
                'title' => 'Properties',
                'parent-page' => (object) ['value' => null],
            ],
            'page' => $this->page( 'Properties', 'properties' ),
        ] )->render();

        $this->assertStringContainsString( 'name="city" value="Berlin"', $html );
        $this->assertStringContainsString( 'value="sale" selected', $html );
        $this->assertStringNotContainsString( 'property-filter-chip', $html );
        $this->assertStringNotContainsString( 'Active filters', $html );
    }


    public function testSellerMetadataRequiresConfiguredSiteName(): void
    {
        config( ['app.name' => 'Estate Agency'] );
        $configured = $this->json( $this->renderProperty(), 'RealEstateListing' );

        $this->assertSame( 'Estate Agency', $configured['offers']['seller']['name'] );
        $this->assertSame( url( '/' ), $configured['offers']['seller']['url'] );

        config( ['app.name' => 'Laravel'] );
        $default = $this->json( $this->renderProperty(), 'RealEstateListing' );

        $this->assertArrayNotHasKey( 'seller', $default['offers'] );
    }


    public function testPropertyCountUsesSingularForm(): void
    {
        $action = new LengthAwarePaginator(
            [$this->item( 'Only property' )],
            1,
            10,
            1,
            ['path' => '/properties']
        );
        $html = view( 'estate::properties', [
            'action' => (object) [
                'items' => $action,
                'filters' => (object) [
                    'sort' => (string) request('sort', '-created_at'),
                    'type' => strtolower(trim((string) request('type', ''))),
                    'offer' => strtolower(trim((string) request('offer', ''))),
                    'status' => strtolower(trim((string) request('status', ''))),
                    'city' => trim((string) request('city', '')),
                    'available_by' => trim((string) request('available_by', '')),
                    'rooms_min' => request()->filled('rooms_min') ? (int) request('rooms_min') : null,
                ],
                'options' => (object) [
                    'property_types' => [['value' => 'apartment', 'label' => 'Apartment']],
                    'offer_types' => [
                        ['value' => 'sale', 'label' => 'Sale'],
                        ['value' => 'rent', 'label' => 'Rent'],
                    ],
                    'statuses' => [['value' => 'available', 'label' => 'Available']],
                ],
            ],
            'data' => (object) [
                'id' => 'properties',
                'layout' => 'cards',
                'order' => '-created_at',
                'title' => 'Properties',
                'parent-page' => (object) ['value' => null],
            ],
            'page' => $this->page( 'Properties', 'properties' ),
        ] )->render();

        $this->assertStringContainsString( 'Showing 1 to 1 of 1 property', $html );
        $this->assertStringNotContainsString( 'Showing 1 to 1 of 1 properties', $html );
    }


    public function testPropertyJsonIsValidAndOmitsEmptyAddress(): void
    {
        $html = $this->renderProperty( [
            'reference' => 'EST-123',
            'property_type' => 'house',
        ] );
        $schema = $this->json( $html, 'RealEstateListing' );

        $this->assertSame( 'RealEstateListing', $schema['@type'] );
        $this->assertSame( 'EST-123', $schema['identifier'] );
        $this->assertSame( 'House', $schema['category'] );
        $this->assertArrayNotHasKey( 'address', $schema );
        $this->assertStringContainsString( '<time datetime="2026-01-02">2 January 2026</time>', $html );
        $this->assertStringContainsString( 'Source page: ' . cmsroute( $this->page() ), $html );
    }


    public function testSaleAndRentUseDifferentOfferMetadata(): void
    {
        $saleHtml = $this->renderProperty( ['price_period' => 'month'] );
        $sale = $this->json( $saleHtml, 'RealEstateListing' );
        $rent = $this->json( $this->renderProperty( [
            'offer_type' => 'rent',
            'price_period' => 'month',
        ] ), 'RealEstateListing' );

        $this->assertSame( 'http://purl.org/goodrelations/v1#Sell', $sale['offers']['businessFunction'] );
        $this->assertArrayNotHasKey( 'priceSpecification', $sale['offers'] );
        $this->assertStringNotContainsString( 'per month', $saleHtml );
        $this->assertSame( 'http://purl.org/goodrelations/v1#LeaseOut', $rent['offers']['businessFunction'] );
        $this->assertSame( 'month', $rent['offers']['priceSpecification']['unitText'] );
    }


    public function testStatusesUseMatchingAvailability(): void
    {
        $available = $this->json( $this->renderProperty(), 'RealEstateListing' );
        $underOffer = $this->json( $this->renderProperty( ['status' => 'under_offer'] ), 'RealEstateListing' );
        $sold = $this->json( $this->renderProperty( ['status' => 'sold'] ), 'RealEstateListing' );

        $this->assertSame( 'https://schema.org/InStock', $available['availability'] );
        $this->assertSame( 'https://schema.org/LimitedAvailability', $underOffer['availability'] );
        $this->assertSame( 'https://schema.org/OutOfStock', $sold['availability'] );
        $this->assertSame( $underOffer['availability'], $underOffer['offers']['availability'] );
    }


    protected function getPackageProviders( $app )
    {
        return array_merge( parent::getPackageProviders( $app ), [
            'Aimeos\Cms\EstateServiceProvider',
        ] );
    }


    protected function item( string $title, array $data = [] ): object
    {
        return (object) [
            'title' => $title,
            'path' => str( $title )->slug()->toString(),
            'content' => [$this->property( $data )],
            'files' => collect(),
            'created_at' => CarbonImmutable::parse( '2026-01-01 12:00:00' ),
        ];
    }


    protected function json( string $html, string $type ): array
    {
        preg_match_all( '#<script type="application/ld\\+json">(.*?)</script>#s', $html, $matches );

        return collect( $matches[1] ?? [] )
            ->map( fn( $json ) => json_decode( trim( $json ), true, flags: JSON_THROW_ON_ERROR ) )
            ->first( fn( $schema ) => ( $schema['@type'] ?? null ) === $type ) ?? [];
    }


    protected function page( string $title = 'Test property', string $path = 'test-property' ): Page
    {
        return ( new Page() )->forceFill( [
            'id' => '11111111-1111-4111-8111-111111111111',
            'lang' => app()->getLocale(),
            'name' => $title,
            'title' => $title,
            'path' => $path,
            'theme' => 'estate',
            'created_at' => CarbonImmutable::parse( '2026-01-01 12:00:00' ),
            'updated_at' => CarbonImmutable::parse( '2026-01-02 12:00:00' ),
        ] );
    }


    protected function property( array $data = [] ): object
    {
        return (object) array_replace( [
            'type' => 'property',
            'text' => 'Property description',
            'status' => 'available',
            'reference' => null,
            'property_type' => 'apartment',
            'offer_type' => 'sale',
            'price' => 1250000,
            'currency' => 'EUR',
            'price_period' => null,
            'available_from' => null,
            'address' => null,
            'area' => 125,
            'area_unit' => 'm²',
            'district' => null,
            'city' => null,
            'country' => null,
            'zip_code' => null,
            'rooms' => 4,
            'bedrooms' => 2,
            'bathrooms' => 1,
            'living_area' => null,
            'plot_area' => null,
            'year_built' => null,
            'values' => [],
            'features' => null,
            'documents' => [],
            'files' => [],
            'file' => null,
        ], $data );
    }


    protected function renderProperty( array $data = [], ?Collection $files = null ): string
    {
        return view( 'estate::property', [
            'data' => $this->property( $data ),
            'files' => $files ?? collect(),
            'page' => $this->page(),
        ] )->render();
    }
}
