<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): \CodeIgniter\HTTP\ResponseInterface|string
    {
        return view('App\Views\landing', [
            'pageTitle'       => 'Kong Safaris — Premium Safari Car Hire, Tour Bookings & Fleet Operations',
            'metaDescription' => 'Book premium safari vehicles online in Kenya. Kong Safaris offers customized Land Cruisers and safari vans with professional drivers, real-time GPS tracking, and secure M-Pesa payments for trips to Maasai Mara, Amboseli, and more.',
            'metaKeywords'    => 'safari car hire Kenya, safari booking, Amboseli transport, Maasai Mara land cruiser, tourist van rental Kenya, M-Pesa safari payment, fleet tracking, travel Kenya',
            'canonicalUrl'    => url_to('home'),
            'robotsTag'       => 'index, follow',
        ]);
    }
}
