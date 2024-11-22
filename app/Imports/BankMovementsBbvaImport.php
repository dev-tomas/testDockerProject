<?php

namespace App\Imports;

use App\BankMovement;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Str;


class BankMovementsBbvaImport implements ToModel, WithStartRow
{
    use Importable;

    public function model(array $row)
    {
        if (trim($row[0]) == "") {
            return null;
        }

        $date = Carbon::createFromFormat('d-m', trim($row[0]))->format('Y-m-d');
        $itfValue = trim($row[7]);

        $existsMovement = BankMovement::where('operation_number', trim($row[5]))
                                        ->where('client_id', auth()->user()->headquarter->client_id)
                                        ->where('bank', 'BBVA')
                                        ->where('date', $date)
                                        ->first();

        if ($existsMovement == null) {
            $movement = new BankMovement;
            $movement->date = $date;
            $movement->bank = "BBVA";
            $movement->amount = abs(trim($row[6]));
            $movement->operation_number = trim($row[5]);
            $movement->description = Str::upper(trim($row[2]));
            $movement->movement_type = $row[3] < 0 ? 'CARGO' : 'ABONO';
            $movement->client_id = auth()->user()->headquarter->client_id;
            $movement->save();

            if ($itfValue != '') {
                $movement = new BankMovement;
                $movement->date = $date;
                $movement->bank = "BBVA";
                $movement->amount = abs($itfValue);
                $movement->operation_number = (int) trim($row[5]) + 1;
                $movement->description = "ITF";
                $movement->movement_type = 'CARGO';
                $movement->client_id = auth()->user()->headquarter->client_id;
                $movement->save();
            }

            return $movement;
        }

        return $existsMovement;
    }

    public function startRow(): int
    {
        return 5;
    }
}
