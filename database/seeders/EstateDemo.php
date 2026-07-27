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
        'buy' => 'Curated residential and mixed-use buy opportunities with premium curation, verified seller guidance, and transparent key facts.',
        'rent' => 'Short- and long-term rentals across central city districts, featuring lifestyle and investment-oriented options.',
        'sell' => 'Professional selling support from valuation and positioning to media production and private client review.',
        'commercial' => 'Prime commercial spaces for hospitality, retail, logistics, and mixed-use expansion.',
        'properties' => 'Current property portfolio with curated residential and commercial listings, ready to compare and inspect.',
        'locations' => 'Regional hubs and market context for Berlin, Munich, Hamburg, and international destination offices.',
        'locations/berlin' => 'Berlin market activity, district highlights, and properties in transition corridors.',
        'locations/munich' => 'Munich residential demand, transit-facing districts, and long-term growth zones.',
        'locations/hamburg' => 'Hamburg lake and port adjacency listings with practical commuting and commercial context.',
        'about' => 'Company profile, service model, and advisor roles across global property teams.',
        'news' => 'Weekly market updates and property strategy notes for clients evaluating high-end transactions.',
        'properties/urban-penthouse-berlin' => 'Luxury penthouse with rooftop access and premium interior context in central Berlin.',
        'properties/riverfront-office-suite' => 'Converted office floorplan for mixed retail and hospitality-adjacent use.',
        'properties/harbor-retail-loft' => 'Harbor-side commercial property with flexible frontage and modern infrastructure.',
        'news/why-portfolio-diversification-matters' => 'How location, tenant mix, and lease duration affect long-term property performance.',
        'news/renovation-as-a-leverage-point' => 'Practical ways to preserve value through targeted upgrades and tenant-ready delivery.',
        'contact' => 'Dedicated sales desk, valuation support, viewing requests, and private advisory contact.',
    ];

    /**
     * @var array<string, array{0: string, 1: string, 2: string}>
     */
    private const PHOTOS = [
        'hero' => ['photo-1568605114967-8130f3a36994', 'Modern city residence facade', 'Large estate facade with controlled exterior lighting and clean geometry'],
        'buy' => ['photo-1512918728675-ed5a9ecdebfd', 'Apartment lounge', 'Neutral-toned apartment interior for premium buy marketing'],
        'rent' => ['photo-1600607687644-8c6e0e8f1d7b', 'Residential building', 'Contemporary residential building exterior at dusk'],
        'sell' => ['photo-1600566753086-00f18fb6b3ea', 'Premium bathroom', 'Well-lit interior prepared for property photography'],
        'commercial' => ['photo-1486406146926-c627a92ad1ab', 'Commercial meeting table', 'Professional interior for office and commercial listings'],
        'lounge' => ['photo-1600566753376-1f2f8b3e3d2f', 'Private lounge', 'Soft-toned lounge with curated finishes and natural textures'],
        'city' => ['photo-1545324418-cc1a3f0c7f67', 'City skyline', 'Metropolitan skyline used for regional marketing context'],
        'berlin' => ['photo-1494526585095-c41746248156', 'Berlin streetscape', 'Berlin streetscape with classic and modern layers'],
        'munich' => ['photo-1513694203232-719a280e022f', 'Munich avenue', 'Munich avenue with boutique building frontage'],
        'hamburg' => ['photo-1467269204594-9661b134dd2b', 'Harbor district', 'Harbor district architecture with soft morning light'],
        'agent' => ['photo-1487958449943-2429e8be8625', 'Advisor desk', 'Advisor desk with structured client portfolio review'],
        'news' => ['photo-1460317442991-0ec209397118', 'Market brief', 'Property market report pages and notes on tablet'],
        'renovation' => ['photo-1522708323590-d24dbb6b0267', 'Building renovation', 'Scaffold and finished interior transition materials'],
    ];

    private string $element;
    private string $logoFile;


    protected function addAbout( Page $home ) : static
    {
        $this->page( [
            'lang' => 'en',
            'name' => 'About',
            'title' => 'About Estate Group',
            'path' => 'about',
            'type' => 'docs',
            'status' => 1,
        ], [
            ['id' => Utils::uid(), 'type' => 'toc', 'group' => 'main', 'data' => ['title' => 'About this platform']],
            ['id' => Utils::uid(), 'type' => 'hero', 'group' => 'main', 'data' => [
                'title' => 'Premium real-estate marketing built for global advisors',
                'subtitle' => 'Estate Group',
                'text' => 'Estate Group combines local expertise, media-first content, and advisor-led client service for buy, rent, and commercial property journeys.',
                'files' => [['id' => $this->img( 'agent' ), 'type' => 'file']],
            ]],
            ['id' => Utils::uid(), 'type' => 'heading', 'group' => 'main', 'data' => [
                'level' => 2,
                'title' => 'How we work',
            ]],
            ['id' => Utils::uid(), 'type' => 'cards', 'group' => 'main', 'data' => [
                'title' => 'Advisory standards',
                'cards' => [
                    ['title' => 'Transparent process', 'text' => 'Every listing path starts with verified data, clear fee language, and documented timelines.'],
                    ['title' => 'International team', 'text' => 'Curated support for cross-border buyers, tenants, and owners with dedicated desk ownership.'],
                    ['title' => 'Investor focus', 'text' => 'Commercial and mixed-use properties are reviewed through occupancy, tenant profile, and growth assumptions.'],
                    ['title' => 'Editorial quality', 'text' => 'Photography, copy, and structure are tuned for serious buyers and long-form decision makers.'],
                ],
            ]],
            ['id' => Utils::uid(), 'type' => 'questions', 'group' => 'main', 'data' => [
                'title' => 'What you get',
                'items' => [
                    ['title' => 'Dedicated advisor', 'text' => 'A named advisor guides each major step from valuation to closing.'],
                    ['title' => 'Editorial packaging', 'text' => 'A consistent package of media, floor context, and translated market details.'],
                    ['title' => 'Secure communication', 'text' => 'Private notes and client-visible updates run inside your dedicated tenancy workflow.'],
                ],
            ]],
        ], $home );

        return $this;
    }


    protected function addBuy( Page $home ) : static
    {
        $this->page( [
            'lang' => 'en',
            'name' => 'Buy',
            'title' => 'Buy Residential and Investment Properties',
            'path' => 'buy',
            'type' => 'page',
            'status' => 1,
        ], [
            ['id' => Utils::uid(), 'type' => 'hero', 'group' => 'main', 'data' => [
                'title' => 'Buy with clarity',
                'subtitle' => 'Residential and investment portfolio',
                'text' => 'Explore verified properties by city, use case, building quality, and sale stage. Every listing is structured for easier comparison, faster diligence, and cleaner decisions.',
                'url' => '/locations',
                'button' => 'View locations',
                'files' => [['id' => $this->img( 'buy' ), 'type' => 'file']],
            ]],
            ['id' => Utils::uid(), 'type' => 'cards', 'group' => 'main', 'data' => [
                'title' => 'Top property families',
                'cards' => [
                    ['title' => 'City residences', 'text' => 'Urban homes in walkable districts with transit and amenity access.', 'file' => ['id' => $this->img( 'lounge' ), 'type' => 'file']],
                    ['title' => 'Waterfront and district penthouses', 'text' => 'High-end residences with panoramic views and private architecture details.', 'file' => ['id' => $this->img( 'renovation' ), 'type' => 'file']],
                    ['title' => 'Investment bundles', 'text' => 'Multi-unit holdings and mixed-use opportunities for structured portfolio entry.', 'file' => ['id' => $this->img( 'city' ), 'type' => 'file']],
                ],
            ]],
            ['id' => Utils::uid(), 'type' => 'table', 'group' => 'main', 'data' => [
                'title' => 'Typical evaluation checklist',
                'header' => 'row',
                'table' => [
                    ['Criterion', 'What to review'],
                    ['Lease term impact', 'Current occupancy, exit paths, and contract transfer assumptions'],
                    ['Condition review', 'Energy, structural disclosures, and finishing state'],
                    ['Cost context', 'Maintenance obligations and recurring service commitments'],
                    ['Market position', 'Comparable sales, pricing range, and demand velocity'],
                ],
            ]],
        ], $home );

        return $this;
    }


    protected function addRent( Page $home ) : static
    {
        $this->page( [
            'lang' => 'en',
            'name' => 'Rent',
            'title' => 'Rent Residences and Premium Apartments',
            'path' => 'rent',
            'type' => 'page',
            'status' => 1,
        ], [
            ['id' => Utils::uid(), 'type' => 'hero', 'group' => 'main', 'data' => [
                'title' => 'Rent by lifestyle, not urgency',
                'subtitle' => 'Flexible residences',
                'text' => 'Filter rentals by contract term, amenities, view quality, and practical access to transit and schools. Short-stay and long-stay options are available with advisor support.',
                'url' => '/contact',
                'button' => 'Request a shortlist',
                'files' => [['id' => $this->img( 'rent' ), 'type' => 'file']],
            ]],
            ['id' => Utils::uid(), 'type' => 'image-text', 'group' => 'main', 'data' => [
                'file' => ['id' => $this->img( 'city' ), 'type' => 'file'],
                'position' => 'start',
                'ratio' => '1-2',
                'text' => "## What renter profiles this serves\n\nA premium rental request is usually about reliability: a clean handover timeline, clear maintenance processes, and an advisor who can help you compare the trade-offs quickly.",
            ]],
            ['id' => Utils::uid(), 'type' => 'cards', 'group' => 'main', 'data' => [
                'title' => 'Available options',
                'columns' => '3',
                'cards' => [
                    ['title' => 'Monthly stays', 'text' => 'Medium- and long-term options across major markets with service-level clarity.'],
                    ['title' => 'Executive relocation', 'text' => 'Ready-to-move units for international moves and transfer schedules.'],
                    ['title' => 'Corporate placements', 'text' => 'Dedicated support for relocation teams and business continuity planning.'],
                ],
            ]],
        ], $home );

        return $this;
    }


    protected function addSell( Page $home ) : static
    {
        $this->page( [
            'lang' => 'en',
            'name' => 'Sell',
            'title' => 'Sell With Estate Group',
            'path' => 'sell',
            'type' => 'page',
            'status' => 1,
        ], [
            ['id' => Utils::uid(), 'type' => 'hero', 'group' => 'main', 'data' => [
                'title' => 'Present every property at full potential',
                'subtitle' => 'Sell advisory',
                'text' => 'From valuation and photography guidance to international investor lead qualification, we help sellers protect value and move with confidence.',
                'url' => '/contact',
                'button' => 'Book a valuation discussion',
                'url-alternative' => '/news',
                'button-alternative' => 'Read market insights',
                'files' => [['id' => $this->img( 'sell' ), 'type' => 'file']],
            ]],
            ['id' => Utils::uid(), 'type' => 'article', 'group' => 'main', 'data' => [
                'title' => 'The sell sequence',
                'file' => ['id' => $this->img( 'agent' ), 'type' => 'file'],
                'text' => 'Pre-listing: verify documents, align pricing, agree editorial direction. Launch: activate listing route and targeted channels. Ongoing: review demand quality and adjust positioning based on evidence.',
            ]],
            ['id' => Utils::uid(), 'type' => 'questions', 'group' => 'main', 'data' => [
                'title' => 'Typical questions',
                'items' => [
                    ['title' => 'How is the market price set?', 'text' => 'By transaction comparables, location context, condition profile, and investor demand for the next 12 months.'],
                    ['title' => 'Do you offer photo support?', 'text' => 'Yes. We coordinate with media teams and deliver consistent visual guidance from the first call onward.'],
                    ['title' => 'How quickly can we start?', 'text' => 'Most assets begin advisory onboarding within five business days.'],
                ],
            ]],
        ], $home );

        return $this;
    }


    protected function addCommercial( Page $home ) : static
    {
        $this->page( [
            'lang' => 'en',
            'name' => 'Commercial',
            'title' => 'Commercial and Retail Property',
            'path' => 'commercial',
            'type' => 'page',
            'status' => 1,
        ], [
            ['id' => Utils::uid(), 'type' => 'hero', 'group' => 'main', 'data' => [
                'title' => 'Commercial opportunities with strategic context',
                'subtitle' => 'Commercial estates',
                'text' => 'Office, retail, hospitality, and mixed-use assets supported with tenant-fit analysis, lease modelling, and route-to-close planning.',
                'files' => [['id' => $this->img( 'commercial' ), 'type' => 'file']],
            ]],
            ['id' => Utils::uid(), 'type' => 'cards', 'group' => 'main', 'data' => [
                'title' => 'Commercial focus',
                'cards' => [
                    ['title' => 'Office', 'text' => 'Flagship and flexible office spaces with location-specific demand snapshots.'],
                    ['title' => 'Retail', 'text' => 'Street and destination retail with visitor profile and lease tenor insights.'],
                    ['title' => 'Hospitality', 'text' => 'Special-use assets with long-cycle value drivers and operational handover frameworks.'],
                ],
            ]],
            ['id' => Utils::uid(), 'type' => 'table', 'group' => 'main', 'data' => [
                'title' => 'Adviser handoff checklist',
                'header' => 'row',
                'table' => [
                    ['Stage', 'Output'],
                    ['Lead qualification', 'Demand profile and target tenant model'],
                    ['Contracting', 'Lease draft assumptions and occupancy risk'],
                    ['Transaction planning', 'Value milestones, milestone owners, and close timeline'],
                ],
            ]],
        ], $home );

        return $this;
    }


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
                'url' => '/contact',
                'button' => 'Contact advisor',
                'files' => [['id' => $this->img( 'commercial' ), 'type' => 'file']],
            ]],
            ['id' => Utils::uid(), 'type' => 'properties', 'group' => 'main', 'data' => [
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
            ['id' => Utils::uid(), 'type' => 'properties', 'group' => 'main', 'data' => [
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
            ['id' => Utils::uid(), 'type' => 'properties', 'group' => 'main', 'data' => [
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
            ['id' => Utils::uid(), 'type' => 'properties', 'group' => 'main', 'data' => [
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


    protected function addContact( Page $home ) : static
    {
        $this->page( [
            'lang' => 'en',
            'name' => 'Contact',
            'title' => 'Contact Estate Group',
            'path' => 'contact',
            'type' => 'page',
            'status' => 1,
        ], [
            ['id' => Utils::uid(), 'type' => 'hero', 'group' => 'main', 'data' => [
                'title' => 'Book your next property consultation',
                'subtitle' => 'Estate Group advisory desk',
                'text' => 'Send your preferences and current timeline. Our team will schedule a review with your preferred advisor.\n\nFor complex inquiries we can also support tenant fit analysis and cross-market comparisons.',
                'files' => [['id' => $this->img( 'lounge' ), 'type' => 'file']],
            ]],
            ['id' => 'estate-contact', 'type' => 'contact', 'group' => 'main', 'data' => [
                'title' => 'Start a property consultation',
            ]],
            ['id' => Utils::uid(), 'type' => 'cards', 'group' => 'main', 'data' => [
                'title' => 'Direct contacts',
                'cards' => [
                    ['title' => 'General enquiries', 'text' => '[hello@estate.example](mailto:hello@estate.example)'],
                    ['title' => 'Investor desk', 'text' => '[investor@estate.example](mailto:investor@estate.example)'],
                    ['title' => 'Commercial services', 'text' => '[commercial@estate.example](mailto:commercial@estate.example)'],
                ],
            ]],
        ], $home );

        return $this;
    }


    protected function addLocations( Page $home ) : static
    {
        $locations = $this->page( [
            'lang' => 'en',
            'name' => 'Locations',
            'title' => 'Locations and Market Focus',
            'path' => 'locations',
            'type' => 'docs',
            'status' => 1,
        ], [
            ['id' => Utils::uid(), 'type' => 'hero', 'group' => 'main', 'data' => [
                'title' => 'Where our advisory teams are active',
                'subtitle' => 'Berlin · Munich · Hamburg',
                'text' => 'Regional pages include practical context for each market: commute, district growth, transaction rhythm, and buyer profile.',
                'files' => [['id' => $this->img( 'city' ), 'type' => 'file']],
            ]],
            ['id' => Utils::uid(), 'type' => 'cards', 'group' => 'main', 'data' => [
                'title' => 'Primary regions',
                'cards' => [
                    ['title' => 'Berlin', 'text' => 'High-turnover residential and commercial districts with continuous investor interest.', 'file' => ['id' => $this->img( 'berlin' ), 'type' => 'file']],
                    ['title' => 'Munich', 'text' => 'Strong rental demand and controlled vacancy in premium residential corridors.', 'file' => ['id' => $this->img( 'munich' ), 'type' => 'file']],
                    ['title' => 'Hamburg', 'text' => 'Port-side and mixed-use opportunities with enterprise-oriented demand cycles.', 'file' => ['id' => $this->img( 'hamburg' ), 'type' => 'file']],
                ],
            ]],
        ], $home );

        $this->page( [
            'lang' => 'en',
            'name' => 'Berlin',
            'title' => 'Berlin Market',
            'path' => 'locations/berlin',
            'type' => 'docs',
            'tag' => 'city',
            'status' => 1,
        ], [
            ['id' => Utils::uid(), 'type' => 'hero', 'group' => 'main', 'data' => [
                'title' => 'Berlin',
                'subtitle' => 'Central and transitional districts',
                'text' => 'Berlin listings focus on premium blocks near transport and mixed-use neighborhoods with layered residential and office usage.',
                'files' => [['id' => $this->img( 'berlin' ), 'type' => 'file']],
            ]],
            ['id' => Utils::uid(), 'type' => 'image-text', 'group' => 'main', 'data' => [
                'file' => ['id' => $this->img( 'hero' ), 'type' => 'file'],
                'position' => 'start',
                'ratio' => '1-2',
                'text' => '## Current focus\n\nUrban edge districts with stable liquidity and consistent cross-border interest remain a priority. We publish periodic comparisons on price movement, tenant demand, and conversion timing.',
            ]],
        ], $locations );

        $this->page( [
            'lang' => 'en',
            'name' => 'Munich',
            'title' => 'Munich Market',
            'path' => 'locations/munich',
            'type' => 'docs',
            'tag' => 'city',
            'status' => 1,
        ], [
            ['id' => Utils::uid(), 'type' => 'hero', 'group' => 'main', 'data' => [
                'title' => 'Munich',
                'subtitle' => 'Transit-friendly residentials',
                'text' => 'Munich demand patterns reward strong structure: building quality, commute efficiency, and predictable occupancy assumptions.',
                'files' => [['id' => $this->img( 'munich' ), 'type' => 'file']],
            ]],
        ], $locations );

        $this->page( [
            'lang' => 'en',
            'name' => 'Hamburg',
            'title' => 'Hamburg Market',
            'path' => 'locations/hamburg',
            'type' => 'docs',
            'tag' => 'city',
            'status' => 1,
        ], [
            ['id' => Utils::uid(), 'type' => 'hero', 'group' => 'main', 'data' => [
                'title' => 'Hamburg',
                'subtitle' => 'Harbor and mixed-use momentum',
                'text' => 'Hamburg entries emphasize tenant quality, logistics adjacency, and asset lifecycle stability.',
                'files' => [['id' => $this->img( 'hamburg' ), 'type' => 'file']],
            ]],
        ], $locations );

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
        return ['id' => Utils::uid(), 'type' => 'property', 'group' => 'main', 'files' => array_map( fn( $id ) => ['id' => $id, 'type' => 'file'], $fileIds ), 'data' => [
            'text' => $text,
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
                ['title' => 'Markets', 'text' => '- [Buy](/buy)\n- [Rent](/rent)\n- [Commercial](/commercial)'],
                ['title' => 'Services', 'text' => '- [Sell](/sell)\n- [Locations](/locations)\n- [News](/news)'],
                ['title' => 'Contact', 'text' => '- [hello@estate.example](mailto:hello@estate.example)\n- [Contact our office](/contact)\n- [Investor desk](/contact)'],
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
                'title' => 'Properties built for your next move',
                'subtitle' => 'Estate',
                'text' => 'Explore buy and rent opportunities, sell with an advisory-first process, and review premium commercial assets across major locations.',
                'url' => '/buy',
                'button' => 'Explore Buy',
                'url-alternative' => '/rent',
                'button-alternative' => 'Explore Rent',
                'files' => [['id' => $fileId, 'type' => 'file']],
            ]],
            ['id' => Utils::uid(), 'type' => 'cards', 'group' => 'main', 'data' => [
                'title' => 'Explore by journey',
                'cards' => [
                    ['title' => 'Buy', 'text' => 'Residential and investment opportunities with verified media and market context.', 'file' => ['id' => $this->img( 'buy' ), 'type' => 'file']],
                    ['title' => 'Rent', 'text' => 'High-quality rentals for professionals, families, and relocation-focused moves.', 'file' => ['id' => $this->img( 'rent' ), 'type' => 'file']],
                    ['title' => 'Sell', 'text' => 'Prepare your property with advisory, pricing, and editorial presentation support.', 'file' => ['id' => $this->img( 'sell' ), 'type' => 'file']],
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
                'description' => 'Estate Group is a premium real-estate showcase with buy, rent, sell, and commercial property content.',
                'keywords' => 'estate, property, buy, rent, sell, commercial real estate, premium residences',
            ], 'meta' ),
            'social-media' => Validation::entry( 'social-media', [
                'title' => 'Estate | Premium Real Estate Portal',
                'description' => 'Property opportunities across buy, rent, and commercial categories with advisory-led support.',
                'file' => ['id' => $fileId, 'type' => 'file'],
            ], 'meta' ),
        ];

        $page = Page::forceCreate( [
            'lang' => 'en',
            'name' => 'Home',
            'title' => 'Estate | Buy, Rent, Sell, and Commercial Property',
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
                'title' => 'Estate | Buy, Rent, Sell, and Commercial Property',
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

        $this->addBuy( $home )
            ->addRent( $home )
            ->addSell( $home )
            ->addCommercial( $home )
            ->addProperties( $home )
            ->addAbout( $home )
            ->addNews( $home, $newsId )
            ->addLocations( $home )
            ->addContact( $home );
    }
}
