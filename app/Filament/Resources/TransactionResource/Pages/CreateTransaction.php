<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Filament\Resources\TransactionResource;
use App\Models\Customer;
use App\Models\Transaction;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateTransaction extends CreateRecord
{
    protected static string $resource = TransactionResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Transaction registered successfully')
            ->body('The transaction has been registered successfully');
    }

    protected function beforeCreate()
    {
        // Runs before the form fields are saved to the database.


    }
    
    protected function afterCreate()
    {
        // Runs after the form fields are saved to the database.
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {

         $transaction_type = $data['type'];
         $customer_id =  $data['customer_id'];
         $customer_details = Customer::find($customer_id);

         if($transaction_type == 'Credit'){
            //update the balance
            $customer_details->account_balance = $customer_details->account_balance + $data['amount'];
            $customer_details->save();
         }
         else{
            //deduct the balance
            $customer_details->account_balance = $customer_details->account_balance - $data['amount'];
            $customer_details->save();
         }
        $data['branch_id'] = Auth::user()->branch->id;
        $data['user_id'] = Auth::user()->id;
        $data['phone_number'] = $customer_details->phone;
        return $data;
    }

    protected function handleRecordCreation(array $data): Transaction
{
    return static::getModel()::create($data);
}

    
}
