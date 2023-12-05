<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Filament\Resources\TransactionResource\RelationManagers;
use App\Models\Transaction;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\Indicator;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
// use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;


class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-s-arrow-path';

    protected static ?string $navigationGroup = 'Transactions';

    public static function form(Form $form): Form
    {
        $branch_name = Auth::user()->branch->name;
        // dd($user);
        return $form
            ->schema([
                Forms\Components\Select::make('customer_id')
                    ->relationship('customer', 'name')
                    ->required(),
                Forms\Components\TextInput::make('branch_id')
                    ->required()
                    ->disabled()
                    ->default(Auth::user()->branch->name)
                    ->label("Branch Name")
                    ,
                Forms\Components\TextInput::make('user_id')
                    ->required()
                    ->disabled()
                    ->default(Auth::user()->name)
                    ->label("User Name")
                    ,
                    Forms\Components\Select::make('type')
                    ->options([
                        'Debit' => 'Debit',
                        'Credit' => 'Credit',
                    ])
                    ->required()
                    ->label("Type")
                    ,
                    
                Forms\Components\TextInput::make('amount')
                    ->required()
                    ->numeric()
                    ->maxLength(255)
                    ->label("Amount")
                    ,
                    Forms\Components\Select::make('payment_mode')
                    ->options([
                        'Cash' => 'Cash',
                        'Card' => 'Card',
                        'Cheque' => 'Cheque',
                        'Bank Transfer' => 'Bank Transfer',
                        'Mobile Money' => 'Mobile Money',
                        'Other' => 'Other',

                    ])
                    ->required()
                    ->label("Payment Mode")
                    ,
                    //add status is a dropdown with complete
                    Forms\Components\Select::make('status')
                    ->options([
                        'Completed' => 'Completed',
                        'Pending' => 'Pending',
                        'Cancelled' => 'Cancelled',
                    ])
                    ->required()
                    ->columnSpanFull()
                    ->label("Status")
                    ,

                Forms\Components\Textarea::make('description')
                    ->required()
                    ->maxLength(65535)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('reference')
                    ->required()
                    ->maxLength(255)
                    ->unique()
                    ->label("Reference")
                    ->columnSpanFull()
                    ,
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('customer.name')
                    ->numeric()
                    ->sortable()
                    ->searchable()
                    ->toggleable()
                    ->copyable()
                    ->copyMessage('Name copied!')
                    ->label('Name')
                    ,
                Tables\Columns\TextColumn::make('status')
                    ->numeric()
                    ->sortable()
                    ->searchable()
                    ->toggleable()
                    ->copyable()
                    ->copyMessage('Status')
                    ->label('Status')
                    ,
                Tables\Columns\TextColumn::make('branch.name')
                    ->numeric()
                    ->sortable()
                    ->searchable()
                    ->toggleable()
                    ->copyable()
                    ->copyMessage('Branch ID copied!')
                    ->label('Branch Name')
                    ,
                Tables\Columns\TextColumn::make('user.name')
                    ->numeric()
                    ->sortable()
                    ->searchable()
                    ->toggleable()
                    ->copyable()
                    ->copyMessage('User ID copied!')
                    ->label('User Name')
                    ,
                Tables\Columns\TextColumn::make('type')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->copyable()
                    ->copyMessage('Type copied!')
                    ->label('Type')
                    ->color(
                        fn (Transaction $record): string => match ($record->type) {
                            'Debit' => 'danger',
                            'Credit' => 'success',
                        }
                    )
                    ,
                Tables\Columns\TextColumn::make('amount')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->copyable()
                    ->copyMessage('Amount copied!')
                    ->label('Amount')
                    ,
                Tables\Columns\TextColumn::make('phone_number')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->copyable()
                    ->copyMessage('Phone Number copied!')
                    ->label('Phone Number')
                    ,
                Tables\Columns\TextColumn::make('payment_mode')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->copyable()
                    ->copyMessage('Payment Mode copied!')
                    ->label('Payment Mode')
                    ,
                Tables\Columns\TextColumn::make('reference')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->copyable()
                    ->copyMessage('Reference copied!')
                    ->label('Reference')
                    ,
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->copyable()
                    ->copyMessage('Deleted At copied!')
                    ->label('Deleted At')
                    ,
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->copyable()
                    ->copyMessage('Created At copied!')
                    ->label('Created At')
                    ,
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->copyable()
                    ->copyMessage('Updated At copied!')
                    ->label('Updated At')
                    ,
            ])
            ->filters([
                //Tables\Filters\TrashedFilter::make(),
                SelectFilter::make('status')
                ->options([
                    "pending" => "Pending",
                    "completed" => "Completed",
                    "failed" => "Failed",

                ])
                ->label('Status'),
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
            ->headerActions([

                ExportBulkAction::make(),


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
                ExportBulkAction::make()
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransaction::route('/create'),
            'view' => Pages\ViewTransaction::route('/{record}'),
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
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
