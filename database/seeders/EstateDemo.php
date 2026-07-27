<?php

/**
 * @license MIT, https://opensource.org/license/mit
 */

namespace Database\Seeders;

use Aimeos\Cms\Models\Element;
use Aimeos\Cms\Models\File;
use Aimeos\Cms\Models\Page;
use Aimeos\Cms\Utils;
use Aimeos\Cms\Validation;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


/**
 * Estate theme demo inspired by premium international real-estate portals.
 */
class EstateDemo extends AbstractDemo
{
    /** @var array<string, string> Meta descriptions keyed by page path */
    private const DESCRIPTIONS = [
        'properties' => 'Current property portfolio with curated residential and commercial listings, ready to compare and inspect.',
        'properties/commercial' => 'Commercial property opportunities selected for access, visibility, adaptable use, and long-term value.',
        'properties/rental' => 'Flexible rental properties with transparent terms, space details, and availability.',
        'properties/residential' => 'Residential properties selected for location quality, architecture, and long-term value.',
        'news' => 'Weekly market updates and property strategy notes for clients evaluating high-end transactions.',
        'properties/urban-penthouse-berlin' => 'Luxury penthouse with rooftop access and premium interior context in central Berlin.',
        'properties/riverfront-office-suite' => 'Converted office floorplan for mixed retail and hospitality-adjacent use.',
        'properties/harbor-retail-loft' => 'Harbor-side commercial property with flexible frontage and modern infrastructure.',
        'news/why-portfolio-diversification-matters' => 'How location, tenant mix, and lease duration affect long-term property performance.',
        'news/renovation-as-a-leverage-point' => 'Practical ways to preserve value through targeted upgrades and tenant-ready delivery.',
    ];

    /**
     * @var array<string, array{0: string, 1: string, 2: string}>
     */
    private const PHOTOS = [
        'hero' => ['photo-1568605114967-8130f3a36994', 'Modern city residence facade', 'Large estate facade with controlled exterior lighting and clean geometry'],
        'buy' => ['photo-1512918728675-ed5a9ecdebfd', 'Apartment lounge', 'Neutral-toned apartment interior for premium buy marketing'],
        'commercial' => ['photo-1486406146926-c627a92ad1ab', 'Commercial meeting table', 'Professional interior for office and commercial listings'],
        'lounge' => ['photo-1600566753376-1f2f8b3e3d2f', 'Private lounge', 'Soft-toned lounge with curated finishes and natural textures'],
        'city' => ['photo-1545324418-cc1a3f0c7f67', 'City skyline', 'Metropolitan skyline used for regional marketing context'],
        'news' => ['photo-1460317442991-0ec209397118', 'Market brief', 'Property market report pages and notes on tablet'],
        'renovation' => ['photo-1522708323590-d24dbb6b0267', 'Building renovation', 'Scaffold and finished interior transition materials'],
    ];

    private string $element;
    private string $logoFile;


