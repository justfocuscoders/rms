<?php
namespace App\Models;

use CodeIgniter\Model;

class ItemModel extends Model
{
    protected $table = 'items';
    protected $primaryKey = 'id';
    protected $useTimestamps = true;

    protected $allowedFields = [
        'code',
        'name',
        'description',
        'unit_price',       
        'unit_id',
        'supplier_id',
        'category_id',
        'department_id',
        'storage_condition_id',
        'reorder_level',     
        'hsn_code',          
        'is_active',         
        'image',             
        'created_by',        
        'created_at',
        'updated_at'
    ];

    // 🟢 Validation rules for inserting new items
    protected $validationRulesInsert = [
        'code'     => 'required|min_length[2]|max_length[50]|is_unique[items.code]',
        'name'     => 'required|min_length[2]|max_length[255]',
        'unit_id'  => 'required|integer',
        'unit_price' => 'permit_empty|decimal',
        'reorder_level' => 'permit_empty|integer',
    ];

    // 🟡 Validation rules for updating existing items
    protected $validationRulesUpdate = [
        'code'     => 'required|min_length[2]|max_length[50]|is_unique[items.code,id,{id}]',
        'name'     => 'required|min_length[2]|max_length[255]',
        'unit_id'  => 'required|integer',
        'unit_price' => 'permit_empty|decimal',
        'reorder_level' => 'permit_empty|integer',
    ];
}
