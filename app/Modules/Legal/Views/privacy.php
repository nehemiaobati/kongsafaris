<?= $this->extend('layouts/default') ?>

<?= $this->section('styles') ?>
<style>
    .legal-section {
        scroll-margin-top: 100px;
    }

    .legal-section h3 {
        color: var(--theme-primary);
        font-weight: 700;
        margin-bottom: 1.25rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid var(--theme-accent);
    }

    .legal-section h4 {
        color: var(--theme-primary);
        font-weight: 600;
        margin-top: 1.5rem;
        margin-bottom: 0.75rem;
    }

    .legal-section p {
        color: rgba(var(--theme-primary-rgb), 0.75);
        line-height: 1.8;
    }

    .legal-section ul {
        padding-left: 1.5rem;
    }

    .legal-section ul li {
        margin-bottom: 0.4rem;
        color: rgba(var(--theme-primary-rgb), 0.75);
        line-height: 1.7;
    }

    .legal-section strong {
        color: var(--theme-primary);
    }

    .toc-link {
        color: var(--theme-accent);
        text-decoration: none;
        font-weight: 500;
        transition: opacity 0.2s ease;
        display: block;
        padding: 0.35rem 0;
        border-bottom: 1px solid rgba(var(--theme-primary-rgb), 0.06);
    }

    .toc-link:hover {
        opacity: 0.8;
        text-decoration: underline;
    }

    .toc-link-sub {
        padding-left: 1.25rem;
        font-size: 0.9rem;
    }

    @media (max-width: 768px) {
        .legal-section ul {
            padding-left: 1.25rem;
        }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="blueprint-header mb-4">
    <h1 class="display-5 fw-bold">Privacy Policy</h1>
    <p class="text-muted lead">Last updated: June 2026</p>
</div>

<div class="row g-4">

    <!-- Sidebar Table of Contents (desktop) -->
    <div class="col-lg-3 d-none d-lg-block">
        <div class="card blueprint-card p-4" style="position: sticky; top: 90px; z-index: 1;">
            <h6 class="fw-bold mb-3">Table of Contents</h6>
            <nav id="toc">
                <a href="#intro" class="toc-link">Introduction</a>
                <a href="#info-collect" class="toc-link">1. Information We Collect</a>
                <a href="#info-use" class="toc-link">2. How We Use Your Information</a>
                <a href="#info-share" class="toc-link">3. Information Sharing</a>
                <a href="#m-pesa" class="toc-link">4. M-Pesa & Payment Data</a>
                <a href="#location" class="toc-link">5. Location & Trip Tracking</a>
                <a href="#retention" class="toc-link">6. Data Retention</a>
                <a href="#rights" class="toc-link">7. Your Rights</a>
                <a href="#security" class="toc-link">8. Data Security</a>
                <a href="#cookies" class="toc-link">9. Cookies</a>
                <a href="#changes" class="toc-link">10. Changes to This Policy</a>
                <a href="#contact" class="toc-link">11. Contact Us</a>
            </nav>
        </div>
    </div>

    <!-- Main Content -->
    <div class="col-lg-9">

        <!-- Introduction -->
        <div id="intro" class="legal-section mb-5">
            <div class="card blueprint-card p-4 p-lg-5">
                <h2 class="fw-bold mb-3">Introduction</h2>
                <p>Kong Safaris Ltd ("<strong>we</strong>", "<strong>our</strong>", "<strong>us</strong>") is committed to protecting your privacy. This Privacy Policy explains how we collect, use, disclose, and safeguard your personal information when you use our website, mobile applications, and services (collectively, the "<strong>Services</strong>").</p>
                <p>By using our Services, you agree to the collection and use of information in accordance with this policy. If you do not agree with the terms of this Privacy Policy, please do not access or use our Services.</p>
                <p>We operate in full compliance with the <strong>Data Protection Act of Kenya (No. 24 of 2019)</strong> and relevant international data protection laws where applicable.</p>
            </div>
        </div>

        <!-- Section 1 -->
        <div id="info-collect" class="legal-section mb-5">
            <div class="card blueprint-card p-4 p-lg-5">
                <h3>1. Information We Collect</h3>

                <h4>Personal Information You Provide</h4>
                <p>We may collect the following categories of personal information when you book a safari, create an account, or communicate with us:</p>
                <ul>
                    <li><strong>Identity Information:</strong> Full name, date of birth, nationality, and passport or national ID number (where required for travel documentation).</li>
                    <li><strong>Contact Information:</strong> Email address, phone number, and physical address.</li>
                    <li><strong>Booking Information:</strong> Pickup and drop-off locations, travel dates, group size, vehicle preferences, special requests, and accessibility needs.</li>
                    <li><strong>Payment Information:</strong> M-Pesa transaction codes, payment receipts, and partial billing details. <em>We do not store full card numbers, M-Pesa PINs, or bank account credentials.</em></li>
                    <li><strong>Communication Records:</strong> Correspondence with our team via email, phone calls, SMS, or messaging platforms.</li>
                    <li><strong>Marketing Preferences:</strong> Your consent or objection to photography and videography during trips.</li>
                </ul>

                <h4>Information Collected Automatically</h4>
                <p>When you use our website or mobile application, we may automatically collect:</p>
                <ul>
                    <li><strong>Device Information:</strong> IP address, browser type, operating system, device identifiers, and language preferences.</li>
                    <li><strong>Usage Data:</strong> Pages visited, time spent on pages, links clicked, referral URLs, and other browsing behaviour.</li>
                    <li><strong>Location Data:</strong> Approximate geographic location derived from your IP address. With your explicit consent, precise GPS location data may be collected during active trip tracking (see Section 5).</li>
                </ul>

                <h4>Information from Third Parties</h4>
                <p>We may receive information about you from third parties, including:</p>
                <ul>
                    <li><strong>Payment Processors:</strong> Paystack and M-Pesa provide transaction confirmations and payment status updates.</li>
                    <li><strong>Corporate Partners:</strong> Travel agencies or corporate accounts providing passenger details for group bookings.</li>
                    <li><strong>Public Sources:</strong> Information available in public records or on public websites, where permitted by law.</li>
                </ul>
            </div>
        </div>

        <!-- Section 2 -->
        <div id="info-use" class="legal-section mb-5">
            <div class="card blueprint-card p-4 p-lg-5">
                <h3>2. How We Use Your Information</h3>
                <p>We use your personal information for the following purposes:</p>
                <ul>
                    <li><strong>Service Delivery:</strong> To process bookings, communicate trip details, assign drivers, coordinate pickups, and deliver safari services as requested.</li>
                    <li><strong>Payment Processing:</strong> To facilitate payments through our payment gateway, issue invoices and receipts, and manage refunds and cancellations.</li>
                    <li><strong>Real-Time Tracking:</strong> To provide live GPS tracking of your booked vehicle during active trips and share your location with dispatch teams for safety purposes.</li>
                    <li><strong>Customer Support:</strong> To respond to your enquiries, resolve issues, and provide technical support.</li>
                    <li><strong>Safety & Security:</strong> To verify identities, enforce our terms and conditions, and comply with legal obligations.</li>
                    <li><strong>Marketing Communications:</strong> To send promotional offers, newsletters, and service updates where you have provided consent. You may opt out at any time.</li>
                    <li><strong>Service Improvement:</strong> To analyse usage patterns, improve our platform, develop new features, and enhance user experience.</li>
                    <li><strong>Legal Compliance:</strong> To comply with applicable laws, regulations, legal processes, or governmental requests in Kenya and other relevant jurisdictions.</li>
                </ul>
            </div>
        </div>

        <!-- Section 3 -->
        <div id="info-share" class="legal-section mb-5">
            <div class="card blueprint-card p-4 p-lg-5">
                <h3>3. Information Sharing</h3>
                <p>We do not sell, trade, or rent your personal information to third parties. We may share your information in the following circumstances:</p>

                <h4>Service Providers</h4>
                <p>We share necessary information with trusted third-party service providers who assist us in operating our Services, including:</p>
                <ul>
                    <li><strong>Payment Processors:</strong> Paystack and Safaricom (M-Pesa) for transaction processing.</li>
                    <li><strong>Cloud Infrastructure:</strong> Hosting and data storage providers.</li>
                    <li><strong>Communication Platforms:</strong> Email and SMS delivery services.</li>
                    <li><strong>Mapping Services:</strong> Google Maps or similar providers for location tracking and route optimisation.</li>
                </ul>

                <h4>Drivers & Service Personnel</h4>
                <p>To fulfil your booking, we share your name, contact details, pickup location, and special requirements with the assigned driver and operations team members.</p>

                <h4>Legal Requirements</h4>
                <p>We may disclose your information if required to do so by law, in response to a court order, subpoena, or other legal process, or to protect our rights, property, or safety, or the rights, property, or safety of others.</p>

                <h4>Business Transfers</h4>
                <p>In the event of a merger, acquisition, or sale of all or a portion of our assets, your personal information may be transferred as part of that transaction. We will notify you of any such change and outline your choices.</p>

                <h4>With Your Consent</h4>
                <p>We may share your information for any other purpose with your explicit consent.</p>
            </div>
        </div>

        <!-- Section 4 -->
        <div id="m-pesa" class="legal-section mb-5">
            <div class="card blueprint-card p-4 p-lg-5">
                <h3>4. M-Pesa & Payment Data</h3>
                <p>Kong Safaris processes payments through Paystack, a PCI-DSS compliant payment gateway that supports M-Pesa integration in Kenya.</p>
                <ul>
                    <li><strong>What We Collect:</strong> We record the M-Pesa transaction code, amount paid, payment date, and mobile money phone number used for the transaction. This data is necessary to reconcile payments and issue receipts.</li>
                    <li><strong>What We Do NOT Collect:</strong> We do not collect, store, or process your M-Pesa PIN, SIM card details, or full bank account credentials. All sensitive payment authentication is handled entirely within the secure M-Pesa/Safaricom environment and Paystack's PCI-DSS compliant infrastructure.</li>
                    <li><strong>Third-Party Processing:</strong> Your payment data is transmitted directly to Paystack and/or Safaricom. We encourage you to review their respective privacy policies:
                        <ul>
                            <li>Paystack Privacy Policy: <a href="https://paystack.com/privacy" target="_blank" rel="noopener noreferrer">https://paystack.com/privacy</a></li>
                            <li>Safaricom (M-Pesa) Privacy Policy: <a href="https://www.safaricom.co.ke/privacy-policy" target="_blank" rel="noopener noreferrer">https://www.safaricom.co.ke/privacy-policy</a></li>
                        </ul>
                    </li>
                    <li><strong>Refunds:</strong> When processing refunds, we will use the original payment method where possible, which may require us to reference your M-Pesa transaction code. Refunds are processed in accordance with our Cancellation & Refund Policy.</li>
                </ul>
            </div>
        </div>

        <!-- Section 5 -->
        <div id="location" class="legal-section mb-5">
            <div class="card blueprint-card p-4 p-lg-5">
                <h3>5. Location & Trip Tracking</h3>
                <p>Kong Safaris provides a real-time GPS tracking feature as part of our Service. This feature is designed for safety, operational coordination, and transparency.</p>
                <ul>
                    <li><strong>How It Works:</strong> During an active trip, our driver's mobile application periodically transmits GPS coordinates (latitude and longitude) to our servers. These coordinates are used to display the vehicle's location on a map interface accessible to the customer and our operations team.</li>
                    <li><strong>Data Collected:</strong> GPS coordinates, timestamp, vehicle identifier, and trip reference number.</li>
                    <li><strong>Who Can See It:</strong> The customer who booked the trip, the assigned driver, and authorised Kong Safaris operations staff. Location data is not publicly visible or shared with third parties unless required by law.</li>
                    <li><strong>Retention:</strong> Real-time coordinate streams are retained for the duration of the trip and for a period of 30 days thereafter for dispute resolution and operational analysis. Historical trip routes may be stored in anonymised or aggregated form for longer periods.</li>
                    <li><strong>Consent:</strong> By booking a trip with tracking enabled, you consent to the collection and display of vehicle location data for the duration of your trip. Drivers are informed and consent separately through their employment agreements.</li>
                </ul>
            </div>
        </div>

        <!-- Section 6 -->
        <div id="retention" class="legal-section mb-5">
            <div class="card blueprint-card p-4 p-lg-5">
                <h3>6. Data Retention</h3>
                <p>We retain your personal information only for as long as is necessary for the purposes set out in this Privacy Policy, or as required by applicable law.</p>
                <ul>
                    <li><strong>Account Data:</strong> Retained for the duration of your account's active status plus 3 years after account closure, unless a longer retention period is required by law (e.g., for tax or legal compliance).</li>
                    <li><strong>Booking Records:</strong> Retained for 5 years from the date of the completed trip to comply with Kenyan tax and transport regulations.</li>
                    <li><strong>Payment Records:</strong> Retained for 7 years in accordance with Kenyan financial record-keeping requirements.</li>
                    <li><strong>Location Data:</strong> Active trip coordinates are retained for 30 days. Anonymised route data may be retained indefinitely for analytical purposes.</li>
                    <li><strong>Communications:</strong> Customer support correspondence is retained for 3 years from the date of resolution.</li>
                    <li><strong>Marketing Data:</strong> Retained until you opt out or withdraw consent, whichever comes first.</li>
                </ul>
                <p>When data is no longer required, we securely delete or anonymise it in accordance with our data disposal procedures.</p>
            </div>
        </div>

        <!-- Section 7 -->
        <div id="rights" class="legal-section mb-5">
            <div class="card blueprint-card p-4 p-lg-5">
                <h3>7. Your Rights</h3>
                <p>Under the Data Protection Act of Kenya and applicable international laws, you have the following rights regarding your personal information:</p>
                <ul>
                    <li><strong>Right to Access:</strong> You may request a copy of the personal information we hold about you.</li>
                    <li><strong>Right to Rectification:</strong> You may request that we correct any inaccurate or incomplete personal information.</li>
                    <li><strong>Right to Erasure:</strong> You may request that we delete your personal information, subject to legal retention obligations.</li>
                    <li><strong>Right to Restrict Processing:</strong> You may request that we limit the processing of your personal information in certain circumstances.</li>
                    <li><strong>Right to Data Portability:</strong> You may request a structured, machine-readable copy of your personal information.</li>
                    <li><strong>Right to Object:</strong> You may object to the processing of your personal information for direct marketing purposes at any time.</li>
                    <li><strong>Right to Withdraw Consent:</strong> Where processing is based on consent, you may withdraw that consent at any time without affecting the lawfulness of processing based on consent before its withdrawal.</li>
                </ul>
                <p>To exercise any of these rights, please contact us using the details in Section 11. We will respond to your request within 30 days, as required by Kenyan data protection law.</p>
                <p>If you are dissatisfied with our response, you have the right to lodge a complaint with the <strong>Office of the Data Protection Commissioner (ODPC) of Kenya</strong>.</p>
            </div>
        </div>

        <!-- Section 8 -->
        <div id="security" class="legal-section mb-5">
            <div class="card blueprint-card p-4 p-lg-5">
                <h3>8. Data Security</h3>
                <p>We implement appropriate technical and organisational measures to protect your personal information against unauthorised access, alteration, disclosure, or destruction. These measures include:</p>
                <ul>
                    <li>Encryption of data in transit using TLS 1.2+ (SSL) for all communications between your browser and our servers.</li>
                    <li>Secure, tokenised payment processing through PCI-DSS compliant third-party providers (Paystack).</li>
                    <li>Access controls and authentication protocols limiting data access to authorised personnel only.</li>
                    <li>Regular security audits, vulnerability assessments, and penetration testing of our systems.</li>
                    <li>Secure data storage with encrypted backups and offsite redundancy.</li>
                    <li>Employee training on data protection and privacy best practices.</li>
                </ul>
                <p>While we strive to protect your personal information, no method of transmission over the Internet or electronic storage is 100% secure. We cannot guarantee absolute security, but we will notify you of any data breach that is likely to result in a high risk to your rights and freedoms in accordance with legal requirements.</p>
            </div>
        </div>

        <!-- Section 9 -->
        <div id="cookies" class="legal-section mb-5">
            <div class="card blueprint-card p-4 p-lg-5">
                <h3>9. Cookies</h3>
                <p>Our website uses cookies and similar tracking technologies to improve user experience, analyse traffic, and support our operations.</p>

                <h4>What Are Cookies?</h4>
                <p>Cookies are small text files stored on your device by your web browser. They enable us to recognise your device, remember your preferences, and understand how you interact with our website.</p>

                <h4>Types of Cookies We Use</h4>
                <ul>
                    <li><strong>Essential Cookies:</strong> Required for the website to function properly. These include session cookies for login authentication and CSRF protection tokens.</li>
                    <li><strong>Analytics Cookies:</strong> Help us understand how visitors use our website, which pages are most popular, and what improvements we can make.</li>
                    <li><strong>Functional Cookies:</strong> Remember your preferences, language settings, and previously viewed content.</li>
                    <li><strong>Third-Party Cookies:</strong> From services embedded on our website, such as Google Maps for location features and payment gateway integrations.</li>
                </ul>

                <h4>Managing Cookies</h4>
                <p>You can control and manage cookies through your browser settings. Please note that disabling certain cookies may affect the functionality of our website. Essential cookies cannot be disabled as they are necessary for the operation of the Services.</p>
            </div>
        </div>

        <!-- Section 10 -->
        <div id="changes" class="legal-section mb-5">
            <div class="card blueprint-card p-4 p-lg-5">
                <h3>10. Changes to This Privacy Policy</h3>
                <p>We may update this Privacy Policy from time to time to reflect changes in our practices, legal requirements, or operational needs. When we make material changes, we will notify you by:</p>
                <ul>
                    <li>Posting the updated policy on this page with a revised "Last updated" date.</li>
                    <li>Sending an email notification to the address associated with your account, where significant changes affect your rights.</li>
                    <li>Displaying a notice on our website or within our application.</li>
                </ul>
                <p>We encourage you to review this Privacy Policy periodically. Your continued use of the Services after any changes constitutes your acceptance of the updated policy.</p>
            </div>
        </div>

        <!-- Section 11 -->
        <div id="contact" class="legal-section mb-5">
            <div class="card blueprint-card p-4 p-lg-5">
                <h3>11. Contact Us</h3>
                <p>If you have any questions, concerns, or requests regarding this Privacy Policy or our data protection practices, please contact us:</p>

                <h4>Data Protection Officer</h4>
                <ul class="list-unstyled">
                    <li><strong>Kong Safaris Ltd</strong></li>
                    <li>Nairobi, Kenya</li>
                    <li>Email: <a href="mailto:privacy@kongsafaris.com" class="text-accent">privacy@kongsafaris.com</a></li>
                    <li>Phone: +254 700 000000</li>
                </ul>

                <h4>Office of the Data Protection Commissioner (ODPC)</h4>
                <p>If you are not satisfied with our response to your data protection concern, you have the right to lodge a complaint with the regulator:</p>
                <ul class="list-unstyled">
                    <li><strong>Office of the Data Protection Commissioner</strong></li>
                    <li>P.O. Box 2183-00502,</li>
                    <li>Nairobi, Kenya</li>
                    <li>Email: <a href="mailto:complaints@odpc.go.ke" class="text-accent" target="_blank" rel="noopener noreferrer">complaints@odpc.go.ke</a></li>
                    <li>Website: <a href="https://www.odpc.go.ke" class="text-accent" target="_blank" rel="noopener noreferrer">https://www.odpc.go.ke</a></li>
                </ul>
            </div>
        </div>

    </div>
</div>

<?= $this->endSection() ?>