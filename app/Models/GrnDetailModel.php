<?php
namespace App\Models;

use CodeIgniter\Model;

class GrnDetailModel extends Model
{
    protected $table = 'grn_details';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'grn_id',
        'item_id',
        'storage_location_id',
        'batch_no',
        'expiry_date',
        'mfg_date',
        'qty_received',
        'unit_id',
        'rate',
        'amount',
        'tax_percent',
        'tax_amount',
        'total_amount',
        'remarks',
        'status',
        'created_by',
        'created_at',
        'updated_at',
        'capacity',
        'noc',
        'weight',
        'lot_no',
    ];

    protected $useTimestamps = false;

    protected $validationRules = [
        'grn_id'       => 'required|integer',
        'item_id'      => 'required|integer',
        'unit_id'      => 'required|integer',
        'qty_received' => 'required|numeric|greater_than[0]',
        'rate'         => 'required|numeric|greater_than_equal_to[0]',
    ];

    protected $validationMessages = [
        'item_id' => [
            'required' => 'Item is required for each row.'
        ],
        'unit_id' => [
            'required' => 'Unit must be selected.'
        ],
        'qty_received' => [
            'required'     => 'Quantity cannot be blank.',
            'greater_than' => 'Quantity must be greater than zero.'
        ],
    ];

}