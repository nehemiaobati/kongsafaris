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

    .legal-section h5 {
        color: var(--theme-primary);
        font-weight: 600;
        margin-top: 1rem;
        margin-bottom: 0.5rem;
    }

    .legal-section ul {
        padding-left: 1.5rem;
    }

    .legal-section ul li {
        margin-bottom: 0.4rem;
        color: rgba(var(--theme-primary-rgb), 0.75);
        line-height: 1.7;
    }

    .legal-section p {
        color: rgba(var(--theme-primary-rgb), 0.75);
        line-height: 1.8;
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

    .penalty-table {
        font-size: 0.9rem;
    }

    .penalty-table thead th {
        background-color: var(--theme-accent);
        color: #ffffff;
        font-weight: 600;
        border: none;
    }

    .penalty-table td,
    .penalty-table th {
        vertical-align: middle;
    }

    .penalty-table tbody tr:hover {
        background-color: rgba(var(--theme-accent-rgb), 0.04);
    }

    .table-container {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .enforcement-list {
        counter-reset: enforcement-step;
        list-style: none;
        padding-left: 0;
    }

    .enforcement-list li {
        counter-increment: enforcement-step;
        padding-left: 2.5rem;
        position: relative;
        margin-bottom: 1rem;
        line-height: 1.7;
    }

    .enforcement-list li::before {
        content: counter(enforcement-step);
        position: absolute;
        left: 0;
        top: 0;
        width: 1.75rem;
        height: 1.75rem;
        background-color: var(--theme-accent);
        color: #ffffff;
        font-weight: 700;
        font-size: 0.85rem;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .table> :not(caption)>*>* {
        padding: 0.75rem;
    }

    @media (max-width: 768px) {
        .legal-section ul {
            padding-left: 1.25rem;
        }

        .enforcement-list li {
            padding-left: 2.25rem;
        }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="blueprint-header mb-4">
    <h1 class="display-5 fw-bold">Terms & Conditions</h1>
    <p class="text-muted lead">Last updated: June 2026</p>
</div>

<div class="row g-4">

    <!-- Sidebar Table of Contents (desktop) -->
    <div class="col-lg-3 d-none d-lg-block">
        <div class="card blueprint-card p-4" style="position: sticky; top: 90px; z-index: 1;">
            <h6 class="fw-bold mb-3">Table of Contents</h6>
            <nav id="toc">
                <a href="#terms-general" class="toc-link">Part A: General Terms</a>
                <a href="#section-1" class="toc-link toc-link-sub">1. Bookings & Payments</a>
                <a href="#section-2" class="toc-link toc-link-sub">2. Cancellation & Refund</a>
                <a href="#section-3" class="toc-link toc-link-sub">3. Client Responsibilities</a>
                <a href="#section-4" class="toc-link toc-link-sub">4. Service Delivery</a>
                <a href="#section-5" class="toc-link toc-link-sub">5. Liability & Disclaimers</a>
                <a href="#section-6" class="toc-link toc-link-sub">6. Corporate Bookings</a>
                <a href="#section-7" class="toc-link toc-link-sub">7. Marketing & Photography</a>
                <a href="#section-8" class="toc-link toc-link-sub">8. Governing Law</a>
                <a href="#terms-safety" class="toc-link mt-2">Part B: Safety Rules</a>
                <a href="#section-s1" class="toc-link toc-link-sub">1. Absolute Compliance</a>
                <a href="#section-s2" class="toc-link toc-link-sub">2. Prohibited Behaviours</a>
                <a href="#section-s3" class="toc-link toc-link-sub">3. Health & Fitness</a>
                <a href="#section-s4" class="toc-link toc-link-sub">4. Code of Conduct</a>
                <a href="#section-s5" class="toc-link toc-link-sub">5. Transport & Equipment</a>
                <a href="#section-s6" class="toc-link toc-link-sub">6. Driver's Authority</a>
                <a href="#section-s7" class="toc-link toc-link-sub">7. Vandalism & Damage</a>
                <a href="#section-s8" class="toc-link toc-link-sub">8. Prohibited Items</a>
                <a href="#section-s9" class="toc-link toc-link-sub">9. Indemnification</a>
                <a href="#penalty-schedule" class="toc-link mt-2">Part C: Penalty Schedule</a>
                <a href="#enforcement" class="toc-link mt-2">Part D: Enforcement</a>
            </nav>
        </div>
    </div>

    <!-- Main Content -->
    <div class="col-lg-9">

        <!-- Part A: General Terms -->
        <div id="terms-general" class="legal-section mb-5">
            <div class="card blueprint-card p-4 p-lg-5">
                <h2 class="fw-bold mb-4">Part A: General Terms & Conditions</h2>
                <p class="text-muted fst-italic mb-4">These General Terms & Conditions govern all bookings, payments, and use of Kong Safaris services. By booking with us, you accept these terms in full.</p>

                <!-- Section 1 -->
                <div id="section-1" class="legal-section">
                    <h3>1. Bookings & Payments</h3>

                    <h4>Booking Process</h4>
                    <ul>
                        <li><strong>Official Channels:</strong> All bookings must be made through official channels (website, email, or phone).</li>
                        <li><strong>Confirmation:</strong> Bookings are only confirmed upon receipt of the required deposit or full payment.</li>
                        <li><strong>Advance Booking Notice:</strong>
                            <ul>
                                <li>A minimum of 48 hours in advance is required for regular services.</li>
                                <li>Peak season and special events require 1 to 2 weeks advance booking.</li>
                            </ul>
                        </li>
                    </ul>

                    <h4>Payment Terms</h4>
                    <ul>
                        <li><strong>Deposit:</strong> A deposit of 30% to 50% of the total cost is required for advance bookings.</li>
                        <li><strong>Full Payment:</strong> The remaining balance must be paid in full at least 24 hours before the service begins.</li>
                        <li><strong>Accepted Payment Methods:</strong> Cash, mobile money (M-Pesa), and bank transfer.</li>
                        <li><strong>Corporate Accounts:</strong> Monthly billing terms may be arranged for corporate clients with approved credit.</li>
                    </ul>
                </div>

                <!-- Section 2 -->
                <div id="section-2" class="legal-section mt-5">
                    <h3>2. Cancellation & Refund Policy</h3>

                    <h4>Client-Initiated Cancellations</h4>
                    <ul>
                        <li><strong>24+ Hours Notice:</strong> Full refund of deposit, minus a 10% processing fee.</li>
                        <li><strong>12–24 Hours Notice:</strong> 50% refund of the deposit.</li>
                        <li><strong>Less than 12 Hours Notice:</strong> Deposit is non-refundable.</li>
                        <li><strong>No-Show:</strong> Full charge of the booked service applies.</li>
                    </ul>

                    <h4>Emergency Cancellations</h4>
                    <p>Medical emergencies, family bereavements, or government-imposed travel restrictions will be reviewed on a case-by-case basis for potential full or partial refunds.</p>
                </div>

                <!-- Section 3 -->
                <div id="section-3" class="legal-section mt-5">
                    <h3>3. Client Responsibilities</h3>
                    <ul>
                        <li><strong>Accuracy:</strong> Provide precise pickup/drop-off details and contact information.</li>
                        <li><strong>Punctuality:</strong> Be ready at the agreed-upon pickup time (a 15-minute grace period applies).</li>
                        <li><strong>Identification:</strong> Ensure all traveling passengers possess valid identification documents.</li>
                        <li><strong>Special Requirements:</strong> Disclose any accessibility needs, child safety seat requirements, or special accommodations in advance.</li>
                        <li><strong>Cleanliness & Rules:</strong> Adhere to the vehicle cleanliness guidelines and the strict no-smoking policy.</li>
                        <li><strong>Damage Reporting:</strong> Report any vehicle damages or issues immediately to the operator.</li>
                        <li><strong>Legal Compliance:</strong> Comply fully with local traffic laws and regulations during the journey.</li>
                        <li><strong>Capacity Limits:</strong> Ensure passenger counts do not exceed the vehicle's legal seating capacity.</li>
                    </ul>
                </div>

                <!-- Section 4 -->
                <div id="section-4" class="legal-section mt-5">
                    <h3>4. Service Delivery</h3>

                    <h4>Standard Service</h4>
                    <ul>
                        <li>Drivers must be professionally licensed with clean driving records.</li>
                        <li>Vehicles must be well-maintained, insured, and suitable for the intended service.</li>
                        <li>Service includes a 15-minute arrival window for scheduled pickups.</li>
                    </ul>

                    <h4>Weather & Emergency Rescheduling</h4>
                    <p>The operator reserves the right to reschedule services without penalty due to:</p>
                    <ul>
                        <li>Severe weather conditions (e.g., heavy rain, flooding).</li>
                        <li>Road closures or government restrictions.</li>
                        <li>Vehicle breakdown (a replacement vehicle will be provided when possible).</li>
                        <li>Force majeure events.</li>
                    </ul>
                    <p class="fst-italic">In these circumstances, services will be rescheduled at no extra cost, or a full refund will be provided if rescheduling is not possible.</p>
                </div>

                <!-- Section 5 -->
                <div id="section-5" class="legal-section mt-5">
                    <h3>5. Liability & Disclaimers</h3>

                    <h4>Insurance Coverage</h4>
                    <ul>
                        <li>Vehicles carry comprehensive insurance coverage.</li>
                        <li>Personal accident coverage is provided to passengers as outlined under the insurance policy terms.</li>
                        <li>Third-party liability is maintained in accordance with national laws.</li>
                    </ul>

                    <h4>Limitations of Liability</h4>
                    <ul>
                        <li><strong>Personal Belongings:</strong> No liability is accepted for lost, stolen, or damaged personal effects left inside vehicles.</li>
                        <li><strong>Delays:</strong> No liability is accepted for delays resulting from traffic, weather, road conditions, or unexpected closures.</li>
                        <li><strong>Consequential Loss:</strong> No liability is accepted for consequential damages, lost profits, or lost business opportunities.</li>
                        <li><strong>Medical Conditions:</strong> Clients travel at their own risk regarding pre-existing medical conditions.</li>
                    </ul>
                    <p><em>Note: All passengers are strongly encouraged to secure their own comprehensive travel and medical insurance.</em></p>
                </div>

                <!-- Section 6 -->
                <div id="section-6" class="legal-section mt-5">
                    <h3>6. Corporate & Group Bookings</h3>
                    <ul>
                        <li>Preferential rates and corporate contracts are available for regular accounts.</li>
                        <li>Group bookings of 10 or more passengers qualify for volume discounts.</li>
                        <li>Group organizers are solely responsible for coordinating passenger counts, ensuring headcounts are accurate, and communicating changes promptly.</li>
                    </ul>
                </div>

                <!-- Section 7 -->
                <div id="section-7" class="legal-section mt-5">
                    <h3>7. Marketing & Photography</h3>
                    <ul>
                        <li><strong>Usage Rights:</strong> By using the services, clients grant the operator permission to capture photographs and video of the service for marketing, social media, website, and promotional purposes.</li>
                        <li><strong>Privacy Controls:</strong>
                            <ul>
                                <li>Images displaying clear facial views will not be used without explicit consent.</li>
                                <li>Clients may opt out of photography and videography upon request before or during the service.</li>
                                <li>Personal information is handled confidentially under the privacy policy.</li>
                            </ul>
                        </li>
                    </ul>
                </div>

                <!-- Section 8 -->
                <div id="section-8" class="legal-section mt-5">
                    <h3>8. Governing Law & Dispute Resolution</h3>
                    <ul>
                        <li><strong>Applicable Law:</strong> These terms are governed by the laws of the Republic of Kenya.</li>
                        <li><strong>Written Complaints:</strong> Formal complaints must be submitted in writing within seven (7) days of the incident.</li>
                        <li><strong>Resolution Process:</strong> Parties are encouraged to resolve disputes through direct communication and alternative dispute resolution methods before resorting to legal proceedings. Unresolved legal actions fall under the jurisdiction of Kenyan courts.</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Part B: Safety Rules -->
        <div id="terms-safety" class="legal-section mb-5">
            <div class="card blueprint-card p-4 p-lg-5">
                <h2 class="fw-bold mb-3">Part B: Safety, Conduct, & Responsibility Rules</h2>
                <div class="alert alert-warning d-flex align-items-center gap-2" role="alert">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-exclamation-triangle flex-shrink-0" viewBox="0 0 16 16">
                        <path d="M7.938 2.016A.13.13 0 0 1 8.002 2a.13.13 0 0 1 .063.016.146.146 0 0 1 .054.057l6.857 11.667c.036.06.035.124.002.183a.163.163 0 0 1-.054.06.116.116 0 0 1-.066.017H1.146a.115.115 0 0 1-.066-.017.163.163 0 0 1-.054-.06.176.176 0 0 1 .002-.183L7.884 2.073a.147.147 0 0 1 .054-.057zm1.044-.45a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566z" />
                        <path d="M7.002 12a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 5.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995z" />
                    </svg>
                    <span>These safety and conduct policies are <strong>strictly enforced</strong>. Booking a service implies full, unconditional acceptance of these terms. Ignorance of these rules will not be accepted as a valid defense.</span>
                </div>

                <!-- Safety Section 1 -->
                <div id="section-s1" class="legal-section">
                    <h3>1. Absolute Compliance</h3>
                    <p>All passengers must immediately obey every instruction issued by drivers, guides, or authorized coordinators. Refusal to comply is grounds for immediate ejection from the trip without a refund.</p>
                </div>

                <!-- Safety Section 2 -->
                <div id="section-s2" class="legal-section mt-4">
                    <h3>2. Prohibited Dangerous Behaviours</h3>
                    <ul>
                        <li>No hanging on, leaning out of, or climbing onto any moving vehicle under any circumstances.</li>
                        <li>No sitting on windows, doors, roofs, bonnets, bumpers, or any exterior vehicle parts.</li>
                        <li>No placing arms, legs, heads, or other body parts outside a moving vehicle.</li>
                        <li>No standing, unbuckling seatbelts, or opening doors while the vehicle is in motion.</li>
                        <li>No distracting, physical contact, or interference with the driver.</li>
                        <li>No consumption of alcohol or narcotics inside the vehicle unless pre-authorized expressly in writing.</li>
                    </ul>
                    <p><strong>Violations of these rules result in immediate trip cancellation, zero refund, a mandatory fine, potential civil lawsuits, and reports filed with transport authorities.</strong></p>
                </div>

                <!-- Safety Section 3 -->
                <div id="section-s3" class="legal-section mt-4">
                    <h3>3. Health, Fitness, & Disclosure</h3>
                    <ul>
                        <li>Clients must be medically and physically fit for the booked trip.</li>
                        <li>All pre-existing medical conditions, allergies, pregnancies, physical disabilities, and regular medications must be disclosed in writing before departure.</li>
                        <li>Failure to disclose pre-existing conditions voids all operator liability and may result in denied boarding with zero refund.</li>
                    </ul>
                </div>

                <!-- Safety Section 4 -->
                <div id="section-s4" class="legal-section mt-4">
                    <h3>4. Code of Conduct</h3>
                    <p>Rowdy, disruptive, intoxicated, abusive, harassing, indecent, racist, tribalistic, sexually inappropriate, or threatening behaviour toward staff, fellow clients, or members of the public will result in immediate removal from the trip without refund, alongside potential reports to law enforcement.</p>
                </div>

                <!-- Safety Section 5 -->
                <div id="section-s5" class="legal-section mt-4">
                    <h3>5. Transport & Equipment</h3>
                    <p>Any provided equipment (tents, ropes, life jackets, etc.) is used entirely at the client's own risk. Clients will be billed the full replacement cost for any lost, misused, or damaged equipment.</p>
                </div>

                <!-- Safety Section 6 -->
                <div id="section-s6" class="legal-section mt-4">
                    <h3>6. Driver's Authority</h3>
                    <p>The driver has final, unquestionable authority to immediately cancel a trip, expel offending passengers at the nearest safe location, and return the vehicle directly to base if any safety guideline is breached. No refunds will be issued.</p>
                </div>

                <!-- Safety Section 7 -->
                <div id="section-s7" class="legal-section mt-4">
                    <h3>7. Vehicle Vandalism & Damage</h3>
                    <p>Any passenger who vandalizes, defaces, soils, or damages any part of the vehicle is fully liable for the cost of brand-new original equipment manufacturer (OEM) replacements, labour, and downtime.</p>
                </div>

                <!-- Safety Section 8 -->
                <div id="section-s8" class="legal-section mt-4">
                    <h3>8. Strictly Prohibited Items</h3>
                    <ul>
                        <li><strong>Weapons:</strong> Absolutely no weapons of any kind (firearms, knives, machetes, explosives, tasers, pepper spray, etc.) are allowed. Possession is treated as a direct, life-threatening act; the trip will be cancelled instantly, zero refund issued, and the offender handed over to the police.</li>
                        <li><strong>Contraband:</strong> Narcotics, illegal drugs, and smuggled goods are strictly forbidden. Offenders will be removed immediately and handed over to law enforcement.</li>
                    </ul>
                </div>

                <!-- Safety Section 9 -->
                <div id="section-s9" class="legal-section mt-4">
                    <h3>9. Indemnification</h3>
                    <p>Clients agree to fully indemnify and hold harmless the operator, its directors, employees, drivers, and partners from any and all claims, damages, losses, fines, or legal costs arising from the client's breach of safety terms or unlawful acts.</p>
                </div>
            </div>
        </div>

        <!-- Part C: Penalty Schedule -->
        <div id="penalty-schedule" class="legal-section mb-5">
            <div class="card blueprint-card p-4 p-lg-5">
                <h2 class="fw-bold mb-3">Part C: Penalty & Charge Schedule (KES)</h2>
                <p class="text-muted mb-4">The following schedule sets out the minimum penalties applied to safety and vandalism violations. The operator reserves the right to charge higher amounts where actual damages, legal costs, or third-party claims exceed these figures. All amounts are in <strong>Kenyan Shillings (KES)</strong> and are payable immediately upon demand.</p>

                <div class="table-container">
                    <table class="table table-bordered penalty-table">
                        <thead>
                            <tr>
                                <th scope="col">Violation</th>
                                <th scope="col" style="min-width: 130px;">Min. Penalty (KES)</th>
                                <th scope="col">Additional Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Sitting on windows / hanging on doors / body parts outside a moving vehicle</td>
                                <td>50,000</td>
                                <td>Immediate trip cancellation, zero refund, transport authority report, civil lawsuit</td>
                            </tr>
                            <tr>
                                <td>Distracting, touching, or interfering with the driver</td>
                                <td>30,000</td>
                                <td>Immediate ejection at nearest safe point, zero refund</td>
                            </tr>
                            <tr>
                                <td>Refusal to obey driver / guide / coordinator instructions</td>
                                <td>20,000</td>
                                <td>Immediate ejection, zero refund</td>
                            </tr>
                            <tr>
                                <td>Unauthorised alcohol or substance consumption on board</td>
                                <td>25,000</td>
                                <td>Ejection, zero refund, potential police report</td>
                            </tr>
                            <tr>
                                <td>Smoking / vaping inside the vehicle</td>
                                <td>15,000</td>
                                <td>Includes mandatory deep-cleaning fee</td>
                            </tr>
                            <tr>
                                <td>Soiling vehicle (vomit, food, drink, mud, blood) requiring deep cleaning</td>
                                <td>10,000 – 30,000</td>
                                <td>Full valeting and downtime charges applied</td>
                            </tr>
                            <tr>
                                <td>Damaged / torn seat upholstery (per seat)</td>
                                <td>25,000</td>
                                <td>Full replacement with new OEM part</td>
                            </tr>
                            <tr>
                                <td>Broken window / mirror / light</td>
                                <td>From 20,000 (at cost)</td>
                                <td>New OEM replacement + labour + downtime</td>
                            </tr>
                            <tr>
                                <td>Body panel / paint / interior trim vandalism</td>
                                <td>From 50,000 (at cost)</td>
                                <td>Full panel beating, respray, and downtime billed</td>
                            </tr>
                            <tr>
                                <td>Theft of vehicle accessories (chargers, seatbelts, tools, etc.)</td>
                                <td>3x replacement cost</td>
                                <td>Police report and criminal charges</td>
                            </tr>
                            <tr>
                                <td>Loss / damage to provided equipment (tents, life jackets, gear)</td>
                                <td>Replacement cost + 20%</td>
                                <td>Billed before disembarking</td>
                            </tr>
                            <tr>
                                <td>Possession of weapons on board</td>
                                <td>100,000</td>
                                <td>Trip cancelled, zero refund, immediate handover to police</td>
                            </tr>
                            <tr>
                                <td>Possession of narcotics / illegal substances / contraband</td>
                                <td>100,000</td>
                                <td>Handover to authorities and criminal prosecution</td>
                            </tr>
                            <tr>
                                <td>Abusive, harassing, racist, tribalistic, or sexually inappropriate conduct</td>
                                <td>25,000</td>
                                <td>Immediate ejection, lifetime booking ban, potential police report</td>
                            </tr>
                            <tr>
                                <td>Causing total loss / write-off of a vehicle through wilful misconduct</td>
                                <td>Full market value + 30 days lost income</td>
                                <td>Civil suit and criminal charges for destruction of property</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Part D: Enforcement -->
        <div id="enforcement" class="legal-section mb-5">
            <div class="card blueprint-card p-4 p-lg-5">
                <h2 class="fw-bold mb-3">Part D: Penalty Enforcement and Charge Initiation Processes</h2>

                <ol class="enforcement-list">
                    <li><strong>On-the-Spot Documentation:</strong> The driver or guide records the violation in the trip incident log, captures photo and/or video evidence, and obtains witness statements where possible.</li>
                    <li><strong>Written Notice:</strong> The offending client is issued an Incident & Charge Notice via digital channels (SMS, messaging application, or email) stating the violation, the applicable penalty, and the payment deadline.</li>
                    <li><strong>Deposit Forfeiture:</strong> Any deposit, advance payment, or balance held by the operator is automatically applied against the penalty without further notice.</li>
                    <li><strong>Demand for Payment:</strong> Any remaining balance must be settled within seventy-two (72) hours. Late payments attract interest of 5% per month, compounded.</li>
                    <li><strong>Assessment for Damage:</strong> For vandalism or physical damage, a written quotation is obtained from an authorised garage or supplier. The client may, at their own cost, obtain an independent second quotation within 48 hours; otherwise, the operator's quotation is final and binding.</li>
                    <li><strong>Police Involvement:</strong> For weapons, narcotics, theft, assault, or wilful destruction of property, a report is filed with the local police station to obtain an Occurrence Book (OB) number. Criminal proceedings are independent of, and in addition to, civil penalties.</li>
                    <li><strong>Debt Recovery:</strong> Unpaid penalties are referred to debt collection agencies or legal representatives. The client is responsible for all recovery costs, advocate fees, court filing fees, and accrued interest.</li>
                    <li><strong>Civil Suit:</strong> If payment is refused, civil proceedings will be instituted in the competent local court, to whose jurisdiction the client consents.</li>
                    <li><strong>Booking Ban:</strong> Any client with an unpaid penalty is permanently blacklisted from services and may be reported to industry partners.</li>
                    <li><strong>Cumulative Charges:</strong> Where multiple violations occur, penalties are cumulative. Fines constitute liquidated damages agreed upon at booking and are not subject to renegotiation.</li>
                </ol>
            </div>
        </div>

    </div>
</div>

<?= $this->endSection() ?>