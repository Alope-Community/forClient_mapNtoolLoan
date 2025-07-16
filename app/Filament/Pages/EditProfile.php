<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Facades\Filament;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class EditProfile extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $view = 'filament.pages.edit-profile';
    protected static bool $shouldRegisterNavigation = false;

    public ?array $profileData = [];
    public ?array $passwordData = [];

    public function mount(): void
    {
        $this->fillForms();
    }

    protected function getForms(): array
    {
        return ['editProfileForm', 'editPasswordForm'];
    }

    public function editProfileForm(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama')->label('Nama')->required(),
                Forms\Components\Radio::make('gender')
                    ->options([
                        'L' => 'Laki-laki',
                        'P' => 'Perempuan',
                    ])->required(),
                Forms\Components\TextInput::make('email')->label('Email')->email()->required(),
            ])
            ->model($this->getUser())
            ->statePath('profileData');
    }

    public function editPasswordForm(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('password')
                    ->label('Password Baru')
                    ->password()
                    ->required()
                    ->minLength(8)
                    ->dehydrateStateUsing(fn($state) => Hash::make($state)),
            ])
            ->statePath('passwordData');
    }

    public function updateProfile(): void
    {
        $this->getUser()->update($this->profileData);
        Notification::make()
            ->title('Profil berhasil diperbarui')
            ->success()
            ->send();
    }

    public function updatePassword(): void
    {
        $this->getUser()->update($this->passwordData);
        Notification::make()
            ->title('Password berhasil diperbarui')
            ->success()
            ->send();
    }

    protected function getUpdateProfileFormActions(): array
    {
        return [Action::make('save')->label('Simpan')->submit('updateProfile')];
    }

    protected function getUpdatePasswordFormActions(): array
    {
        return [Action::make('change')->label('Ganti Password')->submit('updatePassword')];
    }

    protected function getUser(): Authenticatable & Model
    {
        $user = Filament::auth()->user();
        if (! $user instanceof Model) throw new \Exception('User harus model Eloquent.');
        return $user;
    }

    protected function fillForms(): void
    {
        $data = $this->getUser()->only(['nama', 'gender', 'email']);
        $this->editProfileForm->fill($data);
    }
}
