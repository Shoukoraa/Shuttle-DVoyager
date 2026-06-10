import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { ApiService } from '../services/api.service';
import { AlertController } from '@ionic/angular';

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
  public filteredAsalLocations: any[] = [];
  public filteredTujuanLocations: any[] = [];

  public promos: any[] = [];
  public isLoadingPromos = true;
  public isLoadingLocations = true;

  public slideDistance = '0px';

  constructor(
    private router: Router, 
    private apiService: ApiService,
    private alertController: AlertController
  ) {}

  ionViewWillEnter() {
    this.initTabSlideAnimation(0);

    const role = localStorage.getItem('user_role');
    if (role === 'driver') {
      this.router.navigate(['/driver-home'], { replaceUrl: true });
    }
    
    // Refresh profil dan status login setiap kali tab Beranda dibuka
    this.loadCachedUserProfile();
    this.refreshUserProfile();
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

  ngOnInit() {
    const role = localStorage.getItem('user_role');
    if (role === 'driver') {
      this.router.navigate(['/driver-home'], { replaceUrl: true });
      return;
    }

    this.loadCachedUserProfile();
    this.refreshUserProfile();
    this.loadHomePromos();

    this.apiService.getLocations().subscribe({
      next: (res) => {
        console.log('Locations raw response:', res);
        const rawList = Array.isArray(res) ? res : Object.values(res || {});
        this.locations = rawList.filter(loc => loc && typeof loc === 'object' && typeof loc.name === 'string');
        this.refreshFilteredLocations();
        this.isLoadingLocations = false;
        console.log('Locations parsed list:', this.locations);
      },
      error: (err) => {
        console.error('Failed to load locations:', err);
        this.isLoadingLocations = false;
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
      let name = userData?.name || this.userName;
      if (name && name.length > 50) {
        name = name.substring(0, 50) + '...';
      }
      this.userName = name;
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
        let name = response?.name || this.userName;
        if (name && name.length > 50) {
          name = name.substring(0, 50) + '...';
        }
        this.userName = name;
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
    this.updateAsalFilter('');
    this.isAsalModalOpen = true;
  }

  closeAsalModal() {
    this.isAsalModalOpen = false;
  }

  openTujuanModal() {
    this.tujuanSearch = '';
    this.updateTujuanFilter('');
    this.isTujuanModalOpen = true;
  }

  closeTujuanModal() {
    this.isTujuanModalOpen = false;
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

  updateAsalFilter(query: string): void {
    this.filteredAsalLocations = this.filterLocations(query);
  }

  updateTujuanFilter(query: string): void {
    this.filteredTujuanLocations = this.filterLocations(query);
  }

  trackByLocationId(_: number, loc: any): number | string {
    return loc?.id ?? loc?.name;
  }

  private refreshFilteredLocations(): void {
    this.filteredAsalLocations = this.filterLocations(this.asalSearch);
    this.filteredTujuanLocations = this.filterLocations(this.tujuanSearch);
  }

  private filterLocations(query: string): any[] {
    const keyword = (query || '').trim().toLowerCase();
    const validLocs = this.locations || [];

    if (!keyword) {
      return validLocs;
    }

    return validLocs.filter(loc =>
      loc.name.toLowerCase().includes(keyword)
    );
  }

  async searchShuttle() {
    if (!this.asalId || !this.tujuanId) {
      const alert = await this.alertController.create({
        header: 'Pilih Lokasi',
        message: 'Silakan pilih kota asal dan tujuan terlebih dahulu untuk mencari jadwal.',
        cssClass: 'premium-alert',
        buttons: [{
          text: 'Cari',
          role: 'confirm',
          cssClass: 'alert-button-confirm'
        }]
      });
      await alert.present();
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

  loadHomePromos() {
    this.apiService.getVouchers().subscribe({
      next: (res) => {
        this.promos = (res || []).map((v: any) => {
          let title = v.title;
          const words = v.title.split(' ');
          if (words[1] === 'Rp' && words[2]) {
            title = words[0] + ' ' + words[1] + ' ' + words[2];
          } else if (words[0] && words[1]) {
            title = words[0] + ' ' + words[1];
          }
          return {
            title: title,
            subtitle: v.badge_text || 'Promo',
            color: v.theme_color || '#FFC107',
            icon: v.icon || 'gift-outline'
          };
        });
        this.isLoadingPromos = false;
      },
      error: (err) => {
        console.error('Failed to load home promos:', err);
        this.isLoadingPromos = false;
      }
    });
  }
}
