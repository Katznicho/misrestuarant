<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Filament\Resources\CustomerResource\RelationManagers;
use App\Models\Card;
use App\Models\Customer;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\Indicator;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'Customers';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('card_id')
                    ->options(Card::all()->pluck('card_number', 'id'))
                    ->searchable()
                    ->required()
                    ->preload()
                    ->label("Card Number"),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->label("Customer Name")
                    ->placeholder('enter customer name')
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->label("Email Address")
                    ->placeholder('enter email address')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone')
                    ->tel()
                    ->label("Phone Number")
                    ->placeholder('enter phone number')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('account_balance')
                    ->required()
                    ->label("Account Balance")
                    ->placeholder('enter account balance')
                    ->maxLength(255)
                    ->default(0),
                Forms\Components\TextInput::make('minimum_deposit_amount')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('enter minimum deposit amount')
                    ->default(500),
                // Forms\Components\TextInput::make('gender')
                //make gender select field
                Select::make("gender")
                    ->options([
                        "Male" => "Male",
                        "Female" => "Female",
                        "Other" => "Other",
                    ])
                    ->required()
                    ->label("Gender"),
                Forms\Components\TextInput::make('address')

                    ->label("Address")
                    ->placeholder('enter address')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('card.card_number')
                    ->sortable()
                    ->searchable()
                    ->toggleable()
                    ->copyable()
                    ->copyMessage('Card ID copied!')
                    ->label('Card Number'),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->copyable()
                    ->copyMessage('Name copied!')
                    ->label('Name'),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->copyable()
                    ->copyMessage('Email copied!')
                    ->label('Email'),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->copyable()
                    ->copyMessage('Phone copied!')
                    ->label('Phone'),
                Tables\Columns\TextColumn::make('account_balance')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->copyable()
                    ->copyMessage('Account Balance copied!')
                    ->label('Account Balance'),
                Tables\Columns\TextColumn::make('minimum_deposit_amount')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->copyable()
                    ->copyMessage('Account Balance copied!')
                    ->label('Minimum Deposit Amount'),
                Tables\Columns\TextColumn::make('gender')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->copyable()
                    ->copyMessage('Gender copied!')
                    ->label('Gender'),
                Tables\Columns\TextColumn::make('address')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->copyable()
                    ->copyMessage('Address copied!')
                    ->label('Address'),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Deleted At'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->label('Created At'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->label("Updated At"),
            ])
            ->filters([
                //Tables\Filters\TrashedFilter::make(),
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from'),
                        DatePicker::make('created_until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['from'] ?? null) {
                            $indicators[] = Indicator::make('Created from ' . Carbon::parse($data['from'])->toFormattedDateString())
                                ->removeField('from');
                        }

                        if ($data['until'] ?? null) {
                            $indicators[] = Indicator::make('Created until ' . Carbon::parse($data['until'])->toFormattedDateString())
                                ->removeField('until');
                        }

                        return $indicators;
                    })
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                    // Tables\Actions\ForceDeleteBulkAction::make(),
                    // Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
            RelationManagers\TransactionsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'view' => Pages\ViewCustomer::route('/{record}'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
