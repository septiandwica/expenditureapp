<?php

namespace App\Filament\Resources\SupplierResource\Pages;

use App\Filament\Resources\SupplierResource;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSupplier extends CreateRecord
{
    protected static string $resource = SupplierResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
{
    // Generate user untuk supplier
    $user = User::create([
        'name' => $data['name'], // atau bisa pakai field 'pic_name' kalau ada
        'email' => $data['email'], // pastikan kamu punya field email supplier
        'password' => bcrypt('12345678'), // password default
    ]);

    $user->assignRole('supplier'); // jika kamu pakai Spatie Laravel-permission

    $data['user_id'] = $user->id;

    return $data;
}
}
