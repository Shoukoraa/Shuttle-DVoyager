import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { ToastController, LoadingController } from '@ionic/angular';
import { ApiService } from '../services/api.service';
import { catchError, firstValueFrom, of, timeout } from 'rxjs';

@Component({
  selector: 'app-edit-profile',
  templateUrl: './edit-profile.page.html',
  styleUrls: ['./edit-profile.page.scss'],
  standalone: false
})
export class EditProfilePage implements OnInit {
  name = '';
  email = '';
  phone = '';
  profilePhotoUrl = '';
  hasPassword = true;
  currentPassword = '';
  newPassword = '';
  confirmPassword = '';
  isLoading = true;
  isSaving = false;
  isUploadingPhoto = false;
  isSavingPassword = false;
  errorMessage = '';
  passwordErrorMessage = '';

  isDeletingAccount = false;
  isDeleteAccountModalOpen = false;

  constructor(
    private router: Router,
    private toastController: ToastController,
    private loadingController: LoadingController,
    private apiService: ApiService
  ) { }

  ngOnInit() {
    this.loadProfile();
  }

  loadProfile() {
    this.isLoading = true;
    this.errorMessage = '';

    const userDataRaw = localStorage.getItem('user_data');
    if (userDataRaw) {
      try {
        const userData = JSON.parse(userDataRaw);
        this.name = userData.name || '';
        this.email = userData.email || '';
        this.phone = userData.phone || '';
        this.profilePhotoUrl = userData.profile_photo_url || '';
      } catch (error) {
        console.error('Error parsing localStorage user_data:', error);
      }
    }

    this.apiService.getUserProfile().subscribe({
      next: (user) => {
        this.name = user.name || this.name;
        this.email = user.email || this.email;
        this.phone = user.phone || '';
        this.profilePhotoUrl = user.profile_photo_url || this.profilePhotoUrl;
        this.hasPassword = Boolean(user.has_password);
        localStorage.setItem('user_data', JSON.stringify({
          ...user,
          role: localStorage.getItem('user_role') || 'customer',
        }));
        this.isLoading = false;
      },
      error: (error) => {
        console.error('Failed to load profile:', error);
        this.isLoading = false;
      }
    });
  }

  uploadProfilePhoto(event: Event) {
    const input = event.target as HTMLInputElement;
    const file = input.files?.[0];

    if (!file) {
      return;
    }

    if (!file.type.startsWith('image/')) {
      this.errorMessage = 'File harus berupa gambar.';
      input.value = '';
      return;
    }

    if (file.size > 2 * 1024 * 1024) {
      this.errorMessage = 'Ukuran foto maksimal 2 MB.';
      input.value = '';
      return;
    }

    this.errorMessage = '';
    this.isUploadingPhoto = true;

    this.apiService.updateUserProfilePhoto(file).subscribe({
      next: async (response) => {
        const updatedUser = response?.user || response;
        this.profilePhotoUrl = updatedUser.profile_photo_url || this.profilePhotoUrl;
        this.isUploadingPhoto = false;
        input.value = '';
        await this.showToast('Foto profil berhasil diperbarui.');
      },
      error: (error) => {
        this.isUploadingPhoto = false;
        input.value = '';
        const validationErrors = error?.error?.errors;

        if (validationErrors) {
          const firstErrorKey = Object.keys(validationErrors)[0];
          this.errorMessage = validationErrors[firstErrorKey]?.[0] || 'Gagal mengunggah foto profil.';
          return;
        }

        this.errorMessage = error?.error?.message || 'Gagal mengunggah foto profil.';
      }
    });
  }

