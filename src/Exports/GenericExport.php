<?php

namespace Beartropy\Tables\Exports;

use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class GenericExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths
{

    protected $headers;
    protected $original_headers;
    protected $sheetName;
    protected $data;
    protected $strip_tags;

    public function __construct($data,$strip_tags = true, $sheetName = null)
    {
        $this->sheetName = $sheetName;
        $this->data = $data;
        $this->strip_tags = $strip_tags;
    }

    public function collection(): Collection
    {
        if ($this->strip_tags) {
            return $this->data->map(function ($item) {
                // Strip HTML tags from each value
                return collect($item)->map(fn($value) => strip_tags((is_array($value) ? implode(', ', $value) : $value)));
            });
        } else {
            return $this->data;
        }
    }

    function getExcelColumnName($index)
    {
        $columnName = '';
        while ($index > 0) {
            $mod = ($index - 1) % 26;
            $columnName = chr(65 + $mod) . $columnName;
            $index = (int)(($index - $mod) / 26);
        }
        return $columnName;
    }

    public function headings(): array
    {
        $this->headers = $this->original_headers = $this->data->isNotEmpty() ? array_keys((array)$this->data->first()) : [];
        foreach ($this->headers as $key => $value) {
            $this->headers[$key] = ucfirst(str_replace('_',' ',$value));
        }
        return $this->headers;
    }

    public function styles(Worksheet $sheet)
    {
        if (!empty($this->headers)){
            $sheet->setAutoFilter('A1:'.$this->getExcelColumnName(count($this->headers)).'1');
        }

        if ($this->sheetName) {
            #$sheet->setTitle($this->sheetName);
        }

        return [
            1 => [ // Apply styles to the first row (headers)
                'font' => [
                    'bold' => true,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'color' => ['argb' => 'FFCCCCCC'], // Light gray background
                ],
            ],
        ];
    }

    public function columnWidths(): array
    {
        $widths = [];
        if ($this->data->isNotEmpty()) {
            // Get headings
            $headings = $this->original_headers;

            // Set widths based on headings and longest values
            foreach ($headings as $index => $heading) {
                $maxLength = strlen($heading); // Start with the length of the heading

                // Check each row to find the maximum length of the data in this column
                foreach ($this->data as $item) {
                    if ($this->strip_tags) {
                        $valueLength = strlen((string)strip_tags(is_array($item[$heading]) ? implode(', ', $item[$heading]) : $item[$heading]) ?? '');
                    } else {
                        $valueLength = strlen((string)$item[$heading] ?? '');
                    }
                    if ($valueLength > $maxLength) {
                        $maxLength = $valueLength;
                    }
                }

                $widths[$this->getExcelColumnName($index + 1)] = $maxLength + 2; // Add a little padding
            }
        }
        return $widths;
    }
}
