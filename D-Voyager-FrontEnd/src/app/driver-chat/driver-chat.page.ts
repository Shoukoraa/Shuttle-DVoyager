import { Component, OnInit, OnDestroy } from '@angular/core';
import { ApiService } from '../services/api.service';
import { EchoService } from '../services/echo.service';

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
  isTyping = false;
  typingTimeout: any = null;
  lastTypingSent = 0;

  constructor(
    private apiService: ApiService,
    private echoService: EchoService
  ) { }

  ngOnInit() {
  }

  ionViewWillEnter() {
    this.loadData();
  }

  ngOnDestroy() {
    this.unsubscribeFromChat();
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
    this.subscribeToChat();
  }

  closeChat() {
    this.isChatOpen = false;
    this.unsubscribeFromChat();
    this.activeChatCustomer = null;
    this.isTyping = false;
    if (this.typingTimeout) {
      clearTimeout(this.typingTimeout);
    }
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

  subscribeToChat() {
    if (!this.currentScheduleId || !this.activeChatCustomer?.customer_id) return;
    
    this.echoService.getEcho()
      .private(`chat.${this.currentScheduleId}.${this.activeChatCustomer.customer_id}`)
      .listen('DriverCustomerMessageSent', (e: any) => {
        const isDuplicate = this.chatMessages.some(m => 
          m.id === e.id || 
          (!m.id && m.message === e.message && m.sender_type === e.sender_type)
        );
        if (!isDuplicate) {
          this.chatMessages.push(e);
        } else {
          const index = this.chatMessages.findIndex(m => !m.id && m.message === e.message && m.sender_type === e.sender_type);
          if (index !== -1) {
            this.chatMessages[index] = e;
          }
        }
      })
      .listenForWhisper('typing', (e: any) => {
        this.isTyping = true;
        if (this.typingTimeout) {
          clearTimeout(this.typingTimeout);
        }
        this.typingTimeout = setTimeout(() => {
          this.isTyping = false;
        }, 2500);
      });
  }

  unsubscribeFromChat() {
    if (!this.currentScheduleId || !this.activeChatCustomer?.customer_id) return;
    this.echoService.getEcho().leave(`chat.${this.currentScheduleId}.${this.activeChatCustomer.customer_id}`);
  }

  sendTypingEvent() {
    const now = Date.now();
    if (now - this.lastTypingSent < 1500) return;
    this.lastTypingSent = now;

    if (!this.currentScheduleId || !this.activeChatCustomer?.customer_id) return;

    this.echoService.getEcho()
      .private(`chat.${this.currentScheduleId}.${this.activeChatCustomer.customer_id}`)
      .whisper('typing', { typing: true });
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

    const optimisticMsg = { ...data, created_at: new Date().toISOString() };
    this.chatMessages.push(optimisticMsg);

    this.apiService.sendMessage(data).subscribe({
      next: (response) => {
        const index = this.chatMessages.findIndex(m => 
          (m.id === response.id) || 
          (!m.id && m.message === data.message && m.sender_type === data.sender_type)
        );
        if (index !== -1) {
          this.chatMessages[index] = response;
        } else if (!this.chatMessages.some(m => m.id === response.id)) {
          this.chatMessages.push(response);
        }
      },
      error: (err) => {
        console.error(err);
        this.newMessage = msg;
        this.chatMessages = this.chatMessages.filter(m => m !== optimisticMsg);
      }
    });
  }
}
