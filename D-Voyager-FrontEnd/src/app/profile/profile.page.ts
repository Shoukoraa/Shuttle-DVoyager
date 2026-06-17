import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { AlertController, LoadingController, ToastController } from '@ionic/angular';
import { catchError, firstValueFrom, of, timeout } from 'rxjs';
import { ApiService } from '../services/api.service';
import { App } from '@capacitor/app';

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
  isLogoutModalOpen: boolean = false;
  isTermsModalOpen: boolean = false;
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
    const role = localStorage.getItem('user_role');
    if (role === 'driver') {
      this.router.navigate(['/driver-profile'], { replaceUrl: true });
      return;
    }
    this.loadCachedUserData();
  }

  public slideDistance = '0px';

  ionViewWillEnter() {
    this.initTabSlideAnimation(3);

    const role = localStorage.getItem('user_role');
    if (role === 'driver') {
      this.router.navigate(['/driver-profile'], { replaceUrl: true });
      return;
    }
    this.refreshUserData();
  }

  private initTabSlideAnimation(currentTabIndex: number) {
    const prev = localStorage.getItem('active_tab_index');
    if (prev !== null) {
      const prevIndex = parseInt(prev, 10);
      if (prevIndex !== currentTabIndex) {
        const diff = prevIndex - currentTabIndex;
        this.slideDistance = `${diff * 25}vw`;
      }
    }
    localStorage.setItem('active_tab_index', currentTabIndex.toString());
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

  logout() {
    if (this.isLoggingOut) {
      return;
    }
    this.isLogoutModalOpen = true;
  }

  confirmLogout() {
    this.isLogoutModalOpen = false;
    this.performLogout();
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
      duration: 1100,
      position: 'top',
      cssClass: 'premium-toast toast-success',
      icon: 'checkmark-circle',
    });
    await toast.present();

    this.router.navigate(['/login'], { replaceUrl: true });
  }

  openTermsModal() {
    this.isTermsModalOpen = true;
  }

  closeTermsModal() {
    this.isTermsModalOpen = false;
  }
}
