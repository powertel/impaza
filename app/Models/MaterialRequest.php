<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialRequest extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_PARTIAL = 'partial';
    public const STATUS_ISSUED = 'issued';
    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'request_number',
        'fault_id',
        'requested_by',
        'processed_by',
        'technician_note',
        'stores_note',
        'status',
        'submitted_at',
        'processed_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'processed_at' => 'datetime',
    ];

    public static function generateRequestNumber()
    {
        $prefix = 'MR-' . date('Ymd');
        $last = self::where('request_number', 'like', $prefix . '%')->latest('id')->first();
        $seq = $last ? (int) substr($last->request_number, -4) + 1 : 1;
        return $prefix . '-' . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    public function fault()
    {
        return $this->belongsTo(Fault::class);
    }

    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function items()
    {
        return $this->hasMany(MaterialRequestItem::class);
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', [self::STATUS_PENDING, self::STATUS_PROCESSING]);
    }

    public function scopeForFault($query, $faultId)
    {
        return $query->where('fault_id', $faultId);
    }

    public function isPending()
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_PROCESSING]);
    }

    public function isIssued()
    {
        return $this->status === self::STATUS_ISSUED;
    }

    public function statusBadge()
    {
        $badges = [
            self::STATUS_PENDING => ['label' => 'Pending', 'color' => '#F59E0B'],
            self::STATUS_PROCESSING => ['label' => 'Processing', 'color' => '#3B82F6'],
            self::STATUS_PARTIAL => ['label' => 'Partial', 'color' => '#8B5CF6'],
            self::STATUS_ISSUED => ['label' => 'Issued', 'color' => '#10B981'],
            self::STATUS_CANCELLED => ['label' => 'Cancelled', 'color' => '#EF4444'],
        ];
        return $badges[$this->status] ?? ['label' => ucfirst($this->status), 'color' => '#64748B'];
    }
}
