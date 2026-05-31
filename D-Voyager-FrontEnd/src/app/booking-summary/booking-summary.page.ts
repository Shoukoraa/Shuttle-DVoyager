import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { ToastController } from '@ionic/angular';
import { ApiService } from '../services/api.service';
import { environment } from 'src/environments/environment';

@Component({
  selector: 'app-booking-summary',
  templateUrl: './booking-summary.page.html',
  styleUrls: ['./booking-summary.page.scss'],
  standalone: false
})
export class BookingSummaryPage implements OnInit {
  
  tripDetails = {
    scheduleId: null as number | null,
    operator: '',
    origin: '',
    destination: '',
    date: '',
    time: '',
    arrivalTime: '',
    seats: [] as string[]
  };

  contactDetails = {
    name: '',
    phone: '',
    email: ''
  };

  passengers: { seatNumber: string, title: string, name: string }[] = [];

  fareDetails = {
    ticketPrice: 0,
    fee: environment.serviceFee
  };

  promoCodeInput = '';
  appliedPromoCode = '';
  isPromoApplied = false;
  promoDiscount = 0;
  isPromoModalOpen = false;
  vouchers: any[] = [];

  get totalFare() {
    const base = (this.fareDetails.ticketPrice * this.tripDetails.seats.length) + this.fareDetails.fee;
    const discounted = base - this.promoDiscount;
    return discounted > 0 ? discounted : 0;
  }

  constructor(
    private router: Router, 
    private apiService: ApiService,
    private toastCtrl: ToastController
  ) { 
    // Get data from navigation state
    const navigation = this.router.getCurrentNavigation();
    if (navigation?.extras?.state) {
      const state = navigation.extras.state;
      this.tripDetails.scheduleId = state['scheduleId'] || null;
      this.tripDetails.seats = state['selectedSeats'] || [];
      this.fareDetails.ticketPrice = state['pricePerSeat'] || 0;
      this.tripDetails.operator = state['shuttleInfo']?.operator || '';
      this.tripDetails.time = state['shuttleInfo']?.time || '';
      this.tripDetails.arrivalTime = this.normalizeArrivalTime(state['shuttleInfo']?.arrivalTime);
      this.tripDetails.date = state['shuttleInfo']?.date || '';
      this.tripDetails.origin = state['routeOrigin'] || '';
      this.tripDetails.destination = state['routeDest'] || '';
    }
  }

  ngOnInit() {
    // If state is lost on page refresh, try to retrieve from sessionStorage
    const savedState = sessionStorage.getItem('bookingSummaryState');
    if (savedState) {
      const state: any = JSON.parse(savedState);
      if (this.tripDetails.seats.length === 0) {
        this.tripDetails.scheduleId = state.scheduleId || null;
        this.tripDetails.seats = state.selectedSeats || [];
        this.fareDetails.ticketPrice = state.pricePerSeat || 0;
        this.tripDetails.operator = state.shuttleInfo?.operator || '';
        this.tripDetails.time = state.shuttleInfo?.time || '';
        this.tripDetails.arrivalTime = this.normalizeArrivalTime(state.shuttleInfo?.arrivalTime);
        this.tripDetails.date = state.shuttleInfo?.date || '';
        this.tripDetails.origin = state.routeOrigin || '';
        this.tripDetails.destination = state.routeDest || '';
        
        if (state.contactDetails) {
          this.contactDetails = state.contactDetails;
        }
      }

      // Restore promo state
      if (state.isPromoApplied !== undefined) {
        this.isPromoApplied = state.isPromoApplied;
        this.appliedPromoCode = state.appliedPromoCode || '';
        this.promoDiscount = state.promoDiscount || 0;
        this.promoCodeInput = state.promoCodeInput || '';
      }
    }

    this.passengers = this.tripDetails.seats.map(seat => ({
      seatNumber: seat,
      title: 'Tn.',
      name: ''
    }));

    this.saveBookingSummaryState();
    this.loadServiceFee();
    this.loadVouchers();
  }

  private normalizeArrivalTime(arrivalTime: string | undefined): string {
    if (!arrivalTime || arrivalTime === 'Estimasi') {
      return 'Belum diatur';
    }

    return arrivalTime;
  }

  private loadServiceFee() {
    this.apiService.getAppConfig().subscribe({
      next: (config) => {
        const serviceFee = Number(config?.service_fee);

        if (!Number.isNaN(serviceFee) && serviceFee >= 0) {
          this.fareDetails.fee = serviceFee;
          this.saveBookingSummaryState();
        }
      },
      error: (error) => {
        console.error('Failed to load app config:', error);
      }
    });
  }