    protected function addProperties( Page $home ) : static
    {
        $propertiesId = Utils::uid();
        $properties = $this->page( [
            'id' => $propertiesId,
            'lang' => 'en',
            'name' => 'Properties',
            'title' => 'Property Portfolio',
            'path' => 'properties',
            'type' => 'page',
            'tag' => 'property',
            'status' => 1,
        ], [
            ['id' => Utils::uid(), 'type' => 'hero', 'group' => 'main', 'data' => [
                'title' => 'Available properties',
                'subtitle' => 'Estate property portfolio',
                'text' => 'Browse premium residential and commercial listings with practical details: district, area, asking conditions, and availability.',
                'url' => '/#home-contact',
                'button' => 'Contact advisor',
                'files' => [['id' => $this->img( 'commercial' ), 'type' => 'file']],
            ]],
            ['id' => Utils::uid(), 'type' => 'estate::properties', 'group' => 'main', 'data' => [
                'title' => 'Current properties',
                'layout' => 'list',
                'limit' => 3,
                'order' => '_lft',
                'parent-page' => ['value' => $propertiesId, 'label' => 'Properties'],
            ]],
        ], $home );

        $residentialId = Utils::uid();
        $residential = $this->page( [
            'id' => $residentialId,
            'lang' => 'en',
            'name' => 'Residential',
            'title' => 'Residential Properties',
            'path' => 'properties/residential',
            'type' => 'page',
            'tag' => 'property-category',
            'status' => 1,
        ], [
            ['id' => Utils::uid(), 'type' => 'text', 'group' => 'main', 'data' => [
                'text' => '## Residential properties\n\nHomes and apartments selected for location quality, architecture, and long-term residential value.',
            ]],
            ['id' => Utils::uid(), 'type' => 'estate::properties', 'group' => 'main', 'data' => [
                'title' => 'Residential portfolio',
                'layout' => 'cards',
                'filters' => false,
                'limit' => 6,
                'order' => '_lft',
                'parent-page' => ['value' => $residentialId, 'label' => 'Residential'],
            ]],
        ], $properties );

        $rentalId = Utils::uid();
        $rental = $this->page( [
            'id' => $rentalId,
            'lang' => 'en',
            'name' => 'Rental',
            'title' => 'Rental Properties',
            'path' => 'properties/rental',
            'type' => 'page',
            'tag' => 'property-category',
            'status' => 1,
        ], [
            ['id' => Utils::uid(), 'type' => 'text', 'group' => 'main', 'data' => [
                'text' => '## Rental properties\n\nFlexible rental opportunities with transparent periods, space details, and availability.',
            ]],
            ['id' => Utils::uid(), 'type' => 'estate::properties', 'group' => 'main', 'data' => [
                'title' => 'Rental portfolio',
                'layout' => 'cards',
                'filters' => false,
                'limit' => 6,
                'order' => '_lft',
                'parent-page' => ['value' => $rentalId, 'label' => 'Rental'],
            ]],
        ], $properties );

        $commercialId = Utils::uid();
        $commercial = $this->page( [
            'id' => $commercialId,
            'lang' => 'en',
            'name' => 'Commercial',
            'title' => 'Commercial Properties',
            'path' => 'properties/commercial',
            'type' => 'page',
            'tag' => 'property-category',
            'status' => 1,
        ], [
            ['id' => Utils::uid(), 'type' => 'text', 'group' => 'main', 'data' => [
                'text' => '## Commercial properties\n\nOffice and retail opportunities selected for access, visibility, and adaptable use.',
            ]],
            ['id' => Utils::uid(), 'type' => 'estate::properties', 'group' => 'main', 'data' => [
                'title' => 'Commercial portfolio',
                'layout' => 'cards',
                'filters' => false,
                'limit' => 6,
                'order' => '_lft',
                'parent-page' => ['value' => $commercialId, 'label' => 'Commercial'],
            ]],
        ], $properties );

        $this->page( [
            'lang' => 'en',
            'name' => 'Urban Penthouse Berlin',
            'title' => 'Urban Penthouse, Berlin',
            'path' => 'properties/urban-penthouse-berlin',
            'tag' => 'property',
            'type' => 'property',
            'status' => 1,
        ], [
            $this->property(
                'A bright penthouse with skyline orientation, private terrace, and building security in one of Berlin’s most active districts.',
                [
                    $this->img( 'hero' ),
                    $this->img( 'lounge' ),
                    $this->img( 'renovation' ),
                ],
                'sale',
                'available',
                3250000,
                'Berlin Mitte',
                310,
                4,
                3,
                [
                    'reference' => 'EST-UB-001',
                    'property_type' => 'penthouse',
                    'address' => 'Kopenhagener Straße 112',
                    'city' => 'Berlin',
                    'country' => 'Germany',
                    'zip_code' => '10437',
                    'available_from' => '2026-09-01',
                    'living_area' => 245,
                    'plot_area' => null,
                    'rooms' => 10,
                    'year_built' => 2015,
                    'values' => [
                        ['Floor', '7'],
                        ['Parking', '2 parking spaces'],
                        ['Energy class', 'A'],
                        ['Condition', 'Turnkey'],
                        ['Heating', 'District heating with cooling'],
                        ['Terrace', 'Private roof terrace'],
                        ['Lift', 'Direct penthouse access'],
                    ],
                    'features' => "## Property features\n- 3 bedrooms / 3 bathrooms\n- Private terrace and panoramic views\n- Smart home controls\n- Private parking garage",
                ],
            ),
        ], $residential );

        $this->page( [
            'lang' => 'en',
            'name' => 'Riverfront Office Suite',
            'title' => 'Riverfront Office Suite',
            'path' => 'properties/riverfront-office-suite',
            'tag' => 'property',
            'type' => 'property',
            'status' => 1,
        ], [
            $this->property(
                'A flexible office suite with polished circulation, high-speed infrastructure, and direct access to mixed-use retail frontage.',
                [
                    $this->img( 'news' ),
                    $this->img( 'commercial' ),
                    $this->img( 'city' ),
                ],
                'rent',
                'available',
                62000,
                'Hamburg Waterfront',
                1240,
                0,
                2,
                [
                    'reference' => 'EST-OF-044',
                    'property_type' => 'office',
                    'address' => 'Hafencity Promenade 24',
                    'city' => 'Hamburg',
                    'country' => 'Germany',
                    'zip_code' => '20457',
                    'available_from' => '2026-08-15',
                    'price_period' => 'month',
                    'living_area' => 920,
                    'plot_area' => null,
                    'rooms' => 8,
                    'year_built' => 2018,
                    'values' => [
                        ['Floor', '2'],
                        ['Parking', '6 parking spaces'],
                        ['Energy class', 'B'],
                        ['Condition', 'Ready to move'],
                        ['Heating', 'Gas hybrid'],
                        ['Lease term', 'Flexible'],
                        ['Workstations', 'Up to 80'],
                    ],
                    'features' => "## Property features\n- Open floor plan with co-working layout\n- Reception and loading access\n- 24/7 elevator and security\n- Built-in audio-visual systems",
                ],
            ),
        ], $rental );

        $this->page( [
            'lang' => 'en',
            'name' => 'Harbor Retail Loft',
            'title' => 'Harbor Retail Loft',
            'path' => 'properties/harbor-retail-loft',
            'tag' => 'property',
            'type' => 'property',
            'status' => 1,
        ], [
            $this->property(
                'An adaptable waterfront retail loft with high visibility, clear tenant routes, and broad frontage for premium tenant conversions.',
                [
                    $this->img( 'city' ),
                    $this->img( 'commercial' ),
                    $this->img( 'buy' ),
                ],
                'sale',
                'under_offer',
                2150000,
                'Hamburg Hafen City',
                380,
                0,
                1,
                [
                    'reference' => 'EST-RT-117',
                    'property_type' => 'retail',
                    'address' => 'Überseehafen Straße 9',
                    'city' => 'Hamburg',
                    'country' => 'Germany',
                    'zip_code' => '20457',
                    'living_area' => 320,
                    'plot_area' => null,
                    'rooms' => 5,
                    'year_built' => 2005,
                    'values' => [
                        ['Floor', 'Ground + 1'],
                        ['Parking', '1 valet bay'],
                        ['Energy class', 'B+'],
                        ['Condition', 'As-is with fit-out scope'],
                        ['Heating', 'District heating'],
                        ['Frontage', 'Waterfront'],
                        ['Access', 'Street and rear'],
                    ],
                    'features' => "## Property features\n- Street frontage and high visibility\n- Flexible tenant circulation\n- Storage annex and rear access\n- Elevator connection to mezzanine",
                ],
            ),
        ], $commercial );

        return $this;
    }


