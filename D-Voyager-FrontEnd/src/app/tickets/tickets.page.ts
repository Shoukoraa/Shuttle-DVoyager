import { Component, OnInit, OnDestroy } from '@angular/core';
import { Router } from '@angular/router';
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

  constructor(private apiService: ApiService, private router: Router) { }

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

  continuePayment(ticket: any) {
    this.router.navigate(['/payment'], {
      queryParams: {
        pendingBookingId: ticket.id,
        totalPrice: ticket.total_price
      }
    });
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
