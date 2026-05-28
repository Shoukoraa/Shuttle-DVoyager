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
  public isLoggedIn = false;
  public userName = 'Penjelajah';
  public profilePhotoUrl = '';
  public locations: any[] = [];
  public asalId: number | null = null;
  public tujuanId: number | null = null;
  public tanggal: string = new Date().toISOString();
  public isSwapping = false;

  public isAsalModalOpen = false;
  public isTujuanModalOpen = false;
  public asalSearch = '';
  public tujuanSearch = '';

  public promos = [
    { title: 'Diskon 10%', subtitle: 'Rute Bus', color: '#FFC107', icon: 'ticket-outline' },
    { title: 'Cashback 20%', subtitle: 'Rute Shuttle', color: '#6CCD76', icon: 'cash-outline' },
    { title: 'Gratis 1x', subtitle: 'Pengguna Baru', color: '#FF7676', icon: 'gift-outline' }
  ];

  constructor(private router: Router, private apiService: ApiService) {}

  ionViewWillEnter() {
    const role = localStorage.getItem('user_role');
    if (role === 'driver') {
      this.router.navigate(['/driver-home'], { replaceUrl: true });
    }
  }

  ngOnInit() {
    const role = localStorage.getItem('user_role');
    if (role === 'driver') {
      this.router.navigate(['/driver-home'], { replaceUrl: true });
      return;
    }

    this.loadCachedUserProfile();
    this.refreshUserProfile();

    this.apiService.getLocations().subscribe({
      next: (res) => {
        console.log('Locations raw response:', res);
        const rawList = Array.isArray(res) ? res : Object.values(res || {});
        this.locations = rawList.filter(loc => loc && typeof loc === 'object' && typeof loc.name === 'string');
        console.log('Locations parsed list:', this.locations);
      },
      error: (err) => {
        console.error('Failed to load locations:', err);
      }
    });

    // Nama dan foto user bisa ditampilkan instan dari cache,
    // lalu disegarkan dari endpoint /me bila token tersedia.
  }

  private loadCachedUserProfile(): void {
    const userDataRaw = localStorage.getItem('user_data');
    const token = localStorage.getItem('auth_token');
    
    if (token) {
      this.isLoggedIn = true;
    }

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
      this.isLoggedIn = false;
      return;
    }
    
    this.isLoggedIn = true;

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
    this.isSwapping = true;
    const temp = this.asalId;
    this.asalId = this.tujuanId;
    this.tujuanId = temp;
    setTimeout(() => {
      this.isSwapping = false;
    }, 350);
  }

  setToday() {
    this.tanggal = new Date().toISOString();
  }

  setTomorrow() {
    const tmr = new Date();
    tmr.setDate(tmr.getDate() + 1);
    this.tanggal = tmr.toISOString();
  }

  isToday(): boolean {
    if (!this.tanggal) return false;
    const today = new Date();
    const current = new Date(this.tanggal);
    return today.toDateString() === current.toDateString();
  }

  isTomorrow(): boolean {
    if (!this.tanggal) return false;
    const tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    const current = new Date(this.tanggal);
    return tomorrow.toDateString() === current.toDateString();
  }

  getFormattedDate(): string {
    if (!this.tanggal) return 'Pilih Tanggal';
    const dateObj = new Date(this.tanggal);
    const options: Intl.DateTimeFormatOptions = { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' };
    return dateObj.toLocaleDateString('id-ID', options);
  }

  getCalendarMonth(): string {
    if (!this.tanggal) return 'MEI';
    const dateObj = new Date(this.tanggal);
    return dateObj.toLocaleDateString('id-ID', { month: 'short' }).toUpperCase();
  }

  getCalendarDay(): string {
    if (!this.tanggal) return '27';
    const dateObj = new Date(this.tanggal);
    return dateObj.toLocaleDateString('id-ID', { day: '2-digit' });
  }

  openAsalModal() {
    this.asalSearch = '';
    this.isAsalModalOpen = true;
  }

  openTujuanModal() {
    this.tujuanSearch = '';
    this.isTujuanModalOpen = true;
  }

  selectAsal(loc: any) {
    this.asalId = loc.id;
    this.isAsalModalOpen = false;
  }

  selectTujuan(loc: any) {
    this.tujuanId = loc.id;
    this.isTujuanModalOpen = false;
  }

  getAsalName(): string {
    const loc = this.locations.find(l => l.id === this.asalId);
    return loc ? loc.name : '';
  }

  getTujuanName(): string {
    const loc = this.locations.find(l => l.id === this.tujuanId);
    return loc ? loc.name : '';
  }

  getFilteredLocations(query: string): any[] {
    const validLocs = (this.locations || []).filter(loc => loc && typeof loc === 'object' && typeof loc.name === 'string');
    if (!query) return validLocs;
    return validLocs.filter(loc => 
      loc.name.toLowerCase().includes(query.toLowerCase())
    );
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
