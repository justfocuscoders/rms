<?php

namespace App\Controllers;

use App\Models\PurchaseOrderModel;

class Procurement extends BaseController
{
    protected $poModel;

    public function __construct()
    {
        $this->poModel = new PurchaseOrderModel();
    }

    public function dashboard()
    {
        // 🔹 Fetching PO summary stats (optional — you can remove if not ready)
        $data['total_po'] = $this->poModel->countAllResults();
        $data['pending_po'] = $this->poModel->where('status', 'Pending')->countAllResults();
        $data['approved_po'] = $this->poModel->where('status', 'Approved')->countAllResults();
        $data['cancelled_po'] = $this->poModel->where('status', 'Cancelled')->countAllResults();

        // 🔹 Breadcrumbs (used by breadcrumb.php included in header)
        $data['breadcrumbs'] = [
            ['title' => 'Home', 'url' => '/dashboard'],
            ['title' => 'Procurement Dashboard']
        ];

        // 🔹 Load the view
        return view('dashboard/procurement', $data);
    }
}
