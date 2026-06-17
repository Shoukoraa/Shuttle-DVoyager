import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { ToastController } from '@ionic/angular';
import { ApiService } from '../services/api.service';

@Component({
  selector: 'app-forgot-password',
  templateUrl: './forgot-password.page.html',
  styleUrls: ['./forgot-password.page.scss'],
  standalone: false
})
export class ForgotPasswordPage implements OnInit {
  email = '';
  otp = '';
  password = '';
  confirmPassword = '';
  showNewPassword = false;
  showConfirmPassword = false;
  isCodeSent = false;
  isLoading = false;
  errorMessage = '';
  successMessage = '';

  constructor(
    private apiService: ApiService,
    private router: Router,
    private toastController: ToastController
  ) { }

  ngOnInit() {
  }

  sendResetCode() {
    this.errorMessage = '';
    this.successMessage = '';

    if (!this.email.trim()) {
      this.errorMessage = 'Email wajib diisi.';
      return;
    }

    this.isLoading = true;

    this.apiService.requestPasswordReset({ email: this.email.trim() }).subscribe({
      next: (response) => {
        this.isLoading = false;
        this.isCodeSent = true;
        this.successMessage = response?.message || 'Kode reset sudah dikirim ke email.';
      },
      error: (error) => {
        this.isLoading = false;
        this.errorMessage = this.readErrorMessage(error, 'Gagal mengirim kode reset.');
      }
    });
  }

  resetPassword() {
    this.errorMessage = '';
    this.successMessage = '';

    if (!this.otp.trim() || !this.password || !this.confirmPassword) {
      this.errorMessage = 'Kode OTP dan password baru wajib diisi.';
      return;
    }

    if (this.password.length < 8) {
      this.errorMessage = 'Password baru minimal 8 karakter.';
      return;
    }

    if (this.password !== this.confirmPassword) {
      this.errorMessage = 'Password dan konfirmasi password harus sama.';
      return;
    }

    this.isLoading = true;

    this.apiService.resetPassword({
      email: this.email.trim(),
      otp: this.otp.trim(),
      password: this.password,
      password_confirmation: this.confirmPassword,
    }).subscribe({
      next: async (response) => {
        this.isLoading = false;
        const toast = await this.toastController.create({
          message: response?.message || 'Password berhasil direset. Silakan login.',
          duration: 1100,
          cssClass: 'premium-toast toast-success',
          position: 'top',
          icon: 'checkmark-circle'
        });
        await toast.present();
        this.router.navigate(['/login']);
      },
      error: (error) => {
        this.isLoading = false;
        this.errorMessage = this.readErrorMessage(error, 'Gagal reset password.');
      }
    });
  }

  private readErrorMessage(error: any, fallback: string): string {
    const validationErrors = error?.error?.errors;

    if (validationErrors) {
      const firstErrorKey = Object.keys(validationErrors)[0];
      return validationErrors[firstErrorKey]?.[0] || fallback;
    }

    return error?.error?.message || fallback;
  }
}
