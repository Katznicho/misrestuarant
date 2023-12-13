<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use App\Filament\Resources\CustomerResource;
use App\Models\Card;
use App\Traits\MessageTrait;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;

class CreateCustomer extends CreateRecord
{
    use MessageTrait;
    protected static string $resource = CustomerResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Customer registered successfully')
            ->body('The customer has been registered successfully');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        //create a 4 digit pin
        $pin =  rand(1000, 9999);
        $hashed_pin = Hash::make($pin);

        $phone =  $this->formatMobileInternational($data['phone']);
        $data['phone'] = $phone;


        $customer_name  =  $data['name'];
        $app_name =  env('APP_NAME');
        $playStoreUrl = "https://play.google.com/store/apps/details?id=res";
        $card_id =  $data["card_id"];
        $card_number =  Card::find($card_id)->card_number;
        $message  =  "Hello $customer_name, Your Account has been created on $app_name. You have been assigned a card number $card_number. Your PIN is one  time  pin $pin. Download the app from $playStoreUrl to access your account directly on your phone.Thank you";
        $this->sendMessage($data['phone'], $message);
        $data['pin'] = $hashed_pin;

        return $data;
    }
}