    protected function addNews( Page $home, string $newsId ) : static
    {
        $news = $this->page( [
            'id' => $newsId,
            'lang' => 'en',
            'name' => 'News',
            'title' => 'Estate News',
            'path' => 'news',
            'tag' => 'blog',
            'type' => 'blog',
            'status' => 1,
        ], [
            ['id' => Utils::uid(), 'type' => 'hero', 'group' => 'main', 'data' => [
                'title' => 'Market notes and strategy ideas',
                'subtitle' => 'Estate news desk',
                'text' => 'Short reads for buyers, renters, landlords, and owners balancing quality and timeline pressure.',
                'files' => [['id' => $this->img( 'news' ), 'type' => 'file']],
            ]],
            ['id' => Utils::uid(), 'type' => 'blog', 'group' => 'main', 'data' => [
                'title' => 'Latest updates',
                'layout' => 'default',
                'limit' => 2,
                'order' => '_lft',
                'parent-page' => ['value' => $newsId, 'label' => 'News'],
            ]],
        ], $home );

        $this->page( [
            'lang' => 'en',
            'name' => 'Why portfolio diversification matters',
            'title' => 'Why portfolio diversification matters now',
            'path' => 'news/why-portfolio-diversification-matters',
            'tag' => 'article',
            'type' => 'blog',
            'status' => 1,
        ], [
            $this->article(
                'Why portfolio diversification matters now',
                'A concentrated position can perform well when cycles align, but long-horizon owners usually protect downside with location and tenant balance. That remains true for both residential and commercial portfolios.',
                $this->img( 'commercial' )
            ),
            ['id' => Utils::uid(), 'type' => 'image-text', 'group' => 'main', 'data' => [
                'file' => ['id' => $this->img( 'city' ), 'type' => 'file'],
                'position' => 'end',
                'ratio' => '1-2',
                'text' => '## Practical signal\n\nBefore expanding allocation, compare rental carry, demand resilience, and tenant retention by zip code rather than headline yield alone.',
            ]],
        ], $news );

        $this->page( [
            'lang' => 'en',
            'name' => 'Renovation as a leverage point',
            'title' => 'Renovation as a leverage point',
            'path' => 'news/renovation-as-a-leverage-point',
            'tag' => 'article',
            'type' => 'blog',
            'status' => 1,
        ], [
            $this->article(
                'Renovation as a leverage point',
                'Small, high-signal upgrades often beat full rebuilds when done after lease-cycle reviews. Targeted updates improve conversion speed and reduce pricing friction.',
                $this->img( 'renovation' )
            ),
            ['id' => Utils::uid(), 'type' => 'text', 'group' => 'main', 'data' => [
                'text' => 'Most owners see value when renovations are scoped to market-visible outcomes: access to daylight, quiet systems, storage, and transition readiness.',
            ]],
        ], $news );

        return $this;
    }


