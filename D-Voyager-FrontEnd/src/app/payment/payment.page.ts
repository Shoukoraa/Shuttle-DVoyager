import { Component, OnInit } from '@angular/core';
import { Router, ActivatedRoute } from '@angular/router';
import { ToastController } from '@ionic/angular';
import { environment } from 'src/environments/environment';
import { ApiService } from '../services/api.service';

interface PaymentMethod {
  id: string;
  name: string;
  icon: string;
  type: 'va' | 'ewallet' | 'retail';
}

@Component({
  selector: 'app-payment',
  templateUrl: './payment.page.html',
  styleUrls: ['./payment.page.scss'],
  standalone: false
})
export class PaymentPage implements OnInit {

  totalFare = 0;
  ticketTotal = 0;
  serviceFee = environment.serviceFee;
  selectedMethodId: string | null = null;
  scheduleId: number | null = null;
  selectedSeats: string[] = [];
  isProcessing = false;
  pendingBookingId: number | null = null;

  paymentMethods: { category: string, items: PaymentMethod[] }[] = [
    {
      category: 'Virtual Account',
      items: [
        { id: 'bca', name: 'BCA Virtual Account', icon: 'business-outline', type: 'va' },
        { id: 'mandiri', name: 'Mandiri Virtual Account', icon: 'business-outline', type: 'va' },
        { id: 'bni', name: 'BNI Virtual Account', icon: 'business-outline', type: 'va' }
      ]
    },
    {
      category: 'E-Wallet',
      items: [
        { id: 'gopay', name: 'GoPay', icon: 'wallet-outline', type: 'ewallet' },
        { id: 'ovo', name: 'OVO', icon: 'wallet-outline', type: 'ewallet' },
        { id: 'dana', name: 'DANA', icon: 'wallet-outline', type: 'ewallet' }
      ]
    },
    {
      category: 'Minimarket',
      items: [
        { id: 'alfamart', name: 'Alfamart', icon: 'storefront-outline', type: 'retail' },
        { id: 'indomaret', name: 'Indomaret', icon: 'storefront-outline', type: 'retail' }
      ]
    }
  ];

  constructor(
    private apiService: ApiService,
    private router: Router,
    private route: ActivatedRoute,
    private toastCtrl: ToastController
  ) { }

  ngOnInit() {
    this.route.queryParams.subscribe(params => {
      if (params['pendingBookingId']) {
        this.pendingBookingId = Number(params['pendingBookingId']);
        this.totalFare = Number(params['totalPrice']) || 0;
      } else {
        this.loadPaymentSummary();
        this.loadServiceFee();
      }
    });
  }

  selectMethod(id: string) {
    this.selectedMethodId = id;
  }

  private loadPaymentSummary() {
    const savedState = sessionStorage.getItem('bookingSummaryState');

    if (!savedState) {
      this.totalFare = this.serviceFee;
      return;
    }

    try {
      const state = JSON.parse(savedState);
      this.scheduleId = state.scheduleId || null;
      this.selectedSeats = state.selectedSeats || [];
      const seats = state.selectedSeats || [];
      const pricePerSeat = Number(state.pricePerSeat || 0);
      const savedServiceFee = Number(state.serviceFee);

      if (!Number.isNaN(savedServiceFee) && savedServiceFee >= 0) {
        this.serviceFee = savedServiceFee;
      }

      this.ticketTotal = seats.length * pricePerSeat;
      this.totalFare = this.ticketTotal + this.serviceFee;
    } catch (error) {
      console.error('Error parsing bookingSummaryState:', error);
      this.totalFare = this.serviceFee;
    }
  }

  private loadServiceFee() {
    this.apiService.getAppConfig().subscribe({
      next: (config) => {
        const serviceFee = Number(config?.service_fee);

        if (!Number.isNaN(serviceFee) && serviceFee >= 0) {
          this.serviceFee = serviceFee;
          this.totalFare = this.ticketTotal + this.serviceFee;
          this.saveServiceFeeToSummary();
        }
      },
      error: (error) => {
        console.error('Failed to load app config:', error);
      }
    });
  }

  private saveServiceFeeToSummary() {
    const savedState = sessionStorage.getItem('bookingSummaryState');

    if (!savedState) {
      return;
    }

    try {
      const state = JSON.parse(savedState);
      sessionStorage.setItem('bookingSummaryState', JSON.stringify({
        ...state,
        serviceFee: this.serviceFee,
      }));
    } catch (error) {
      console.error('Error updating bookingSummaryState:', error);
    }
  }

  async processPayment() {
    if (!this.selectedMethodId) {
      this.showToast('Silakan pilih metode pembayaran', 'warning');
      return;
    }

    // Scenario B: Resume Payment for existing booking
    if (this.pendingBookingId) {
      this.isProcessing = true;
      this.apiService.payTicket(this.pendingBookingId, {
        amount: this.totalFare,
        payment_method: this.selectedMethodId
      }).subscribe({
        next: async (payRes) => {
          this.isProcessing = false;
          await this.showToast('Pembayaran berhasil!', 'success');
          this.router.navigate(['/home']);
        },
        error: (err) => {
          console.error(err);
          this.isProcessing = false;
          this.showToast(err.error?.message || 'Gagal memproses pembayaran', 'danger');
        }
      });
      return;
    }

    // Scenario A: New Booking
    if (!this.scheduleId || this.selectedSeats.length === 0) {
      this.showToast('Data booking tidak lengkap', 'danger');
      return;
    }

    let passengerData = { name: '', phone: '', email: '' };
    try {
      const savedState = sessionStorage.getItem('bookingSummaryState');
      if (savedState) {
        const state = JSON.parse(savedState);
        if (state.contactDetails) {
          passengerData = state.contactDetails;
        }
      }
    } catch (e) {
      console.error(e);
    }

    this.isProcessing = true;

    this.apiService.bookTicket({
      schedule_id: this.scheduleId,
      seats: this.selectedSeats,
      passenger_name: passengerData.name,
      passenger_phone: passengerData.phone,
      passenger_email: passengerData.email
    }).subscribe({
      next: (bookingRes) => {
        this.apiService.payTicket(bookingRes.booking_id, {
          amount: bookingRes.total_price,
          payment_method: this.selectedMethodId!
        }).subscribe({
          next: async (payRes) => {
            this.isProcessing = false;
            sessionStorage.removeItem('bookingSummaryState');
            await this.showToast('Pembayaran berhasil!', 'success');
            this.router.navigate(['/home']);
          },
          error: (err) => {
            console.error(err);
            this.isProcessing = false;
            this.showToast(err.error?.message || 'Gagal memproses pembayaran', 'danger');
          }
        });
      },
      error: (err) => {
        console.error(err);
        this.isProcessing = false;
        this.showToast(err.error?.message || 'Gagal membuat booking', 'danger');
      }
    });
  }

  async showToast(message: string, color: string) {
    const toast = await this.toastCtrl.create({
      message,
      duration: 2000,
      color,
      position: 'top'
    });
    toast.present();
  }

}
