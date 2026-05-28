import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { ApiService } from '../services/api.service';

@Component({
  selector: 'app-signup',
  templateUrl: './signup.page.html',
  styleUrls: ['./signup.page.scss'],
  standalone: false
})
export class SignupPage implements OnInit {
  fullName = '';
  email = '';
  phone = '';
  password = '';
  confirmPassword = '';
  showPassword = false;
  showConfirmPassword = false;
  isLoading = false;
  errorMessage = '';

  constructor(
    private router: Router,
    private apiService: ApiService
  ) { }

  ngOnInit() {
  }

  signup() {
    this.errorMessage = '';

    if (!this.fullName || !this.email || !this.phone || !this.password || !this.confirmPassword) {
      this.errorMessage = 'Semua field wajib diisi.';
      return;
    }

    if (this.fullName.trim().length > 50) {
      this.errorMessage = 'Nama lengkap maksimal 50 karakter.';
      return;
    }

    if (this.password !== this.confirmPassword) {
      this.errorMessage = 'Password dan konfirmasi password harus sama.';
      return;
    }

    if (this.password.length < 8) {
      this.errorMessage = 'Password minimal 8 karakter.';
      return;
    }

    this.isLoading = true;

    const payload = {
      name: this.fullName.trim(),
      email: this.email.trim(),
      phone: this.phone.trim(),
      password: this.password,
      password_confirmation: this.confirmPassword,
    };

    this.apiService.register(payload).subscribe({
      next: (response) => {
        this.isLoading = false;

        // Backend now sends OTP on register. Navigate to OTP input page.
        this.router.navigate(['/otp'], { queryParams: { email: this.email } });
      },
      error: (error) => {
        this.isLoading = false;

        const backendMessage = error?.error?.message;
        const validationErrors = error?.error?.errors;

        if (validationErrors) {
          const firstErrorKey = Object.keys(validationErrors)[0];
          this.errorMessage = validationErrors[firstErrorKey]?.[0] || 'Terjadi kesalahan saat sign up.';
          return;
        }

        this.errorMessage = backendMessage || 'Terjadi kesalahan saat sign up.';
      }
    });
  }
}