    protected function property( string $text, array $fileIds, string $offerType, string $status, int|float $price, string $district, int|float $area, int $bedrooms, int $bathrooms, array $extra = [] ) : array
    {
        return ['id' => Utils::uid(), 'type' => 'estate::property', 'group' => 'main', 'files' => $fileIds, 'data' => [
            'text' => $text,
            'files' => array_map( fn( $id ) => ['id' => $id, 'type' => 'file'], $fileIds ),
            'offer_type' => $offerType,
            'status' => $status,
            'price' => $price,
            'currency' => $extra['currency'] ?? 'EUR',
            'price_period' => $extra['price_period'] ?? null,
            'district' => $district,
            'area' => $area,
            'area_unit' => $extra['area_unit'] ?? 'm²',
            'bedrooms' => $bedrooms,
            'bathrooms' => $bathrooms,
            'reference' => $extra['reference'] ?? null,
            'property_type' => $extra['property_type'] ?? null,
            'address' => $extra['address'] ?? null,
            'city' => $extra['city'] ?? null,
            'country' => $extra['country'] ?? null,
            'zip_code' => $extra['zip_code'] ?? null,
            'available_from' => $extra['available_from'] ?? null,
            'living_area' => $extra['living_area'] ?? null,
            'plot_area' => $extra['plot_area'] ?? null,
            'rooms' => $extra['rooms'] ?? null,
            'year_built' => $extra['year_built'] ?? null,
            'values' => $extra['values'] ?? [],
            'features' => $extra['features'] ?? null,
        ]];
    }


