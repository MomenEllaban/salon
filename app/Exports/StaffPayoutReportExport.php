<?php

namespace App\Exports;

use Currency;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Modules\Earning\Models\EmployeeEarning;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
class StaffPayoutReportExport implements FromCollection, WithHeadings,  WithStyles
{
    public array $columns;

    public array $dateRange;

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
            $modifiedHeadings[] = ucwords(str_replace('_', ' ', $column));
        }

        return $modifiedHeadings;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $query = EmployeeEarning::with('employee');

        $query->whereDate('payment_date', '>=', $this->dateRange[0]);

        $query->whereDate('payment_date', '<=', $this->dateRange[1]);

        $query = $query->get();

        $newQuery = $query->map(function ($row) {
            $selectedData = [];

            foreach ($this->columns as $column) {
                switch ($column) {
                    case 'date':
                        $selectedData[$column] = customDate($row->payment_date);
                        break;

                    case 'employee':
                        $selectedData[$column] = $row->employee->full_name ?? '-';
                        break;

                    case 'commission_amount':
                        $selectedData[$column] = Currency::format($row->commission_amount ?? 0);
                        break;

                    case 'tip_amount':
                        $selectedData[$column] = Currency::format($row->tip_amount ?? 0);
                        break;

                    case 'total_pay':
                        $selectedData[$column] = Currency::format($row->total_amount ?? 0);
                        break;

                    default:
                        $selectedData[$column] = $row[$column];
                        break;
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
