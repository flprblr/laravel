<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class UsersImport implements SkipsEmptyRows, ToCollection, WithHeadingRow, WithValidation
{
    public function rules(): array
    {
        return [
            'id' => 'required|integer|min:1',
            'name' => 'required|string|min:2|max:255',
            'email' => 'required|email|max:255',
            'password' => 'nullable|string|min:6',
            'status' => 'nullable|boolean',
            'dni' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:20',
        ];
    }

    public function collection(Collection $rows)
    {
        DB::beginTransaction();

        try {
            foreach ($rows as $row) {
                User::updateOrCreate(
                    [
                        'id' => $row['id'],
                    ],
                    [
                        'name' => trim($row['name']),
                        'email' => strtolower(trim($row['email'])),
                        'password' => ! empty($row['password']) ? Hash::make($row['password']) : null,
                        'status' => $row['status'] ?? true,
                        'dni' => $row['dni'] ?? null,
                        'phone' => $row['phone'] ?? null,
                    ]
                );
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
