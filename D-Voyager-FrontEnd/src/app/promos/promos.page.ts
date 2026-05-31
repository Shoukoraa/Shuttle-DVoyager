import { Component, OnInit } from '@angular/core';
import { ApiService } from '../services/api.service';

@Component({
  selector: 'app-promos',
  templateUrl: './promos.page.html',
  styleUrls: ['./promos.page.scss'],
  standalone: false
})
export class PromosPage implements OnInit {
  public vouchers: any[] = [];
  public isLoading = true;

  constructor(private apiService: ApiService) { }

  ngOnInit() {
    this.loadVouchers();
  }

  loadVouchers() {
    this.apiService.getVouchers().subscribe({
      next: (res) => {
        this.vouchers = res || [];
        this.isLoading = false;
      },
      error: (err) => {
        console.error('Failed to load vouchers:', err);
        this.isLoading = false;
      }
    });
  }

  hexToRgb(hex: string): string {
    if (!hex || !hex.startsWith('#')) return '255, 193, 7';
    try {
      const r = parseInt(hex.slice(1, 3), 16);
      const g = parseInt(hex.slice(3, 5), 16);
      const b = parseInt(hex.slice(5, 7), 16);
      return `${r}, ${g}, ${b}`;
    } catch (e) {
      return '255, 193, 7';
    }
  }

  getFormattedExpiry(dateStr: string): string {
    if (!dateStr) return '-';
    try {
      const date = new Date(dateStr);
      const options: Intl.DateTimeFormatOptions = { day: 'numeric', month: 'short', year: 'numeric' };
      return date.toLocaleDateString('id-ID', options);
    } catch (e) {
      return dateStr;
    }
  }
}
