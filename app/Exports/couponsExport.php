<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Modules\Promotion\Models\Coupon;
use Currency;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CouponsExport implements FromCollection, WithHeadings,WithStyles
{
    public array $columns;

    public array $dateRange;

    public $id;

    public function __construct($columns, $dateRange)
    {
        $this->columns = $columns;
        $this->dateRange = $dateRange;

    }

    public function headings(): array
    {
        $modifiedHeadings = [];

        foreach ($this->columns as $column) {
            // Capitalize each word and replace underscores with spaces
            if ($column != $this->columns[1]) {
                $modifiedHeadings[] = ucwords(str_replace('_', ' ', $column));
            }
        }

        return $modifiedHeadings;
    }

    /**
     * @return \Illuminate\Support\Collection
     */

public function collection()
{
    $Promotion_id = $this->columns[1];
    $query = Coupon::where('promotion_id', $Promotion_id)
                   ->whereDate('created_at', '>=', $this->dateRange[0])
                   ->whereDate('created_at', '<=', $this->dateRange[1])
                   ->get();

    $newQuery = $query->map(function ($row) {
        $selectedData = [];

        foreach ($this->columns as $column) {
            if ($column != $this->columns[1]) {
                switch ($column) {
                    case 'status':
                        $selectedData[$column] = $row[$column] ? 'active' : 'inactive';
                        break;
                    case 'value':
                        // Format value based on discount type
                        if ($row->discount_type === 'fixed') {
                            $selectedData[$column] = Currency::format($row->discount_amount ?? 0);
                        } elseif ($row->discount_type === 'percent') {
                            $selectedData[$column] = $row->discount_percentage . '%';
                        } else {
                            // Handle other cases or set a default value
                            $selectedData[$column] = 'N/A';
                        }
                        break;
                    case 'is_expired':
                         $selectedData[$column] = $row[$column] === 1 ? 'Yes' : 'No';
                        break;
                        case 'used_by':
                            $userNames = $row->userRedeems->pluck('full_name');
                            $displayedNames = $userNames->take(2)->implode(', ');
                            if ($userNames->count() > 2) {
                                $displayedNames .= ', ...';
                            }
                            $selectedData[$column] = $displayedNames ?: '-';
                            break;
                    default:
                        $selectedData[$column] = $row[$column];
                        break;
                }
            }
        }

        return $selectedData;
    });

    return $newQuery;
}
public function styles(Worksheet $sheet)
{
    applyExcelStyles($sheet);
}
}
