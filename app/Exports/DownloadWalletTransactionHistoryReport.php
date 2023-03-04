<?php 

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DownloadWalletTransactionHistoryReport implements FromView, WithColumnWidths, WithStyles
{
  private $data;

  public function __construct($data)
  {
    $this->data = $data;
  }

  /**
  * @return \Illuminate\Support\Collection
  */
  public function view(): View
  {
    return view('backend.reports.excel.wallet_history_report', [
      'wallets' => $this->data
    ]);
  }

  public function columnWidths(): array
  {
    return [
      'A' => 10,
      'B' => 25,      
      'C' => 15,            
      'D' => 15,
      'E' => 15,
      'F' => 20,
    ];
  }

  public function styles(Worksheet $sheet)
  {
    return [
      // Style the first row as bold text.
      1  => ['font' => ['bold' => true]],
    ];
  }
}