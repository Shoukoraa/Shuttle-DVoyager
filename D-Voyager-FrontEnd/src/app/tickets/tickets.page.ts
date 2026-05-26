import { Component, OnInit, OnDestroy } from '@angular/core';
import { Router } from '@angular/router';
import { ToastController } from '@ionic/angular';
import { ApiService } from '../services/api.service';

@Component({
  selector: 'app-tickets',
  templateUrl: './tickets.page.html',
  styleUrls: ['./tickets.page.scss'],
  standalone: false
})
export class TicketsPage implements OnInit {

  hasTickets: boolean = false;
  tickets: any[] = [];
  isLoading: boolean = false;
  selectedTab: 'aktif' | 'riwayat' = 'aktif';
  isRatingOpen = false;
  ratingTicket: any = null;
  selectedRating = 0;
  ratingComment = '';
  isSubmittingRating = false;
  readonly ratingOptions = [1, 2, 3, 4, 5];

  constructor(
    private apiService: ApiService,
    private router: Router,
    private toastController: ToastController
  ) { }

  ngOnInit() {
  }

  ionViewWillEnter() {
    this.fetchMyBookings();
  }

  fetchMyBookings() {
    this.isLoading = true;
    this.apiService.getMyBookings().subscribe({
      next: (data: any[]) => {
        this.tickets = data || [];
        this.hasTickets = this.tickets.length > 0;
        this.isLoading = false;
      },
      error: (err) => {
        console.error('Failed to fetch bookings', err);
        this.isLoading = false;
        this.hasTickets = false;
      }
    });
  }

  get activeTickets(): any[] {
    return this.tickets.filter((ticket) => !this.isHistoryTicket(ticket));
  }

  get historyTickets(): any[] {
    return this.tickets.filter((ticket) => this.isHistoryTicket(ticket));
  }

  get selectedTickets(): any[] {
    return this.selectedTab === 'riwayat' ? this.historyTickets : this.activeTickets;
  }

  private isHistoryTicket(ticket: any): boolean {
    const normalizedStatus = (ticket?.status || '').toLowerCase();

    if (['completed', 'canceled', 'cancelled', 'finished'].includes(normalizedStatus)) {
      return true;
    }

    if (normalizedStatus === 'on_the_way') {
      return false;
    }

    const departureMs = this.getDepartureTimeMs(ticket);

    if (departureMs === null) {
      return false;
    }

    const todayStart = new Date();
    todayStart.setHours(0, 0, 0, 0);

    return departureMs < todayStart.getTime();
  }

  private getDepartureTimeMs(ticket: any): number | null {
    const raw = ticket?.schedule?.departure_time;

    if (!raw) {
      return null;
    }

    const parsed = new Date(raw).getTime();
    return Number.isNaN(parsed) ? null : parsed;
  }

  continuePayment(ticket: any) {
    this.router.navigate(['/payment'], {
      queryParams: {
        pendingBookingId: ticket.id,
        totalPrice: ticket.total_price
      }
    });
  }

  canRate(ticket: any): boolean {
    return this.isHistoryTicket(ticket) && !ticket?.review && !!ticket?.schedule?.driver_id;
  }

  openRating(ticket: any) {
    this.ratingTicket = ticket;
    this.selectedRating = ticket?.review?.rating || 0;
    this.ratingComment = ticket?.review?.comment || '';
    this.isRatingOpen = true;
  }

  closeRating() {
    if (this.isSubmittingRating) {
      return;
    }

    this.isRatingOpen = false;
    this.ratingTicket = null;
    this.selectedRating = 0;
    this.ratingComment = '';
  }

  setRating(value: number) {
    this.selectedRating = value;
  }

  submitRating() {
    if (!this.ratingTicket?.id || this.selectedRating < 1 || this.selectedRating > 5) {
      this.presentToast('Pilih rating 1 sampai 5 bintang.', 'warning');
      return;
    }

    this.isSubmittingRating = true;

    this.apiService.submitBookingReview(this.ratingTicket.id, {
      rating: this.selectedRating,
      comment: this.ratingComment.trim() || undefined
    }).subscribe({
      next: (response) => {
        const review = response?.review;

        this.tickets = this.tickets.map((ticket) => {
          if (ticket.id !== this.ratingTicket.id) {
            return ticket;
          }

          return {
            ...ticket,
            review
          };
        });

        this.isSubmittingRating = false;
        this.closeRating();
        this.presentToast('Terima kasih, rating berhasil dikirim.', 'success');
      },
      error: (err) => {
        console.error('Failed to submit rating', err);
        this.isSubmittingRating = false;
        this.presentToast(err?.error?.message || 'Rating gagal dikirim.', 'danger');
      }
    });
  }

  private async presentToast(message: string, color: string = 'medium') {
    const toast = await this.toastController.create({
      message,
      duration: 2200,
      color,
      position: 'top'
    });

    await toast.present();
  }

  // --- Chat Features ---
  isChatOpen = false;
  activeChatTicket: any = null;
  chatMessages: any[] = [];
  newMessage: string = '';
  private chatInterval: any;

  ngOnDestroy() {
    this.stopPolling();
  }

  openChat(ticket: any) {
    this.activeChatTicket = ticket;
    this.isChatOpen = true;
    this.loadChatHistory();
    this.startPolling();
  }

  closeChat() {
    this.isChatOpen = false;
    this.activeChatTicket = null;
    this.stopPolling();
  }

  loadChatHistory() {
    if (!this.activeChatTicket?.schedule_id) return;
    this.apiService.getChatMessages(this.activeChatTicket.schedule_id).subscribe({
      next: (msgs) => this.chatMessages = msgs,
      error: (err) => console.error(err)
    });
  }

  startPolling() {
    this.chatInterval = setInterval(() => {
      this.loadChatHistory();
    }, 5000);
  }

  stopPolling() {
    if (this.chatInterval) {
      clearInterval(this.chatInterval);
    }
  }

  sendMessage() {
    if (!this.newMessage.trim() || !this.activeChatTicket?.schedule_id || !this.activeChatTicket?.customer_id) return;
    
    const data = {
      schedule_id: this.activeChatTicket.schedule_id,
      customer_id: this.activeChatTicket.customer_id,
      message: this.newMessage.trim(),
      sender_type: 'customer'
    };

    const msg = this.newMessage;
    this.newMessage = '';

    // Optimistic push
    this.chatMessages.push({ ...data, created_at: new Date().toISOString() });

    this.apiService.sendMessage(data).subscribe({
      next: () => this.loadChatHistory(),
      error: (err) => {
        console.error(err);
        this.newMessage = msg;
      }
    });
  }
}
