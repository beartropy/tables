<?php

namespace Beartropy\Tables\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class GenericExport implements FromCollection, WithColumnWidths, WithHeadings, WithStyles
{
    /** @var array */
    protected $headers;

    /** @var array */
    protected $original_headers;

    /** @var string|null */
    protected $sheetName;

    /** @var \Illuminate\Support\Collection */
    protected $data;

    /** @var bool */
    protected $strip_tags;

    /**
     * Create a new GenericExport instance.
     *
     * @param  \Illuminate\Support\Collection  $data
     * @param  bool  $strip_tags
     * @param  string|null  $sheetName
     */
    public function __construct($data, $strip_tags = true, $sheetName = null)
    {
        $this->sheetName = $sheetName;
        // Clean data: remove _original keys to prevent duplicates in export
        $this->data = $data->map(function ($item) {
            return collect($item)->reject(function ($value, $key) {
                return str_ends_with($key, '_original');
            })->all();
        });
        $this->strip_tags = $strip_tags;
    }

    public function collection(): Collection
    {
        if ($this->strip_tags) {
            return $this->data->map(function ($item) {
                // Strip HTML tags from each value
                return collect($item)->map(fn ($value) => strip_tags((is_array($value) ? implode(', ', $value) : $value)));
            });
        } else {
            return $this->data;
        }
    }

    /**
     * Convert a 1-based column index to an Excel column name (e.g. 1=A, 27=AA).
     *
     * @param  int  $index
     * @return string
     */
    public function getExcelColumnName($index)
    {
        $columnName = '';
        while ($index > 0) {
            $mod = ($index - 1) % 26;
            $columnName = chr(65 + $mod).$columnName;
            $index = (int) (($index - $mod) / 26);
        }

        return $columnName;
    }

    public function headings(): array
    {
        $this->headers = $this->original_headers = $this->data->isNotEmpty() ? array_keys((array) $this->data->first()) : [];
        foreach ($this->headers as $key => $value) {
            $this->headers[$key] = ucfirst(str_replace('_', ' ', $value));
        }

        return $this->headers;
    }

    public function styles(Worksheet $sheet)
    {
        if (! empty($this->headers)) {
            $sheet->setAutoFilter('A1:'.$this->getExcelColumnName(count($this->headers)).'1');
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
                        $valueLength = strlen((string) strip_tags(is_array($item[$heading]) ? implode(', ', $item[$heading]) : $item[$heading]) ?? '');
                    } else {
                        $valueLength = strlen((string) $item[$heading] ?? '');
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
