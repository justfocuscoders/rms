<?php
namespace App\Models;

use CodeIgniter\Model;

class MrsModel extends Model
{
    protected $table = 'mrs';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'mrs_no', 'department_id', 'batch_id', 'requested_by', 'mrs_date',
        'status', 'remarks', 'created_at', 'updated_at'
    ];
    protected $useTimestamps = false;
    protected $returnType = 'array'; // ✅ This line fixes your dashboard issue

    /** ----------------------------------------
     * Generate Auto MRS Number
     * ---------------------------------------- */
    public function generateMrsNo()
    {
        $last = $this->select('mrs_no')->orderBy('id', 'DESC')->first();
        $num = 1;
        if ($last && preg_match('/(\d+)$/', $last['mrs_no'], $m)) {
            $num = intval($m[1]) + 1;
        }
        return 'MRS-' . str_pad($num, 4, '0', STR_PAD_LEFT);
    }

    /** ----------------------------------------
     * Get MRS + Details (with stock info)
     * ---------------------------------------- */
    public function getMrsWithDetails($id)
    {
        // 🔹 Get MRS Header Info
        $mrs = $this
            ->select('mrs.*, d.name as department, u.name as requested_by_name')
            ->join('departments d', 'd.id = mrs.department_id', 'left')
            ->join('users u', 'u.id = mrs.requested_by', 'left')
            ->where('mrs.id', $id)
            ->first();

        if (!$mrs) return null;

        // 🔹 Get Details + Live Stock Qty
        $db = \Config\Database::connect();

        $mrs['details'] = $db->table('mrs_details md')
            ->select("
                md.*,
                i.name as item_name,
                i.code as item_code,
                IFNULL((
                    SELECT SUM(sm.qty)
                    FROM stock_movements sm
                    WHERE sm.stock_id = i.id
                ), 0) AS current_stock
            ")
            ->join('items i', 'i.id = md.item_id', 'left')
            ->where('md.mrs_id', $id)
            ->get()
            ->getResultArray();

        return $mrs;
    }
}
