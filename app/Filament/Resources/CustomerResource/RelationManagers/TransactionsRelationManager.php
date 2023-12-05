<?php

namespace App\Filament\Resources\CustomerResource\RelationManagers;

use App\Models\Transaction;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\Indicator;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Filters\SelectFilter;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class TransactionsRelationManager extends RelationManager
{
    protected static string $relationship = 'transactions';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('customer_id')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('customer_id')
            ->columns([
                // Tables\Columns\TextColumn::make('customer_id'),
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
                ->label('Status'),

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
                ->label('User Name'),
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
                ->label('Amount'),
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
                
            ])
            ->filters([
                //
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
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
                ExportBulkAction::make()
            ]);
    }
}