  private saveBookingSummaryState() {
    sessionStorage.setItem('bookingSummaryState', JSON.stringify({
      scheduleId: this.tripDetails.scheduleId,
      selectedSeats: this.tripDetails.seats,
      pricePerSeat: this.fareDetails.ticketPrice,
      serviceFee: this.fareDetails.fee,
      shuttleInfo: {
        operator: this.tripDetails.operator,
        time: this.tripDetails.time,
        arrivalTime: this.tripDetails.arrivalTime,
        date: this.tripDetails.date
      },
      routeOrigin: this.tripDetails.origin,
      routeDest: this.tripDetails.destination,
      contactDetails: this.contactDetails,
      appliedPromoCode: this.appliedPromoCode,
      isPromoApplied: this.isPromoApplied,
      promoDiscount: this.promoDiscount,
      promoCodeInput: this.promoCodeInput
    }));
  }

  async applyPromoCode() {
    if (this.isPromoApplied) {
      // Remove promo
      this.isPromoApplied = false;
      this.appliedPromoCode = '';
      this.promoCodeInput = '';
      this.promoDiscount = 0;
      this.saveBookingSummaryState();
      
      const toast = await this.toastCtrl.create({
        message: 'Voucher telah dihapus.',
        duration: 2000,
        color: 'medium',
        position: 'top'
      });
      toast.present();
      return;
    }

    const code = this.promoCodeInput ? this.promoCodeInput.trim().toUpperCase() : '';
    if (!code) {
      const toast = await this.toastCtrl.create({
        message: 'Silakan masukkan kode voucher.',
        duration: 2000,
        color: 'warning',
        position: 'top'
      });
      toast.present();
      return;
    }

    const ticketSubtotal = this.fareDetails.ticketPrice * this.tripDetails.seats.length;

    const voucher = this.vouchers.find(v => v.code.toUpperCase() === code);

    if (!voucher) {
      const toast = await this.toastCtrl.create({
        message: 'Kode voucher tidak valid atau telah kedaluwarsa.',
        duration: 2000,
        color: 'danger',
        position: 'top'
      });
      toast.present();
      return;
    }

    if (voucher.expiry_date) {
      const expiry = new Date(voucher.expiry_date);
      if (expiry < new Date()) {
        const toast = await this.toastCtrl.create({
          message: `Voucher ${code} telah kedaluwarsa.`,
          duration: 2000,
          color: 'danger',
          position: 'top'
        });
        toast.present();
        return;
      }
    }

    const minTx = Number(voucher.min_transaction) || 0;
    if (ticketSubtotal < minTx) {
      const toast = await this.toastCtrl.create({
        message: `Minimal transaksi untuk voucher ini adalah Rp ${minTx.toLocaleString('id-ID')}`,
        duration: 2000,
        color: 'warning',
        position: 'top'
      });
      toast.present();
      return;
    }

    let discount = 0;
    const value = Number(voucher.value) || 0;
    if (voucher.type === 'percentage') {
      discount = Math.round(ticketSubtotal * (value / 100));
      const maxDiscount = Number(voucher.max_discount);
      if (maxDiscount && discount > maxDiscount) {
        discount = maxDiscount;
      }
    } else if (voucher.type === 'flat') {
      discount = value;
    }

    if (discount > ticketSubtotal) {
      discount = ticketSubtotal;
    }

    this.isPromoApplied = true;
    this.appliedPromoCode = code;
    this.promoDiscount = discount;
    this.saveBookingSummaryState();

    const toast = await this.toastCtrl.create({
      message: `Voucher ${code} berhasil digunakan! Potongan Rp ${discount.toLocaleString('id-ID')}`,
      duration: 2000,
      color: 'success',
      position: 'top'
    });
    toast.present();
  }

  openPromoModal() {
    this.isPromoModalOpen = true;
  }

  async selectPromoDirectlyFromModal(code: string) {
    if (this.isPromoApplied && this.appliedPromoCode === code) {
      // Hapus jika diklik lagi saat sudah aktif
      this.isPromoApplied = false;
      this.appliedPromoCode = '';
      this.promoCodeInput = '';
      this.promoDiscount = 0;
      this.saveBookingSummaryState();
      
      const toast = await this.toastCtrl.create({
        message: 'Voucher telah dihapus.',
        duration: 2000,
        color: 'medium',
        position: 'top'
      });
      toast.present();
      return;
    }

    // Jika ada promo lain yang sedang aktif, hapus dulu tanpa toast
    if (this.isPromoApplied) {
      this.isPromoApplied = false;
      this.appliedPromoCode = '';
      this.promoDiscount = 0;
    }

    this.promoCodeInput = code;
    await this.applyPromoCode();
  }

  async applyPromoCodeFromModal() {
    await this.applyPromoCode();
  }

  loadVouchers() {
    this.apiService.getVouchers().subscribe({
      next: (res) => {
        this.vouchers = res || [];
      },
      error: (err) => {
        console.error('Failed to load vouchers in booking summary:', err);
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

  async proceedToPayment() {
    if (!this.contactDetails.name || !this.contactDetails.phone || !this.contactDetails.email) {
      const toast = await this.toastCtrl.create({
        message: 'Mohon lengkapi detail penumpang terlebih dahulu.',
        duration: 2000,
        color: 'warning',
        position: 'top'
      });
      toast.present();
      return;
    }

    this.saveBookingSummaryState();
    this.router.navigate(['/payment']);
  }

}
