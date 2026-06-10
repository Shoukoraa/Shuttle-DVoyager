import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { Location } from '@angular/common';
import { ApiService } from '../services/api.service';

@Component({
  selector: 'app-driver-login',
  templateUrl: './driver-login.page.html',
  styleUrls: ['./driver-login.page.scss'],
  standalone: false
})
export class DriverLoginPage implements OnInit {
  public email: string = '';
  public password: string = '';
  public showPassword: boolean = false;
  public isLoading: boolean = false;
  public errorMessage: string = '';

  constructor(
    private router: Router,
    private apiService: ApiService,
    private location: Location
  ) {}

  ngOnInit() {}

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
        console.log('Driver login successful', response);
        
        // Cek role yang dikembalikan oleh API Laravel
        const userRole = this.apiService.getUserRole(response.user);
        
        if (userRole === 'driver') {
          this.router.navigate(['/driver-home'], { replaceUrl: true });
        } else {
          // If a customer logs in here accidentally, still log them in but go to home
          this.router.navigate(['/home'], { replaceUrl: true });
        }
      },
      error: (error) => {
        this.isLoading = false;
        console.error('Driver login error', error);
        this.errorMessage = 'Login failed: ' + (error.error?.message || 'Invalid credentials');
      }
    });
  }
}
