import { Component, OnInit, OnDestroy, NgZone } from '@angular/core';
import { Router, ActivatedRoute } from '@angular/router';
import { ToastController } from '@ionic/angular';
import { Browser } from '@capacitor/browser';
import { Capacitor } from '@capacitor/core';
import { environment } from 'src/environments/environment';
import { ApiService } from '../services/api.service';

interface PaymentMethod {
  id: string;
  name: string;
  icon: string;
  type: 'va' | 'qris';
}

@Component({
  selector: 'app-payment',
  templateUrl: './payment.page.html',
  styleUrls: ['./payment.page.scss'],
  standalone: false
})
export class PaymentPage implements OnInit, OnDestroy {

  totalFare = 0;
  ticketTotal = 0;
  serviceFee = environment.serviceFee;
  appliedPromoCode = '';
  promoDiscount = 0;
  selectedMethodId: string | null = null;
  scheduleId: number | null = null;
  selectedSeats: string[] = [];
  isProcessing = false;
  pendingBookingId: number | null = null;
  displayTimer: string = '15:00';
  countdownSeconds: number = 900;
  timerInterval: any;
  private browserFinishedListener: any = null;

  paymentMethods: { category: string, items: PaymentMethod[] }[] = [

    {
      category: 'Virtual Account',
      items: [
        { id: 'VA_PERMATA', name: 'Permata Virtual Account', icon: 'business-outline', type: 'va' },
        { id: 'VA_BCA', name: 'BCA Virtual Account', icon: 'business-outline', type: 'va' },
        { id: 'VA_MANDIRI', name: 'Mandiri Virtual Account', icon: 'business-outline', type: 'va' },
        { id: 'VA_DANAMON', name: 'Danamon Virtual Account', icon: 'business-outline', type: 'va' },
        { id: 'VA_CIMB', name: 'CIMB Virtual Account', icon: 'business-outline', type: 'va' },
        { id: 'VA_BSI', name: 'BSI Virtual Account', icon: 'business-outline', type: 'va' },
        { id: 'VA_BRI', name: 'BRI Virtual Account', icon: 'business-outline', type: 'va' },
        { id: 'VA_BNI', name: 'BNI Virtual Account', icon: 'business-outline', type: 'va' }
      ]
    },
    {
      category: 'QRIS',
      items: [
        { id: 'QRIS', name: 'QRIS', icon: 'qr-code-outline', type: 'qris' }
      ]
    }
  ];

  constructor(
    private apiService: ApiService,
    private router: Router,
    private route: ActivatedRoute,
    private toastCtrl: ToastController,
    private zone: NgZone
  ) { }

  ngOnInit() {
    this.startTimer();
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

  ngOnDestroy() {
    if (this.timerInterval) {
      clearInterval(this.timerInterval);
    }
    this.removeBrowserListener();
  }

  private startTimer() {
    this.timerInterval = setInterval(() => {
      if (this.countdownSeconds > 0) {
        this.countdownSeconds--;
        const minutes = Math.floor(this.countdownSeconds / 60);
        const seconds = this.countdownSeconds % 60;
        this.displayTimer = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
      } else {
        clearInterval(this.timerInterval);
        this.showToast('Waktu pembayaran telah habis', 'danger');
        this.router.navigate(['/booking-summary']);
      }
    }, 1000);
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

      this.appliedPromoCode = state.appliedPromoCode || '';
      this.promoDiscount = Number(state.promoDiscount) || 0;

      this.ticketTotal = seats.length * pricePerSeat;
      this.totalFare = this.ticketTotal + this.serviceFee - this.promoDiscount;
      if (this.totalFare < 0) {
        this.totalFare = 0;
      }
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
          this.totalFare = this.ticketTotal + this.serviceFee - this.promoDiscount;
          if (this.totalFare < 0) {
            this.totalFare = 0;
          }
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
      sessionStorage.setItem('pendingPaymentBookingId', this.pendingBookingId.toString());
      this.apiService.payTicket(this.pendingBookingId, {
          amount: this.totalFare,
          payment_method: this.selectedMethodId
      }).subscribe({
        next: async (payRes) => {
          this.isProcessing = false;
          await this.handlePaymentResponse(payRes);
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
      passenger_email: passengerData.email,
      promo_code: this.appliedPromoCode || null
    }).subscribe({
      next: (bookingRes) => {
        this.apiService.payTicket(bookingRes.booking_id, {
          amount: bookingRes.total_price,
          payment_method: this.selectedMethodId!
        }).subscribe({
          next: async (payRes) => {
            this.isProcessing = false;
            sessionStorage.removeItem('bookingSummaryState');
            sessionStorage.setItem('pendingPaymentBookingId', bookingRes.booking_id.toString());
            await this.handlePaymentResponse(payRes);
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

  private async handlePaymentResponse(payRes: any) {
    const paymentUrl = payRes?.payment_url || payRes?.payment?.payment_url;

    if (paymentUrl) {
      await this.showToast('Checkout DompetX dibuat. Mengarahkan ke pembayaran...', 'success');
      await this.openPaymentCheckout(paymentUrl);
      return;
    }

    sessionStorage.setItem('showPaymentSuccessMascot', '1');
    this.router.navigate(['/tickets']);
  }

  private async openPaymentCheckout(paymentUrl: string) {
    if (Capacitor.isNativePlatform()) {
      // Dengarkan event ketika in-app browser ditutup
      this.removeBrowserListener();
      this.browserFinishedListener = await Browser.addListener('browserFinished', () => {
        this.removeBrowserListener();
        this.zone.run(() => {
          this.router.navigate(['/tickets']);
        });
      });

      await Browser.open({
        url: paymentUrl,
        presentationStyle: 'fullscreen',
      });

      return;
    }

    window.open(paymentUrl, '_blank', 'noopener,noreferrer');
    this.router.navigate(['/tickets']);
  }

  private removeBrowserListener() {
    if (this.browserFinishedListener) {
      this.browserFinishedListener.remove();
      this.browserFinishedListener = null;
    }
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
