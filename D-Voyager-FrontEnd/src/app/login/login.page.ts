import { Component, OnInit } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { Router } from '@angular/router';
import { Location } from '@angular/common';
import { ApiService } from '../services/api.service';

@Component({
  selector: 'app-login',
  templateUrl: './login.page.html',
  styleUrls: ['./login.page.scss'],
  standalone: false
})
export class LoginPage implements OnInit {
  public email: string = '';
  public password: string = '';
  public showPassword: boolean = false;
  public isLoading: boolean = false;
  public errorMessage: string = '';

  constructor(
    private router: Router,
    private route: ActivatedRoute,
    private apiService: ApiService,
    private location: Location
  ) {}

  ngOnInit() {
    this.route.queryParamMap.subscribe((params) => {
      const oauth = params.get('oauth');
      const token = params.get('token');
      const role = params.get('role');
      const oauthError = params.get('oauth_error');

      if (oauthError) {
        this.errorMessage = oauthError;
        return;
      }

      if (oauth === 'google' && token && role) {
        this.errorMessage = '';
        localStorage.setItem('auth_token', token);
        localStorage.setItem('user_role', role);

        // Fetch full user profile dari backend
        this.apiService.getUserProfile().subscribe({
          next: (response) => {
            console.log('User profile fetched:', response);
            localStorage.setItem('user_data', JSON.stringify({
              ...response,
              role
            }));

            if (role === 'driver') {
              this.router.navigate(['/driver-home'], { replaceUrl: true });
            } else {
              this.router.navigate(['/home'], { replaceUrl: true });
            }
          },
          error: (error) => {
            console.error('Failed to fetch user profile:', error);
            
            // Fallback: Simpan minimal user data jika fetch gagal
            const userData = {
              email: '',
              name: 'User',
              role: role
            };
            localStorage.setItem('user_data', JSON.stringify(userData));
            
            // Tetap redirect meski gagal fetch profile
            if (role === 'driver') {
              this.router.navigate(['/driver-home'], { replaceUrl: true });
            } else {
              this.router.navigate(['/home'], { replaceUrl: true });
            }
          }
        });
      }
    });
  }

  login() {
    this.errorMessage = '';

    if (!this.email || !this.password) {
      this.errorMessage = 'Please enter your email and password';
      return;
    }

    this.isLoading = true;
    const loginData = { email: this.email, password: this.password };

    this.apiService.login(loginData).subscribe({
      next: (response) => {
        this.isLoading = false;
        console.log('Login successful', response);
        
        // Cek role yang dikembalikan oleh API Laravel
        const userRole = this.apiService.getUserRole(response.user);
        
        if (userRole === 'driver') {
          this.router.navigate(['/driver-home'], { replaceUrl: true });
        } else {
          this.location.back();
        }
      },
      error: (error) => {
        this.isLoading = false;
        console.error('Login error', error);
        this.errorMessage = 'Login failed: ' + (error.error?.message || 'Invalid credentials');
      }
    });
  }

  async loginWithGoogle() {
    this.errorMessage = '';
    let frontendCallbackUrl = `${window.location.origin}/login`;
    
    // Jika berjalan di platform native (Android/iOS), gunakan custom scheme
    const { Capacitor } = await import('@capacitor/core');
    if (Capacitor.isNativePlatform()) {
      frontendCallbackUrl = 'shuttle://auth/callback';
    }

    const oauthUrl = this.apiService.getGoogleOAuthRedirectUrl(frontendCallbackUrl);
    
    if (Capacitor.isNativePlatform()) {
      const { Browser } = await import('@capacitor/browser');
      await Browser.open({ url: oauthUrl });
    } else {
      window.location.href = oauthUrl;
    }
  }
}