  saveChanges() {
    this.errorMessage = '';
    this.passwordErrorMessage = '';

    const trimmedName = this.name.trim();
    const trimmedPhone = this.phone.trim();
    const wantsToChangePassword = Boolean(this.currentPassword || this.newPassword || this.confirmPassword);

    if (!trimmedName) {
      this.errorMessage = 'Nama wajib diisi.';
      return;
    }

    if (trimmedName.length > 50) {
      this.errorMessage = 'Nama lengkap maksimal 50 karakter.';
      return;
    }

    if (trimmedPhone && !/^[0-9+\-\s()]{8,20}$/.test(trimmedPhone)) {
      this.errorMessage = 'Nomor telepon harus 8-20 karakter dan hanya berisi angka atau simbol telepon.';
      return;
    }

    if (wantsToChangePassword && !this.isPasswordFormValid()) {
      return;
    }

    this.isSaving = true;

    this.apiService.updateUserProfile({
      name: trimmedName,
      phone: trimmedPhone,
    }).subscribe({
      next: () => {
        if (wantsToChangePassword) {
          this.updatePasswordAfterProfile();
          return;
        }

        this.finishSave('Profil berhasil diperbarui.');
      },
      error: (error) => {
        this.isSaving = false;
        const validationErrors = error?.error?.errors;

        if (validationErrors) {
          const firstErrorKey = Object.keys(validationErrors)[0];
          this.errorMessage = validationErrors[firstErrorKey]?.[0] || 'Gagal menyimpan profil.';
          return;
        }

        this.errorMessage = error?.error?.message || 'Gagal menyimpan profil.';
      }
    });
  }

  private isPasswordFormValid(): boolean {
    if (this.hasPassword && !this.currentPassword) {
      this.passwordErrorMessage = 'Password lama wajib diisi.';
      return false;
    }

    if (!this.newPassword || this.newPassword.length < 6) {
      this.passwordErrorMessage = 'Password baru minimal 6 karakter.';
      return false;
    }

    if (this.newPassword !== this.confirmPassword) {
      this.passwordErrorMessage = 'Konfirmasi password tidak sama.';
      return false;
    }

    return true;
  }

  private updatePasswordAfterProfile() {
    this.isSavingPassword = true;

    this.apiService.updateUserPassword({
      current_password: this.currentPassword,
      password: this.newPassword,
      password_confirmation: this.confirmPassword,
    }).subscribe({
      next: () => {
        this.hasPassword = true;
        this.currentPassword = '';
        this.newPassword = '';
        this.confirmPassword = '';
        this.finishSave('Profil dan password berhasil diperbarui.');
      },
      error: (error) => {
        this.isSaving = false;
        this.isSavingPassword = false;
        const validationErrors = error?.error?.errors;

        if (validationErrors) {
          const firstErrorKey = Object.keys(validationErrors)[0];
          this.passwordErrorMessage = validationErrors[firstErrorKey]?.[0] || 'Gagal menyimpan password.';
          return;
        }

        this.passwordErrorMessage = error?.error?.message || 'Gagal menyimpan password.';
      }
    });
  }

  private async finishSave(message: string) {
    this.isSaving = false;
    this.isSavingPassword = false;
    await this.showToast(message);
    // Redirection removed as per user request to stay on page after save
  }

  private async showToast(message: string) {
    const toast = await this.toastController.create({
      message,
      duration: 1100,
      position: 'top',
      cssClass: 'premium-toast toast-success',
      icon: 'checkmark-circle',
    });

    await toast.present();
  }

  openDeleteAccountModal() {
    if (this.isDeletingAccount || this.isSaving || this.isSavingPassword) {
      return;
    }
    this.isDeleteAccountModalOpen = true;
  }

  confirmDeleteAccount() {
    this.isDeleteAccountModalOpen = false;
    this.performDeleteAccount();
  }

  private async performDeleteAccount() {
    this.isDeletingAccount = true;

    const loading = await this.loadingController.create({
      message: 'Menghapus akun...',
      spinner: 'crescent',
    });

    await loading.present();

    await firstValueFrom(
      this.apiService.deleteAccount().pipe(
        timeout({ first: 5000 }),
        catchError((error) => {
          console.error('Delete Account API error:', error);
          return of(null);
        })
      )
    );

    await loading.dismiss();
    this.isDeletingAccount = false;

    // Hapus sisa data Auth
    localStorage.removeItem('auth_token');
    localStorage.removeItem('user_data');
    localStorage.removeItem('user_role');

    // Redirect ke landing page
    this.router.navigate(['/landing'], { replaceUrl: true });
  }
}
