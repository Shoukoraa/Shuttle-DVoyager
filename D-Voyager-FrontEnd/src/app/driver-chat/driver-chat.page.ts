import { Component, OnInit, OnDestroy } from '@angular/core';
import { ApiService } from '../services/api.service';

@Component({
  selector: 'app-driver-chat',
  templateUrl: './driver-chat.page.html',
  styleUrls: ['./driver-chat.page.scss'],
  standalone: false
})
export class DriverChatPage implements OnInit, OnDestroy {
  public selectedTab: string = 'customer';
  public isLoading = false;
  
  public currentScheduleId: number | null = null;
  public customerChats: any[] = [];
  public csChats = [
    { name: 'Admin Pusat', message: 'Gunakan tab ini jika ada kendala di jalan.', time: '', unread: 0 },
  ];

  isChatOpen = false;
  activeChatCustomer: any = null;
  chatMessages: any[] = [];
  newMessage: string = '';
  private chatInterval: any;

  constructor(private apiService: ApiService) { }

  ngOnInit() {
  }

  ionViewWillEnter() {
    this.loadData();
  }

  ngOnDestroy() {
    this.stopPolling();
  }

  loadData() {
    this.isLoading = true;
    this.apiService.getDriverSchedules().subscribe({
      next: (schedules: any[]) => {
        const activeSchedules = schedules.filter(s => s.status !== 'completed');
        if (activeSchedules && activeSchedules.length > 0) {
          this.currentScheduleId = activeSchedules[0].id;
          this.loadPassengers(this.currentScheduleId!);
        } else {
          this.isLoading = false;
          this.customerChats = [];
        }
      },
      error: () => this.isLoading = false
    });
  }

  loadPassengers(scheduleId: number) {
    this.apiService.getDriverManifest(scheduleId).subscribe({
      next: (data: any[]) => {
        this.customerChats = data.map(p => ({
          customer_id: p.customer_id,
          name: p.passenger_name,
          phone: p.phone,
          message: 'Ketuk untuk mulai chat',
          time: '',
          unread: 0
        }));
        this.isLoading = false;
      },
      error: () => this.isLoading = false
    });
  }

  openChat(customer: any) {
    this.activeChatCustomer = customer;
    this.isChatOpen = true;
    this.loadChatHistory();
    this.startPolling();
  }

  closeChat() {
    this.isChatOpen = false;
    this.activeChatCustomer = null;
    this.stopPolling();
  }

  loadChatHistory() {
    if (!this.currentScheduleId || !this.activeChatCustomer?.customer_id) return;
    this.apiService.getChatMessages(this.currentScheduleId, this.activeChatCustomer.customer_id).subscribe({
      next: (msgs) => {
        this.chatMessages = msgs;
      },
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
    if (!this.newMessage.trim() || !this.currentScheduleId || !this.activeChatCustomer?.customer_id) return;
    
    const data = {
      schedule_id: this.currentScheduleId,
      customer_id: this.activeChatCustomer.customer_id,
      message: this.newMessage.trim(),
      sender_type: 'driver'
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
