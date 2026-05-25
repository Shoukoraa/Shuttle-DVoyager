import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { ApiService } from '../services/api.service';

@Component({
  selector: 'app-home',
  templateUrl: 'home.page.html',
  styleUrls: ['home.page.scss'],
  standalone: false,
})
export class HomePage implements OnInit {
  public userName = 'User';
  public profilePhotoUrl = '';
  public locations: any[] = [];
  public asalId: number | null = null;
  public tujuanId: number | null = null;
  public tanggal: string = new Date().toISOString();

  public promos = [
    { title: 'Diskon 10%', subtitle: 'Rute Bus', color: '#FFC107', icon: 'ticket-outline' },
    { title: 'Cashback 20%', subtitle: 'Rute Shuttle', color: '#6CCD76', icon: 'cash-outline' },
    { title: 'Gratis 1x', subtitle: 'Pengguna Baru', color: '#FF7676', icon: 'gift-outline' }
  ];

  constructor(private router: Router, private apiService: ApiService) {}

  ngOnInit() {
    this.loadCachedUserProfile();
    this.refreshUserProfile();

    this.apiService.getLocations().subscribe({
      next: (res) => {
        this.locations = res;
      },
      error: (err) => console.error(err)
    });

    // Nama dan foto user bisa ditampilkan instan dari cache,
    // lalu disegarkan dari endpoint /me bila token tersedia.
  }

  private loadCachedUserProfile(): void {
    const userDataRaw = localStorage.getItem('user_data');

    if (!userDataRaw) {
      return;
    }

    try {
      const userData = JSON.parse(userDataRaw);
      this.userName = userData?.name || this.userName;
      this.profilePhotoUrl = userData?.profile_photo_url || '';
    } catch (error) {
      console.error('Failed to parse user_data from localStorage:', error);
    }
  }

  private refreshUserProfile(): void {
    const token = localStorage.getItem('auth_token');

    if (!token) {
      return;
    }

    this.apiService.getUserProfile().subscribe({
      next: (response) => {
        this.userName = response?.name || this.userName;
        this.profilePhotoUrl = response?.profile_photo_url || this.profilePhotoUrl;

        localStorage.setItem('user_data', JSON.stringify({
          ...response,
          role: localStorage.getItem('user_role') || response?.role || 'customer',
        }));
      },
      error: (error) => {
        console.error('Failed to refresh home profile from API:', error);
      }
    });
  }

  swapRoutes() {
    const temp = this.asalId;
    this.asalId = this.tujuanId;
    this.tujuanId = temp;
  }

  setToday() {
    this.tanggal = new Date().toISOString();
  }

  setTomorrow() {
    const tmr = new Date();
    tmr.setDate(tmr.getDate() + 1);
    this.tanggal = tmr.toISOString();
  }

  searchShuttle() {
    if (!this.asalId || !this.tujuanId) {
      alert('Pilih kota asal dan tujuan');
      return;
    }

    // Format date to YYYY-MM-DD
    const dateObj = new Date(this.tanggal);
    const formattedDate = dateObj.toISOString().split('T')[0];

    this.router.navigate(['/schedule'], {
      queryParams: {
        origin_id: this.asalId,
        destination_id: this.tujuanId,
        date: formattedDate
      }
    });
  }
}
