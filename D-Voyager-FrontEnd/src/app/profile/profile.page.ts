import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { AlertController, LoadingController, ToastController } from '@ionic/angular';
import { catchError, firstValueFrom, of, timeout } from 'rxjs';
import { ApiService } from '../services/api.service';

@Component({
  selector: 'app-profile',
  templateUrl: './profile.page.html',
  styleUrls: ['./profile.page.scss'],
  standalone: false
})
export class ProfilePage implements OnInit {
  userData: any = {
    name: '',
    email: '',
    phone: '',
    role: ''
  };
  isLoading: boolean = true;
  isLoggingOut: boolean = false;
  private isRefreshingProfile: boolean = false;
  private lastProfileRefreshAt: number = 0;

  constructor(
    private router: Router,
    private apiService: ApiService,
    private alertController: AlertController,
    private loadingController: LoadingController,
    private toastController: ToastController
  ) { }

  ngOnInit() {
    this.loadCachedUserData();
  }

  ionViewWillEnter() {
    this.refreshUserData();
  }

  loadCachedUserData() {
    const userDataRaw = localStorage.getItem('user_data');

    if (userDataRaw) {
      try {
        this.userData = {
          ...this.userData,
          ...JSON.parse(userDataRaw)
        };
        this.isLoading = false;
      } catch (error) {
        console.error('Error parsing localStorage user_data:', error);
      }
    }
  }

  refreshUserData(force: boolean = false) {
    this.loadCachedUserData();

    const token = localStorage.getItem('auth_token');
    const shouldSkipRefresh = !force && Date.now() - this.lastProfileRefreshAt < 30000;

    if (!token || this.isRefreshingProfile || shouldSkipRefresh) {
      this.isLoading = false;
      return;
    }

    this.isRefreshingProfile = true;
    this.apiService.getUserProfile().subscribe({
      next: (response) => {
        this.userData = {
          ...this.userData,
          ...response,
          role: localStorage.getItem('user_role') || this.userData.role || 'customer'
        };

        localStorage.setItem('user_data', JSON.stringify(this.userData));
        this.lastProfileRefreshAt = Date.now();
        this.isLoading = false;
        this.isRefreshingProfile = false;
      },
      error: (error) => {
        console.error('Failed to refresh profile from API:', error);

        if (!this.userData.name) {
          this.userData = {
            name: 'User',
            email: 'Not available',
            phone: '',
            role: localStorage.getItem('user_role') || 'customer'
          };
        }

        this.isLoading = false;
        this.isRefreshingProfile = false;
      }
    });
  }

  async logout() {
    if (this.isLoggingOut) {
      return;
    }

    const alert = await this.alertController.create({
      header: 'Keluar akun?',
      message: 'Kamu perlu login lagi untuk melihat tiket dan melakukan pemesanan.',
      buttons: [
        {
          text: 'Batal',
          role: 'cancel',
        },
        {
          text: 'Keluar',
          role: 'destructive',
          handler: () => {
            this.performLogout();
          },
        },
      ],
    });

    await alert.present();
  }

  private async performLogout() {
    this.isLoggingOut = true;

    const loading = await this.loadingController.create({
      message: 'Keluar dari akun...',
      spinner: 'crescent',
    });

    await loading.present();

    await firstValueFrom(
      this.apiService.logout().pipe(
        timeout({ first: 2500 }),
        catchError((error) => {
          console.error('Logout API error:', error);
          return of(null);
        })
      )
    );

    this.apiService.clearAuthSession();
    await loading.dismiss();
    this.isLoggingOut = false;

    const toast = await this.toastController.create({
      message: 'Kamu sudah keluar dari akun.',
      duration: 1400,
      position: 'top',
      color: 'success',
    });
    await toast.present();

    this.router.navigate(['/login'], { replaceUrl: true });
  }
}
