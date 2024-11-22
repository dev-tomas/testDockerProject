<?php

namespace App\Imports;

use App\BankMovement;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Str;

class BankMovementsInterbankImport implements ToModel, WithMultipleSheets, WithStartRow
{
    use Importable;

    public function model(array $row)
    {
        if ($row[1] == null) {
            return null;
        }

        $date = Carbon::createFromFormat('d/m/Y', $row[1])->format('Y-m-d');

        $existsMovement = BankMovement::where('operation_number', trim($row[3]))
                                        ->where('client_id', auth()->user()->headquarter->client_id)
                                        ->where('bank', 'INTERBANK')
                                        ->where('date', $date)
                                        ->first();

        if ($existsMovement == null) {
            $movement = new BankMovement;
            $movement->date = $date;
            $movement->bank = "INTERBANK";
            $movement->amount = $row[7] != "" ? abs($row[7]) : abs($row[8]);
            $movement->operation_number = trim($row[3]);
            $movement->description = Str::upper(trim($row[4]));
            $movement->movement_type = $row[7] != "" ? "CARGO" : 'ABONO';
            $movement->client_id = auth()->user()->headquarter->client_id;
            $movement->save();

            return $movement;
        }

        return $existsMovement;
    }

    public function startRow(): int
    {
        return 13;
    }

    public function sheets(): array
    {
        return [
            0 => $this,
        ];
    }
}
