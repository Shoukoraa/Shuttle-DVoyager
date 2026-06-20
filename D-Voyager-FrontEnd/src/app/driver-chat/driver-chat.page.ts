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
  public slideDistance = '0px';
  
  public currentScheduleId: number | null = null;
  public customerChats: any[] = [];
  public csChats = [
    { name: 'Admin Pusat', message: 'Ketuk untuk menghubungi Admin CS.', time: '', unread: 0 },
  ];

  isChatOpen = false;
  activeChatCustomer: any = null;
  chatMessages: any[] = [];
  newMessage: string = '';
  isTyping = false;
  typingTimeout: any = null;
  lastTypingSent = 0;

  isCsChatActive = false;
  csSessionId: string | null = null;

  constructor(
    private apiService: ApiService,
    private echoService: EchoService
  ) { }

  ngOnInit() {
  }

  ionViewWillEnter() {
    this.initTabSlideAnimation(2);
    this.loadData();
  }

  private initTabSlideAnimation(currentTabIndex: number) {
    const prev = localStorage.getItem('driver_active_tab_index');
    if (prev !== null) {
      const prevIndex = parseInt(prev, 10);
      if (prevIndex !== currentTabIndex) {
        const diff = prevIndex - currentTabIndex;
        this.slideDistance = `${diff * 25}vw`;
      }
    }
    localStorage.setItem('driver_active_tab_index', currentTabIndex.toString());
  }

  ngOnDestroy() {
    this.unsubscribeFromChat();
  }

  private getDateKey(date: Date): string {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
  }

  loadData() {
    this.isLoading = true;
    this.apiService.getDriverSchedules().subscribe({
      next: (schedules: any[]) => {
        const todayKey = this.getDateKey(new Date());
        const activeSchedules = schedules.filter(s => {
          return s.status !== 'completed' && this.getDateKey(new Date(s.departure_time)) === todayKey;
        });
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
    this.isCsChatActive = false;
    this.loadChatHistory();
    this.subscribeToChat();
  }

  openCsChat() {
    this.isCsChatActive = true;
    this.activeChatCustomer = { name: 'Admin Pusat' };
    this.isChatOpen = true;
    this.chatMessages = [];
    
    this.apiService.post('chat/connect-admin', {
      last_category_id: null,
      last_problem_id: null
    }).subscribe({
      next: (res: any) => {
        this.csSessionId = res.session.id;
        this.chatMessages = res.messages;
        this.subscribeToChat();
      },
      error: (err) => console.error(err)
    });
  }

  closeChat() {
    this.isChatOpen = false;
    this.unsubscribeFromChat();
    this.activeChatCustomer = null;
    this.isCsChatActive = false;
    this.csSessionId = null;
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
    if (this.isCsChatActive) {
      if (!this.csSessionId) return;
      this.echoService.getEcho()
        .private(`chat.${this.csSessionId}`)
        .listen('.App\\Events\\MessageSent', (e: any) => {
          if (e.sender_type !== 'user') {
            this.chatMessages.push(e);
          }
        })
        .listen('.App\\Events\\SessionStatusChanged', (e: any) => {
          if (e.status === 'resolved') {
            this.chatMessages.push({
              sender_type: 'system',
              message_content: 'Sesi chat ini telah diselesaikan oleh Admin.',
              created_at: new Date().toISOString()
            });
            this.csSessionId = null;
            this.echoService.getEcho().leave(`chat.${e.session_id}`);
          }
        });
      return;
    }

    if (!this.currentScheduleId || !this.activeChatCustomer?.customer_id) return;
    
    this.echoService.getEcho()
      .private(`chat.${this.currentScheduleId}.${this.activeChatCustomer.customer_id}`)
      .listen('.App\\Events\\DriverCustomerMessageSent', (e: any) => {
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
    if (this.isCsChatActive) {
      if (this.csSessionId) {
        this.echoService.getEcho().leave(`chat.${this.csSessionId}`);
      }
    } else {
      if (!this.currentScheduleId || !this.activeChatCustomer?.customer_id) return;
      this.echoService.getEcho().leave(`chat.${this.currentScheduleId}.${this.activeChatCustomer.customer_id}`);
    }
  }

  sendTypingEvent() {
    if (this.isCsChatActive) return; // Disable typing whisper for CS for now
    const now = Date.now();
    if (now - this.lastTypingSent < 1500) return;
    this.lastTypingSent = now;

    if (!this.currentScheduleId || !this.activeChatCustomer?.customer_id) return;

    this.echoService.getEcho()
      .private(`chat.${this.currentScheduleId}.${this.activeChatCustomer.customer_id}`)
      .whisper('typing', { typing: true });
  }

  sendMessage() {
    if (!this.newMessage.trim()) return;

    if (this.isCsChatActive) {
      if (!this.csSessionId) return;
      const text = this.newMessage.trim();
      this.newMessage = '';
      
      const optimisticMsg = { sender_type: 'user', message_content: text, created_at: new Date().toISOString() };
      this.chatMessages.push(optimisticMsg);
      
      this.apiService.post(`chat/${this.csSessionId}/messages`, { message: text }).subscribe({
        error: (err) => {
          console.error(err);
          this.newMessage = text;
          this.chatMessages = this.chatMessages.filter(m => m !== optimisticMsg);
        }
      });
      return;
    }

    if (!this.currentScheduleId || !this.activeChatCustomer?.customer_id) return;
    
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
