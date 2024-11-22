<?php

namespace App\Imports;

use App\BankMovement;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Str;

class BankMovementsBcpImport implements ToModel, WithMultipleSheets, WithStartRow
{
    use Importable;

    public function model(array $row)
    {
        $date = Carbon::createFromFormat('d/m/Y', $row[0])->format('Y-m-d');

        $existsMovement = BankMovement::where('operation_number', trim($row[5]))
                                        ->where('client_id', auth()->user()->headquarter->client_id)
                                        ->where('bank', 'BCP')
                                        ->where('date', $date)
                                        ->first();

        if ($existsMovement == null) {
            $movement = new BankMovement;
            $movement->date = $date;
            $movement->bank = "BCP";
            $movement->amount = abs($row[3]);
            $movement->operation_number = trim($row[5]);
            $movement->description = Str::upper(trim($row[2]));
            $movement->movement_type = $row[3] < 0 ? 'CARGO' : 'ABONO';
            $movement->client_id = auth()->user()->headquarter->client_id;
            $movement->save();

            return $movement;
        }

        return $existsMovement;
    }

    public function startRow(): int
    {
        return 9;
    }

    public function sheets(): array
    {
        return [
            0 => $this,
        ];
    }
}