    protected function article( string $title, string $text, string $fileId ) : array
    {
        return ['id' => Utils::uid(), 'type' => 'article', 'group' => 'main', 'files' => [$fileId], 'data' => [
            'title' => $title,
            'file' => ['id' => $fileId, 'type' => 'file'],
            'text' => $text,
        ]];
    }


    protected function element() : string
    {
        if( !isset( $this->element ) )
        {
            $cards = [
                ['title' => 'Explore', 'text' => '- [Home](/)\n- [Properties](/properties)\n- [News](/news)'],
                ['title' => 'Properties', 'text' => '- [Residential](/properties/residential)\n- [Rental](/properties/rental)\n- [Commercial](/properties/commercial)'],
                ['title' => 'Contact', 'text' => '- [hello@estate.example](mailto:hello@estate.example)\n- [Request a consultation](/#home-contact)'],
            ];

            $element = Element::forceCreate( [
                'lang' => 'en',
                'type' => 'cards',
                'name' => 'Estate footer',
                'data' => ['type' => 'cards', 'data' => ['cards' => $cards]],
                'editor' => 'demo',
            ] );

            $version = $element->versions()->forceCreate( [
                'lang' => 'en',
                'data' => [
                    'lang' => 'en',
                    'type' => 'cards',
                    'name' => 'Estate footer',
                    'data' => ['cards' => $cards],
                ],
                'editor' => 'demo',
            ] );

            $element->forceFill( ['latest_id' => $version->id] )->saveQuietly();
            $element->publish( $version );
            $this->element = (string) $element->refresh()->id;
        }

        return $this->element;
    }


    protected function file() : string
    {
        return $this->img( 'hero' );
    }


