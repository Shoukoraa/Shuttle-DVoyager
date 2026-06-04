import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { AlertController, LoadingController, ToastController } from '@ionic/angular';
import { catchError, firstValueFrom, of, timeout } from 'rxjs';
import { ApiService } from '../services/api.service';
import { App } from '@capacitor/app';

@Component({
  selector: 'app-driver-profile',
  templateUrl: './driver-profile.page.html',
  styleUrls: ['./driver-profile.page.scss'],
  standalone: false
})
export class DriverProfilePage implements OnInit {
  public driverName: string = 'Driver';
  public driverId: string = '';
  public driverEmail: string = '';
  public vehicleType: string = '';
  public vehiclePlate: string = '';
  public profilePhotoUrl: string = '';
  public rating: number = 0;
  public totalTrips: number = 0;
  public reviewCount: number = 0;
  public reviews: any[] = [];
  public isLoadingReviews: boolean = false;
  public isLoggingOut: boolean = false;
  public isUploadingPhoto: boolean = false;
  public isTermsModalOpen: boolean = false;
  public isLogoutModalOpen: boolean = false;
  public errorMessage: string = '';
  public slideDistance = '0px';

  private lastProfileRefreshAt: number = 0;
  private isRefreshingProfile: boolean = false;

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
    this.initTabSlideAnimation(3);
    this.refreshUserData();
    this.loadDriverReviews();
  }

  private initTabSlideAnimation(currentTabIndex: number) {
    const prev = localStorage.getItem('driver_active_tab_index');
    if (prev !== null) {
      const prevIndex = parseInt(prev, 10);
      if (prevIndex !== currentTabIndex) {
        const diff = prevIndex - currentTabIndex;
        this.slideDistance = `${diff * 25}vw`;
      }
    }
    localStorage.setItem('driver_active_tab_index', currentTabIndex.toString());
  }

  loadCachedUserData() {
    const userDataRaw = localStorage.getItem('user_data');

    if (userDataRaw) {
      try {
        const user = JSON.parse(userDataRaw);
        this.applyUserToView(user);
      } catch (error) {
        console.error('Error parsing localStorage user_data:', error);
      }
    }
  }

  refreshUserData(force: boolean = false) {
    const token = localStorage.getItem('auth_token');
    const shouldSkip = !force && Date.now() - this.lastProfileRefreshAt < 30000;

    if (!token || this.isRefreshingProfile || shouldSkip) return;

    this.isRefreshingProfile = true;
    this.apiService.getUserProfile().subscribe({
      next: (response) => {
        const user = response || {};
        const currentUserRaw = localStorage.getItem('user_data');
        const currentUser = currentUserRaw ? JSON.parse(currentUserRaw) : {};

        const merged = {
          ...currentUser,
          ...user,
          role: currentUser.role || localStorage.getItem('user_role') || 'driver'
        };

        localStorage.setItem('user_data', JSON.stringify(merged));
        this.applyUserToView(merged);
        this.lastProfileRefreshAt = Date.now();
        this.isRefreshingProfile = false;
      },
      error: (err) => {
        console.error('Failed to refresh driver profile:', err);
        this.isRefreshingProfile = false;
      }
    });
  }

  private applyUserToView(user: any) {
    this.driverName = user?.name || user?.full_name || this.driverName;
    this.driverId = user?.driver_id || user?.id || this.driverId;
    this.driverEmail = user?.email || user?.contact_email || this.driverEmail;
    this.profilePhotoUrl = user?.profile_photo_url || this.profilePhotoUrl;
    this.vehicleType = user?.vehicle?.type || user?.vehicle_type || this.vehicleType;
    this.vehiclePlate = user?.vehicle?.plate || user?.vehicle_plate || this.vehiclePlate;
    this.rating = typeof user?.rating === 'number' ? user.rating : this.rating;
    this.reviewCount = typeof user?.review_count === 'number' ? user.review_count : this.reviewCount;
    this.totalTrips = typeof user?.total_trips === 'number' ? user.total_trips : this.totalTrips;
  }

  loadDriverReviews() {
    this.isLoadingReviews = true;

    this.apiService.getDriverReviews().subscribe({
      next: (response) => {
        this.reviews = response?.reviews || [];
        this.rating = typeof response?.average_rating === 'number' ? response.average_rating : this.rating;
        this.reviewCount = typeof response?.review_count === 'number' ? response.review_count : this.reviewCount;
        this.isLoadingReviews = false;
      },
      error: (err) => {
        console.error('Failed to load driver reviews:', err);
        this.isLoadingReviews = false;
      }
    });
  }

  getReviewRoute(review: any): string {
    const origin = review?.booking?.schedule?.route?.origin?.name || 'Asal';
    const destination = review?.booking?.schedule?.route?.destination?.name || 'Tujuan';
    return `${origin} -> ${destination}`;
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
        this.profilePhotoUrl = updatedUser?.profile_photo_url || this.profilePhotoUrl;
        this.isUploadingPhoto = false;
        input.value = '';
        await this.showToast('Foto profil berhasil diperbarui.', 'success');
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

    await this.showToast('Kamu sudah keluar dari akun.', 'success', 1400);
    this.router.navigate(['/login'], { replaceUrl: true });
  }

  private async showToast(message: string, color: string = 'success', duration: number = 1100) {
    const icon = color === 'success' ? 'checkmark-circle' :
                 color === 'danger' ? 'close-circle' :
                 color === 'warning' ? 'warning' : 'information-circle';

    const toast = await this.toastController.create({
      message,
      duration,
      position: 'top',
      cssClass: `premium-toast toast-${color}`,
      icon,
    });

    await toast.present();
  }

  openTermsModal() {
    this.isTermsModalOpen = true;
  }

  closeTermsModal() {
    this.isTermsModalOpen = false;
  }
}
