<?php

namespace App\Filament\Resources\BibleStudyGroups\RelationManagers;

use App\Models\Member;
use Filament\Actions\Action;
use Filament\Forms\Components;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class MembersRelationManager extends RelationManager
{
    protected static string $relationship = 'members';

    protected static bool $isLazy = false;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Components\Select::make('member_id')
                    ->label('Member')
                    ->options(Member::query()->where('is_active', true)->get()->pluck('full_name', 'id'))
                    ->searchable()
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('profile_photo')->circular()->size(40),
                Tables\Columns\TextColumn::make('full_name')->searchable(),
                Tables\Columns\TextColumn::make('phone'),
                Tables\Columns\TextColumn::make('gender'),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('gender'),
            ])
            ->headerActions([
                Action::make('export_csv')
                    ->label('Export CSV')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function () {
                        $records = $this->getRelationship()->get();
                        $filename = 'bible_study_group_members_'.date('Y-m-d_His').'.csv';

                        $headers = ['Name', 'Phone', 'Gender', 'Is Active'];
                        $rows = $records->map(fn ($r) => [
                            $r->full_name ?? 'N/A',
                            $r->phone ?? 'N/A',
                            $r->gender?->getLabel() ?? 'N/A',
                            $r->is_active ? 'Yes' : 'No',
                        ])->toArray();

                        array_unshift($rows, $headers);

                        return response()->stream(function () use ($rows) {
                            $file = fopen('php://output', 'w');
                            foreach ($rows as $row) {
                                fputcsv($file, $row);
                            }
                            fclose($file);
                        }, 200, [
                            'Content-Type' => 'text/csv',
                            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
                        ]);
                    }),
            ]);
    }
}
