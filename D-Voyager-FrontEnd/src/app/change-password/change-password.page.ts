import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { ToastController } from '@ionic/angular';
import { ApiService } from '../services/api.service';

@Component({
  selector: 'app-change-password',
  templateUrl: './change-password.page.html',
  styleUrls: ['./change-password.page.scss'],
  standalone: false
})
export class ChangePasswordPage implements OnInit {
  hasPassword = true;
  currentPassword = '';
  newPassword = '';
  confirmPassword = '';
  showCurrentPassword = false;
  showNewPassword = false;
  showConfirmPassword = false;
  isSaving = false;
  errorMessage = '';

  constructor(
    private api: ApiService,
    private toast: ToastController,
    private router: Router
  ) {}

  ngOnInit() {
    const userRaw = localStorage.getItem('user_data');
    if (userRaw) {
      try {
        const user = JSON.parse(userRaw);
        this.hasPassword = Boolean(user.has_password ?? true);
      } catch (e) {
        // ignore
      }
    }
  }

  passwordStrength(): { score: number; label: string } {
    const p = this.newPassword || '';
    let score = 0;

    if (p.length >= 8) score += 1;
    if (p.length >= 12) score += 1;
    if (/[A-Z]/.test(p)) score += 1;
    if (/[0-9]/.test(p)) score += 1;
    if (/[^A-Za-z0-9]/.test(p)) score += 1;

    let label = 'Lemah';
    if (score >= 4) label = 'Kuat';
    else if (score >= 3) label = 'Sedang';

    return { score, label };
  }

  canSubmit(): boolean {
    if (!this.newPassword || this.newPassword.length < 8) return false;
    if (this.newPassword !== this.confirmPassword) return false;
    if (this.hasPassword && !this.currentPassword) return false;
    return true;
  }

  async submit() {
    this.errorMessage = '';
    if (!this.canSubmit()) {
      this.errorMessage = 'Password baru minimal 8 karakter dan konfirmasinya harus sama.';
      return;
    }

    this.isSaving = true;

    this.api.updateUserPassword({
      current_password: this.hasPassword ? this.currentPassword : undefined,
      password: this.newPassword,
      password_confirmation: this.confirmPassword,
    }).subscribe({
      next: async () => {
        this.isSaving = false;
        const t = await this.toast.create({
          message: 'Password berhasil diperbarui.',
          duration: 1600,
          color: 'success'
        });
        await t.present();
        this.router.navigate(['/edit-profile']);
      },
      error: (err) => {
        this.isSaving = false;
        const validation = err?.error?.errors;
        if (validation) {
          const first = Object.keys(validation)[0];
          this.errorMessage = validation[first]?.[0] || 'Gagal memperbarui password.';
          return;
        }
        this.errorMessage = err?.error?.message || 'Gagal memperbarui password.';
      }
    });
  }
}
