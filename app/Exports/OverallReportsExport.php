<?php

namespace App\Exports;

use Currency;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Modules\Booking\Models\Booking;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
class OverallReportsExport implements FromCollection, WithHeadings,WithStyles
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
        $query = Booking::overallReport();

        $query->whereDate('bookings.start_date_time', '>=', $this->dateRange[0]);

        $query->whereDate('bookings.start_date_time', '<=', $this->dateRange[1]);

        $query = $query->get();

        $newQuery = $query->map(function ($row) {
            $selectedData = [];

            foreach ($this->columns as $column) {
                switch ($column) {
                    case 'date':
                        $selectedData[$column] = customDate($row->start_date_time);
                        break;

                    case 'inv_id':
                        $selectedData[$column] = setting('booking_invoice_prifix').$row->id;
                        break;

                    case 'employee':
                        $selectedData[$column] = $row->services->first()->employee?->full_name ?? '-';
                        break;

                    case 'total_service_amount':
                        $selectedData[$column] = Currency::format($row->total_service_amount ?? 0);
                        break;

                    case 'total_tax_amount':
                        $selectedData[$column] = Currency::format($row->total_tax_amount ?? 0);
                        break;

                    case 'total_tip_amount':
                        $selectedData[$column] = Currency::format($row->total_tip_amount ?? 0);
                        break;

                    case 'total_amount':
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
