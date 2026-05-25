import { Component, OnDestroy, OnInit } from '@angular/core';
import { Router, ActivatedRoute } from '@angular/router';
import { ApiService } from '../services/api.service';

@Component({
  selector: 'app-otp',
  templateUrl: './otp.page.html',
  styleUrls: ['./otp.page.scss'],
  standalone: false
})
export class OtpPage implements OnInit {
  email = '';
  otp = '';
  isLoading = false;
  errorMessage = '';
  resendCooldownSeconds = 60;
  private resendTimer: ReturnType<typeof setInterval> | null = null;

  constructor(
    private router: Router,
    private route: ActivatedRoute,
    private api: ApiService
  ) {}

  ngOnInit() {
    this.route.queryParams.subscribe(params => {
      if (params && params['email']) {
        this.email = params['email'];
      }
    });

    // Register flow already sends OTP, so hold resend for 60 seconds.
    this.startResendCooldown(60);
  }

  ngOnDestroy(): void {
    if (this.resendTimer) {
      clearInterval(this.resendTimer);
      this.resendTimer = null;
    }
  }

  verify() {
    this.errorMessage = '';
    if (!this.otp || this.otp.length !== 6) {
      this.errorMessage = 'Masukkan kode 6 digit.';
      return;
    }

    this.isLoading = true;
    this.api.verifyOtp({ email: this.email, otp: this.otp }).subscribe({
      next: (res: any) => {
        this.isLoading = false;
        // token stored by ApiService
        this.router.navigate(['/home'], { replaceUrl: true });
      },
      error: (err) => {
        this.isLoading = false;
        this.errorMessage = err?.error?.message || 'OTP salah atau kadaluarsa.';
      }
    });
  }

  resend() {
    this.errorMessage = '';
    if (this.resendCooldownSeconds > 0) {
      return;
    }

    this.api.requestOtp({ email: this.email }).subscribe({
      next: (res: any) => {
        const delay = Number(res?.resend_delay_seconds ?? 60);
        this.startResendCooldown(delay);
      },
      error: (err) => {
        if (err?.status === 429) {
          const retryAfter = Number(err?.error?.retry_after_seconds ?? 60);
          this.startResendCooldown(retryAfter);
        }
        this.errorMessage = err?.error?.message || 'Gagal mengirim ulang OTP.';
      }
    });
  }

  private startResendCooldown(seconds: number): void {
    const safeSeconds = Number.isFinite(seconds) ? Math.max(0, Math.floor(seconds)) : 60;
    this.resendCooldownSeconds = safeSeconds;

    if (this.resendTimer) {
      clearInterval(this.resendTimer);
      this.resendTimer = null;
    }

    if (this.resendCooldownSeconds === 0) {
      return;
    }

    this.resendTimer = setInterval(() => {
      if (this.resendCooldownSeconds <= 1) {
        this.resendCooldownSeconds = 0;
        if (this.resendTimer) {
          clearInterval(this.resendTimer);
          this.resendTimer = null;
        }
        return;
      }

      this.resendCooldownSeconds -= 1;
    }, 1000);
  }
}