    protected function home( string $newsId ) : Page
    {
        $elementId = $this->element();
        $fileId = $this->file();
        $logoId = $this->logoFile();

        $config = [
            'logo' => [
                'type' => 'logo',
                'files' => [$logoId],
                'data' => ['file' => ['id' => $logoId, 'type' => 'file']],
            ],
            'logo-alternative' => [
                'type' => 'logo-alternative',
                'files' => [$logoId],
                'data' => ['file' => ['id' => $logoId, 'type' => 'file']],
            ],
        ];

        $content = [
            ['id' => Utils::uid(), 'type' => 'hero', 'group' => 'main', 'data' => [
                'title' => 'Find the property behind your next move',
                'subtitle' => 'Estate',
                'text' => 'Compare residential, rental, and commercial properties with clear pricing, availability, location details, and practical market context.',
                'url' => '/properties',
                'button' => 'Browse properties',
                'url-alternative' => '/news',
                'button-alternative' => 'Read property news',
                'files' => [['id' => $fileId, 'type' => 'file']],
            ]],
            ['id' => Utils::uid(), 'type' => 'cards', 'group' => 'main', 'data' => [
                'title' => 'Explore properties',
                'cards' => [
                    ['title' => 'Residential', 'text' => "Homes and apartments selected for location quality and long-term value.\n\n[View residential properties](/properties/residential)", 'file' => ['id' => $this->img( 'buy' ), 'type' => 'file']],
                    ['title' => 'Rental', 'text' => "Flexible rentals with transparent periods, space details, and availability.\n\n[View rental properties](/properties/rental)", 'file' => ['id' => $this->img( 'lounge' ), 'type' => 'file']],
                    ['title' => 'Commercial', 'text' => "Office and retail opportunities selected for access, visibility, and adaptable use.\n\n[View commercial properties](/properties/commercial)", 'file' => ['id' => $this->img( 'commercial' ), 'type' => 'file']],
                ],
            ]],
            ['id' => Utils::uid(), 'type' => 'image-text', 'group' => 'main', 'data' => [
                'file' => ['id' => $this->img( 'lounge' ), 'type' => 'file'],
                'position' => 'start',
                'ratio' => '1-2',
                'text' => "## Location-aware recommendations\n\nEstate Group uses region teams to surface what matters: floor quality, tenant profile, lease structure, and practical ownership context. You can compare options quickly without losing the nuance of each district.",
            ]],
            ['id' => Utils::uid(), 'type' => 'blog', 'group' => 'main', 'data' => [
                'title' => 'Latest property insights',
                'layout' => 'cards',
                'limit' => 2,
                'order' => '_lft',
                'parent-page' => ['value' => $newsId, 'label' => 'News'],
            ]],
            ['id' => Utils::uid(), 'type' => 'questions', 'group' => 'main', 'data' => [
                'title' => 'How can we help',
                'items' => [
                    ['title' => 'I am evaluating a purchase', 'text' => 'Tell us your criteria and timeframe. We shortlist the strongest opportunities and include comparable transaction context.'],
                    ['title' => 'I want to relocate and rent', 'text' => 'Share your duration and target districts. We can prepare a short list of serious options and review slots.'],
                    ['title' => 'I have a property to sell', 'text' => 'We review documentation, pricing, and positioning before a targeted launch and private campaign plan.'],
                ],
            ]],
            ['id' => 'home-contact', 'type' => 'contact', 'group' => 'main', 'data' => [
                'title' => 'Request a private consultation',
            ]],
            ['id' => Utils::uid(), 'type' => 'reference', 'refid' => $elementId, 'group' => 'footer'],
        ];

        $meta = [
            'meta-tags' => Validation::entry( 'meta-tags', [
                'description' => 'Estate Group presents residential, rental, and commercial properties with clear details and current market news.',
                'keywords' => 'estate, properties, property news, rentals, commercial real estate, premium residences',
            ], 'meta' ),
            'social-media' => Validation::entry( 'social-media', [
                'title' => 'Estate | Premium Real Estate Portal',
                'description' => 'Residential, rental, and commercial properties with practical details and current market news.',
                'file' => ['id' => $fileId, 'type' => 'file'],
            ], 'meta' ),
        ];

        $page = Page::forceCreate( [
            'lang' => 'en',
            'name' => 'Home',
            'title' => 'Estate | Properties and Real Estate News',
            'path' => '',
            'tag' => 'root',
            'theme' => $this->theme,
            'status' => 1,
            'cache' => 5,
            'editor' => 'demo',
            'config' => $config,
            'meta' => $meta,
            'content' => $content,
        ] );

        $version = $page->versions()->forceCreate( [
            'lang' => 'en',
            'data' => [
                'name' => 'Home',
                'title' => 'Estate | Properties and Real Estate News',
                'path' => '',
                'tag' => 'root',
                'domain' => '',
                'theme' => $this->theme,
                'status' => 1,
                'cache' => 5,
            ],
            'aux' => ['config' => $config, 'meta' => $meta, 'content' => $content],
            'published' => true,
            'editor' => 'demo',
        ] );

        $version->files()->attach( array_unique( array_merge( [$fileId], $this->ids( $config ), $this->ids( $content ), $this->ids( $meta ) ) ) );
        $version->elements()->attach( $elementId );
        $page->forceFill( ['latest_id' => $version->id] )->saveQuietly();
        $page->publish( $version );

        return $page;
    }


    protected function ids( mixed $value ) : array
    {
        $ids = [];

        if( is_array( $value ) )
        {
            if( ( $value['type'] ?? null ) === 'file' && is_string( $value['id'] ?? null )
                && !isset( $value['data'] ) && !isset( $value['group'] )
            ) {
                $ids[] = $value['id'];
            }

            foreach( $value as $item ) {
                $ids = array_merge( $ids, $this->ids( $item ) );
            }
        }

        return $ids;
    }


    protected function img( string $key ) : string
    {
        [$photo, $name, $desc] = self::PHOTOS[$key];
        return $this->image( $photo, $name, $desc );
    }


