import { Component, OnInit, ViewChild } from '@angular/core';
import { IonContent, ToastController } from '@ionic/angular';
import { ApiService } from '../services/api.service';
import { EchoService } from '../services/echo.service';

interface ChatMessage {
  id?: number;
  sender_type: 'bot' | 'user' | 'admin' | 'system';
  message_content: string;
}

@Component({
  selector: 'app-chatbot',
  templateUrl: './chatbot.page.html',
  styleUrls: ['./chatbot.page.scss'],
  standalone: false,
})
export class ChatbotPage implements OnInit {
  @ViewChild('chatContent') chatContent!: IonContent;

  messages: ChatMessage[] = [];
  categories: any[] = [];
  problems: any[] = [];
  
  selectedCategory: any = null;
  selectedProblem: any = null;
  
  sessionId: string | null = null;
  userInput: string = '';
  isTyping: boolean = false;
  echoConnected: boolean = false;

  constructor(
    private apiService: ApiService,
    private echoService: EchoService,
    private toastCtrl: ToastController
  ) {}

  ngOnInit() {
    this.checkConnection();
    this.startBot();
  }

  ionViewWillLeave() {
    if (this.sessionId) {
      this.echoService.getEcho().leave(`chat.${this.sessionId}`);
    }
  }

  checkConnection() {
    const echo = this.echoService.getEcho();
    if (echo && echo.connector && echo.connector.pusher) {
      this.echoConnected = echo.connector.pusher.connection.state === 'connected';
      echo.connector.pusher.connection.bind('connected', () => {
        this.echoConnected = true;
      });
      echo.connector.pusher.connection.bind('disconnected', () => {
        this.echoConnected = false;
      });
    }
  }

  scrollToBottom() {
    setTimeout(() => {
      if (this.chatContent) {
        this.chatContent.scrollToBottom(300);
      }
    }, 100);
  }

  startBot() {
    this.messages = [];
    this.selectedCategory = null;
    this.selectedProblem = null;
    this.sessionId = null;
    
    this.appendBotMsg('Halo! Selamat datang di <b>Customer Service Shuttle System</b> 👋<br><br>Saya adalah asisten virtual Anda. Saya siap membantu menyelesaikan kendala seputar layanan shuttle Anda!');
    
    this.isTyping = true;
    this.scrollToBottom();
    
    setTimeout(() => {
      this.isTyping = false;
      this.appendBotMsg('Silakan pilih <b>kategori bantuan</b> yang sesuai dengan kendala Anda:');
      this.loadCategories();
    }, 800);
  }

  appendBotMsg(text: string) {
    this.messages.push({ sender_type: 'bot', message_content: text });
    this.scrollToBottom();
  }

  appendUserMsg(text: string) {
    this.messages.push({ sender_type: 'user', message_content: text });
    this.scrollToBottom();
  }

  appendSystemMsg(text: string) {
    this.messages.push({ sender_type: 'system', message_content: text });
    this.scrollToBottom();
  }

  async loadCategories() {
    try {
      const res = await this.apiService.get('chatbot/categories').toPromise();
      this.categories = res;
      this.scrollToBottom();
    } catch (e) {
      this.appendSystemMsg('Gagal memuat kategori. Periksa koneksi internet Anda.');
    }
  }

  async selectCategory(cat: any) {
    this.selectedCategory = cat;
    this.appendUserMsg(`Pilih Kategori: ${cat.name}`);
    this.categories = []; // Hide options

    const catNameLower = cat.name.toLowerCase();
    if (catNameLower.includes('customer service') || catNameLower.includes('hubungi')) {
      this.isTyping = true;
      this.scrollToBottom();
      setTimeout(() => {
        this.isTyping = false;
        this.appendBotMsg('Anda dapat menghubungkan langsung ke Admin CS Live Chat atau menghubungi kami via WhatsApp:');
        this.problems = [
          { title: 'Hubungkan ke Admin CS Live', isConnect: true },
          { title: 'Hubungi via WhatsApp', isWhatsApp: true }
        ];
        this.scrollToBottom();
      }, 600);
      return;
    }
    
    this.isTyping = true;
    this.scrollToBottom();
    
    setTimeout(async () => {
      this.isTyping = false;
      try {
        const res = await this.apiService.get(`chatbot/problems?category_id=${cat.id}`).toPromise();
        this.problems = res;
        if (this.problems.length === 0) {
          // If no problems defined, connect to admin directly
          this.connectToAdmin();
        } else {
          this.appendBotMsg(`Baik, terkait <b>${cat.name}</b>, spesifiknya apa yang ingin Anda tanyakan?`);
        }
        this.scrollToBottom();
      } catch (e) {
        this.appendSystemMsg('Gagal memuat pertanyaan.');
      }
    }, 600);
  }

