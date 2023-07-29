<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use App\Filament\Resources\CustomerResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

class EditCustomer extends EditRecord
{
    protected static string $resource = CustomerResource::class;

    public $record = null;

    protected function getTitle(): string|Htmlable
    {
        return $this->record->first_name . ' ' . $this->record->last_name . '\'s Profile';
    }

    protected function getHeaderWidgetsColumns(): int|string|array
    {
        return 2;
    }

    protected function getHeaderWidgets(): array
    {
        return [
            CustomerResource\Widgets\CustomerProfileStats::class,
            CustomerResource\Widgets\CustomerProfileTopProducts::class,
            CustomerResource\Widgets\CustomerProfileTopType::class,
            CustomerResource\Widgets\RecentOrderOfCustomerTable::class
        ];
    }

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