    protected function logoFile() : string
    {
        if( !isset( $this->logoFile ) )
        {
            $svg = <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 420 84" role="img" aria-labelledby="title desc">
  <title id="title">Estate logo</title>
  <desc id="desc">Estate wordmark with a geometric building icon</desc>
  <g fill="none" fill-rule="evenodd">
    <rect x="12" y="31" width="26" height="43" rx="2" fill="#102A43" />
    <rect x="42" y="22" width="26" height="52" rx="2" fill="#102A43" />
    <rect x="72" y="15" width="26" height="59" rx="2" fill="#102A43" />
    <rect x="102" y="27" width="26" height="47" rx="2" fill="#102A43" />
    <rect x="132" y="33" width="26" height="41" rx="2" fill="#0F766E" />
    <rect x="168" y="18" width="3" height="56" fill="#102A43"/>
    <rect x="176" y="18" width="3" height="56" fill="#102A43"/>
    <path d="M10 75h210" stroke="#102A43" stroke-width="2"/>
    <text x="210" y="61" fill="#102A43" font-family="Avenir Next, Avenir, Segoe UI, sans-serif" font-size="38" font-weight="700" letter-spacing="2.3">ESTATE</text>
    <text x="214" y="78" fill="#0F766E" font-family="Avenir Next, Avenir, Segoe UI, sans-serif" font-size="10" letter-spacing="7">PROPERTY PORTAL</text>
  </g>
</svg>
SVG;

            $disk = Storage::disk( config( 'cms.disk', 'public' ) );
            $path = rtrim( 'cms/' . $this->tenant, '/' ) . '/estate-logo.svg';

            if( !$disk->put( $path, $svg ) ) {
                throw new \Aimeos\Cms\Exception( sprintf( 'Unable to store logo "%s"', $path ) );
            }

            $data = [
                'mime' => 'image/svg+xml',
                'lang' => 'en',
                'name' => 'Estate logo',
                'path' => $path,
                'previews' => ['500' => $path],
                'description' => ['en' => 'Estate portfolio logo'],
            ];

            $file = File::forceCreate( $data + ['editor' => 'demo'] );
            $version = $file->versions()->forceCreate( [
                'lang' => 'en',
                'data' => $data,
                'published' => true,
                'editor' => 'demo',
            ] );

            $file->forceFill( ['latest_id' => $version->id] )->saveQuietly();
            $file->publish( $version );
            $this->logoFile = (string) $file->refresh()->id;
        }

        return $this->logoFile;
    }


    protected function page( array $data, array $content, Page $parent, array $fileIds = [], array $meta = [] ) : Page
    {
        $elementId = $this->element();
        $fileId = $this->file();
        $description = self::DESCRIPTIONS[$data['path'] ?? ''] ?? $data['title'] ?? '';

        $meta = $data['meta'] ?? $meta ?: [
            'meta-tags' => Validation::entry( 'meta-tags', [
                'description' => $description,
                'keywords' => 'Estate Group, real estate, buy, rent, sell, commercial',
            ], 'meta' ),
            'social-media' => Validation::entry( 'social-media', [
                'title' => $data['title'] ?? '',
                'description' => $description,
                'file' => ['id' => $fileId, 'type' => 'file'],
            ], 'meta' ),
        ];

        $content[] = ['id' => Utils::uid(), 'type' => 'reference', 'refid' => $elementId, 'group' => 'footer'];

        $page = Page::forceCreate( $data + [
            'theme' => $this->theme,
            'editor' => 'demo',
            'meta' => $meta,
            'content' => $content,
        ] );
        $page->appendToNode( $parent )->save();

        $version = $page->versions()->forceCreate( [
            'lang' => $data['lang'] ?? 'en',
            'data' => array_diff_key( $data, ['content' => 1, 'meta' => 1, 'id' => 1] ) + [
                'domain' => '',
                'theme' => $this->theme,
            ],
            'aux' => ['meta' => $meta, 'content' => $content],
            'published' => true,
            'editor' => 'demo',
        ] );

        $version->elements()->attach( $elementId );
        $version->files()->attach( array_unique( array_merge( [$fileId], $fileIds, $this->ids( $content ), $this->ids( $meta ) ) ) );

        $page->forceFill( ['latest_id' => $version->id] )->saveQuietly();
        $page->publish( $version );

        return $page;
    }


    protected function pages() : void
    {
        $newsId = (string) Str::uuid7();
        $home = $this->home( $newsId );

        $this->addProperties( $home )
            ->addNews( $home, $newsId );
    }
}
