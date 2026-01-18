<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ContentController extends Controller
{
    /**
     * Get FAQ content.
     */
    public function faq()
    {
        $faqs = [
            [
                'id' => 1,
                'question' => 'How do I search for properties?',
                'answer' => 'Go to the "Explore Properties" section from your dashboard. You can use filters like location, rent range, number of bedrooms, and property type to find your perfect home.',
                'category' => 'General'
            ],
            [
                'id' => 2,
                'question' => 'How do I submit a rental request?',
                'answer' => 'Once you find a property you like, click "View Details" and then "Request to Rent". Fill in the required information and submit your request.',
                'category' => 'Rentals'
            ],
            [
                'id' => 3,
                'question' => 'How do I pay rent through the platform?',
                'answer' => 'Navigate to "Pay Rent" from the sidebar. Select your rental property, enter the amount, and choose your payment method.',
                'category' => 'Payments'
            ],
            [
                'id' => 4,
                'question' => 'Can I schedule property viewings?',
                'answer' => 'Yes! On the property details page, you\'ll find an option to schedule a viewing. Choose your preferred date and time.',
                'category' => 'Viewings'
            ],
            [
                'id' => 5,
                'question' => 'What should I do if I have issues with my rental?',
                'answer' => 'First, try to communicate with your landlord through the messaging system. If the issue persists, submit a support ticket using the support form.',
                'category' => 'Support'
            ],
            [
                'id' => 6,
                'question' => 'How do I update my profile information?',
                'answer' => 'Click on your profile picture in the top right corner and select "Settings" or "My Profile". From there, you can update your personal information, profile picture, and preferences.',
                'category' => 'Account'
            ],
            [
                'id' => 7,
                'question' => 'How does the digital signature work for contracts?',
                'answer' => 'Our platform uses RSA-based digital signatures. When you sign a contract, your unique cryptographic key creates a signature that can be verified later. This ensures the contract\'s authenticity and prevents tampering.',
                'category' => 'Contracts'
            ],
            [
                'id' => 8,
                'question' => 'How do I report a property or user?',
                'answer' => 'You can report a property from the property details page or report a user from their profile. Provide details and any supporting evidence. Our admin team will review the report and take appropriate action.',
                'category' => 'Safety'
            ],
        ];

        return response()->json([
            'success' => true,
            'data' => $faqs
        ]);
    }

    /**
     * Get About page content.
     */
    public function about()
    {
        $about = [
            'title' => 'About RENTIFY',
            'description' => 'RENTIFY is a comprehensive rental platform that connects tenants, landlords, and property agents. Our mission is to make the rental process simple, transparent, and secure for everyone.',
            'features' => [
                [
                    'title' => 'Wide Property Selection',
                    'description' => 'Browse through thousands of verified properties across various locations and types.'
                ],
                [
                    'title' => 'Secure Payments',
                    'description' => 'Pay rent securely through our platform with multiple payment options and instant receipts.'
                ],
                [
                    'title' => 'Digital Contracts',
                    'description' => 'Sign contracts digitally with legally binding e-signatures and QR verification.'
                ],
                [
                    'title' => 'Real-time Messaging',
                    'description' => 'Communicate directly with landlords and tenants through our secure messaging system.'
                ],
                [
                    'title' => 'Verified Listings',
                    'description' => 'All properties are verified by our team to ensure authenticity and accuracy.'
                ],
                [
                    'title' => '24/7 Support',
                    'description' => 'Our support team is available around the clock to assist you with any issues.'
                ],
            ],
            'stats' => [
                'properties' => '10,000+',
                'users' => '50,000+',
                'contracts_signed' => '5,000+',
                'cities_covered' => '20+',
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => $about
        ]);
    }

    /**
     * Get Privacy Policy content.
     */
    public function privacy()
    {
        $privacy = [
            'title' => 'Privacy Policy',
            'last_updated' => 'January 18, 2026',
            'sections' => [
                [
                    'heading' => 'Information We Collect',
                    'content' => 'We collect information you provide directly, including name, email, phone number, and profile information. We also collect information about your use of our platform, properties you view, and communications with other users.'
                ],
                [
                    'heading' => 'How We Use Your Information',
                    'content' => 'We use your information to provide and improve our services, facilitate rentals, process payments, send notifications, communicate with you, and ensure platform security.'
                ],
                [
                    'heading' => 'Information Sharing',
                    'content' => 'We share your information with landlords when you submit rental requests, with payment processors for transactions, and as required by law. We do not sell your personal information.'
                ],
                [
                    'heading' => 'Data Security',
                    'content' => 'We implement industry-standard security measures to protect your data, including encryption, secure authentication, and regular security audits.'
                ],
                [
                    'heading' => 'Your Rights',
                    'content' => 'You have the right to access, update, or delete your personal information. You can also opt-out of marketing communications at any time.'
                ],
                [
                    'heading' => 'Contact Us',
                    'content' => 'For privacy-related questions, contact us at privacy@rentify.com'
                ],
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => $privacy
        ]);
    }

    /**
     * Get Terms & Conditions content.
     */
    public function terms()
    {
        $terms = [
            'title' => 'Terms & Conditions',
            'last_updated' => 'January 18, 2026',
            'sections' => [
                [
                    'heading' => 'Acceptance of Terms',
                    'content' => 'By accessing and using RENTIFY, you agree to be bound by these Terms & Conditions. If you do not agree, please do not use our platform.'
                ],
                [
                    'heading' => 'User Responsibilities',
                    'content' => 'Users must provide accurate information, maintain account security, comply with applicable laws, and respect other users. You are responsible for all activities under your account.'
                ],
                [
                    'heading' => 'Property Listings',
                    'content' => 'Landlords must ensure property information is accurate and up-to-date. False or misleading listings may result in account suspension.'
                ],
                [
                    'heading' => 'Rental Agreements',
                    'content' => 'Rental agreements are legally binding contracts between tenants and landlords. RENTIFY provides the platform for these agreements but is not a party to them.'
                ],
                [
                    'heading' => 'Payments',
                    'content' => 'All payments are processed securely. Refunds are subject to the terms agreed upon in the rental agreement and our refund policy.'
                ],
                [
                    'heading' => 'Prohibited Activities',
                    'content' => 'Users may not: post fraudulent listings, harass other users, attempt to gain unauthorized access, use the platform for illegal activities, or interfere with platform operation.'
                ],
                [
                    'heading' => 'Termination',
                    'content' => 'We reserve the right to suspend or terminate accounts that violate these terms. Users may also terminate their accounts at any time.'
                ],
                [
                    'heading' => 'Limitation of Liability',
                    'content' => 'RENTIFY is not liable for any direct, indirect, incidental, or consequential damages arising from use of our platform.'
                ],
                [
                    'heading' => 'Contact',
                    'content' => 'For questions about these terms, contact us at legal@rentify.com'
                ],
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => $terms
        ]);
    }

    /**
     * Get contact information.
     */
    public function contact()
    {
        $contact = [
            'title' => 'Contact Us',
            'support_channels' => [
                [
                    'type' => 'email',
                    'label' => 'Email Support',
                    'value' => 'support@rentify.com',
                    'description' => 'Get help via email',
                ],
                [
                    'type' => 'phone',
                    'label' => 'Phone Support',
                    'value' => '+880 1234-567890',
                    'description' => 'Call us anytime',
                ],
                [
                    'type' => 'whatsapp',
                    'label' => 'WhatsApp',
                    'value' => 'https://wa.me/8801234567890',
                    'description' => 'Chat with us',
                ],
            ],
            'social_links' => [
                ['platform' => 'facebook', 'url' => 'https://facebook.com/rentify'],
                ['platform' => 'twitter', 'url' => 'https://twitter.com/rentify'],
                ['platform' => 'instagram', 'url' => 'https://instagram.com/rentify'],
                ['platform' => 'linkedin', 'url' => 'https://linkedin.com/company/rentify'],
            ],
            'address' => [
                'line1' => 'House 22, Road 8',
                'line2' => 'Bashundhara R/A',
                'city' => 'Daka',
                'country' => 'Bangladesh',
                'postal_code' => '1229',
            ],
        ];

        return response()->json([
            'success' => true,
            'data' => $contact
        ]);
    }

    /**
     * Get app settings and configuration.
     */
    public function settings()
    {
        $settings = [
            'app_name' => 'RENTIFY',
            'version' => '1.0.0',
            'features' => [
                'wallet_enabled' => true,
                'digital_contracts_enabled' => true,
                'live_chat_enabled' => true,
                'support_tickets_enabled' => true,
                'property_reviews_enabled' => true,
                'tenant_reviews_enabled' => true,
                'roommate_search_enabled' => true,
            ],
            'limits' => [
                'max_property_images' => 10,
                'max_chat_file_size_mb' => 10,
                'max_report_file_size_mb' => 5,
                'max_favorites' => 50,
            ],
            'supported_payment_methods' => [
                'credit_card',
                'debit_card',
                'bank_transfer',
                'wallet',
                'cash',
            ],
        ];

        return response()->json([
            'success' => true,
            'data' => $settings
        ]);
    }
}