  async selectProblem(prob: any) {
    this.selectedProblem = prob;
    this.appendUserMsg(prob.title);
    this.problems = []; // Hide options

    if (prob.isConnect) {
      this.connectToAdmin();
      return;
    }

    if (prob.isWhatsApp) {
      this.openWhatsApp();
      return;
    }

    if (prob.isFeedback) {
      this.handleFeedback(prob.value);
      return;
    }
    
    this.isTyping = true;
    this.scrollToBottom();
    
    setTimeout(() => {
      this.isTyping = false;

      const solution = (prob.solution_text || '').replace(/\n/g, '<br>').replace(/\*\*(.*?)\*\"/g, '<b>$1</b>');
      this.appendBotMsg(`Berikut adalah solusi untuk kendala Anda:<br><br><div style="padding:10px;background:rgba(255,255,255,0.2);border-radius:8px;border-left:3px solid #60a5fa;margin:8px 0;">${solution}</div>`);
      
      this.isTyping = true;
      this.scrollToBottom();
      setTimeout(() => {
        this.isTyping = false;
        this.appendBotMsg('Apakah Anda sudah puas dengan layanan kami?');
        
        // Show satisfaction options
        this.problems = [
          { title: 'Saya puas', isFeedback: true, value: 'yes' },
          { title: 'Saya tidak puas', isFeedback: true, value: 'no' }
        ];
        this.scrollToBottom();
      }, 1000);
    }, 600);
  }

  handleFeedback(value: string) {
    if (value === 'yes') {
      this.appendUserMsg('Saya puas ✅');
      this.isTyping = true;
      this.scrollToBottom();
      setTimeout(() => {
        this.isTyping = false;
        this.appendBotMsg('Terima kasih, senang membantu Anda! 😊');
        setTimeout(() => {
          this.resetFlow();
        }, 1500);
      }, 800);
    } else {
      this.appendUserMsg('Saya tidak puas ❌');
      this.isTyping = true;
      this.scrollToBottom();
      setTimeout(() => {
        this.isTyping = false;
        this.appendBotMsg('Mohon maaf atas ketidaknyamanannya. 🙏');
        
        this.isTyping = true;
        this.scrollToBottom();
        setTimeout(() => {
          this.isTyping = false;
          this.appendBotMsg('Untuk kendala ini, silakan hubungi langsung ke Admin CS Live Chat atau WhatsApp kami:');
          this.problems = [
            { title: 'Hubungkan ke Admin CS Live', isConnect: true },
            { title: 'Hubungi via WhatsApp', isWhatsApp: true }
          ];
          this.scrollToBottom();
        }, 1000);
      }, 800);
    }
  }

  openWhatsApp() {
    this.appendUserMsg('Hubungi via WhatsApp 📱');
    const waNumber = '62895324354052';
    const waMessage = 'Halo Admin, saya butuh bantuan terkait aplikasi Shuttle System.';
    const waUrl = `https://wa.me/${waNumber}?text=${encodeURIComponent(waMessage)}`;
    window.open(waUrl, '_blank');
    
    this.isTyping = true;
    this.scrollToBottom();
    setTimeout(() => {
      this.isTyping = false;
      this.appendBotMsg('Apakah ada hal lain yang bisa saya bantu?');
      this.problems = [];
      this.startBot();
    }, 1500);
  }

  resetFlow() {
    this.selectedCategory = null;
    this.selectedProblem = null;
    this.problems = [];
    this.appendUserMsg('Kembali ke Menu Utama');
    this.startBot();
  }

  async connectToAdmin() {
    if (this.selectedProblem && this.selectedProblem.isConnect) {
      this.appendUserMsg('Hubungkan ke Admin CS Live');
      this.problems = [];
    }
    
    this.isTyping = true;
    this.scrollToBottom();
    
    try {
      const payload = {
        last_category_id: this.selectedCategory ? this.selectedCategory.id : null,
        last_problem_id: this.selectedProblem && !this.selectedProblem.isConnect && !this.selectedProblem.isFeedback && !this.selectedProblem.isWhatsApp ? this.selectedProblem.id : null
      };
      
      const res: any = await this.apiService.post('chat/connect-admin', payload).toPromise();
      this.isTyping = false;
      this.sessionId = res.session.id;
      
      this.appendSystemMsg('Sesi Live Chat dimulai. Mohon tunggu balasan dari Admin CS.');
      
      // Subscribe to Reverb
      this.subscribeToChat();
      
    } catch (e) {
      this.isTyping = false;
      this.appendSystemMsg('Gagal menghubungi Admin. Coba lagi nanti.');
    }
  }

  subscribeToChat() {
    if (!this.sessionId) return;
    
    const echo = this.echoService.getEcho();
    
    echo.private(`chat.${this.sessionId}`)
      .listen('MessageSent', (e: any) => {
        if (e.sender_type !== 'user') {
          this.messages.push({
            sender_type: e.sender_type,
            message_content: e.message_content
          });
          this.scrollToBottom();
        }
      })
      .listen('SessionStatusChanged', (e: any) => {
        if (e.status === 'resolved') {
          this.appendSystemMsg('Sesi chat ini telah diselesaikan oleh Admin.');
          this.sessionId = null;
          echo.leave(`chat.${e.session_id}`);
          
          setTimeout(() => {
            this.resetFlow();
          }, 3000);
        }
      });
  }

  async sendMessage() {
    if (!this.userInput.trim() || !this.sessionId) return;
    
    const text = this.userInput.trim();
    this.userInput = '';
    
    // Optimistic append
    this.appendUserMsg(text);
    
    try {
      await this.apiService.post(`chat/${this.sessionId}/messages`, { message: text }).toPromise();
    } catch (e) {
      this.showToast('Gagal mengirim pesan');
    }
  }

  async endSession() {
    if (!this.sessionId) return;
    
    try {
      await this.apiService.post(`chat/${this.sessionId}/resolve`, {}).toPromise();
      this.sessionId = null;
      this.appendSystemMsg('Anda telah mengakhiri sesi chat.');
      setTimeout(() => {
        this.resetFlow();
      }, 2000);
    } catch (e) {
      this.showToast('Gagal mengakhiri sesi');
    }
  }

  async showToast(msg: string) {
    const toast = await this.toastCtrl.create({
      message: msg,
      duration: 2000,
      color: 'danger',
      position: 'bottom'
    });
    toast.present();
  }
}
