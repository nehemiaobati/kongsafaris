<?php

declare(strict_types=1);

namespace App\Modules\Legal\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * LegalController
 *
 * Handles display of static legal pages: Terms of Service and Privacy Policy.
 * These are public-facing informational pages with no business logic.
 *
 * @package App\Modules\Legal\Controllers
 * @author  Kong Safaris
 * @since   1.0.0
 */
class LegalController extends BaseController
{
    /**
     * Display the Terms & Conditions page.
     *
     * @return string|ResponseInterface
     */
    public function terms(): string|ResponseInterface
    {
        $schemaJson = [
            '@context'   => 'https://schema.org',
            '@type'      => 'WebPage',
            'name'       => 'Terms & Conditions | Kong Safaris',
            'description' => 'Complete terms and conditions governing bookings, payments, cancellations, safety rules, and penalty schedules for Kong Safaris services.',
            'isPartOf'   => [
                '@type' => 'TravelAgency',
                'name'  => 'Kong Safaris',
            ],
            'about'      => 'Terms of service for safari vehicle rental and tour transport bookings.',
        ];

        return view('App\Modules\Legal\Views\terms', [
            'pageTitle'       => 'Terms & Conditions | Kong Safaris',
            'metaDescription' => 'Complete terms governing safari bookings, payments, cancellations, safety conduct rules, and penalty schedules for Kong Safaris services in Kenya.',
            'metaKeywords'    => 'Kong Safaris terms, safari booking terms, cancellation policy, safety rules, penalty schedule, Kenya',
            'canonicalUrl'    => url_to('legal.terms'),
            'robotsTag'       => 'index, follow',
            'metaImage'       => base_url('assets/img/safari-hero.png'),
            'schemaJson'      => $schemaJson,
        ]);
    }

    /**
     * Display the Privacy Policy page.
     *
     * @return string|ResponseInterface
     */
    public function privacy(): string|ResponseInterface
    {
        $schemaJson = [
            '@context'   => 'https://schema.org',
            '@type'      => 'WebPage',
            'name'       => 'Privacy Policy | Kong Safaris',
            'description' => 'Privacy policy detailing how Kong Safaris collects, uses, stores, and protects personal data including booking information, M-Pesa payments, and trip location tracking.',
            'isPartOf'   => [
                '@type' => 'TravelAgency',
                'name'  => 'Kong Safaris',
            ],
            'about'      => 'Data protection and privacy practices for Kong Safaris customers.',
        ];

        return view('App\Modules\Legal\Views\privacy', [
            'pageTitle'       => 'Privacy Policy | Kong Safaris',
            'metaDescription' => 'Privacy policy for Kong Safaris — how we collect, use, and protect your personal data including booking information, M-Pesa payments, and trip location tracking.',
            'metaKeywords'    => 'Kong Safaris privacy, data protection, M-Pesa data handling, privacy policy Kenya, booking data',
            'canonicalUrl'    => url_to('legal.privacy'),
            'robotsTag'       => 'index, follow',
            'metaImage'       => base_url('assets/img/safari-hero.png'),
            'schemaJson'      => $schemaJson,
        ]);
    }
}