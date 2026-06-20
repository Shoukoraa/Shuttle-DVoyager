import { Component, OnInit, ViewChild } from '@angular/core';
import { IonContent, ToastController } from '@ionic/angular';
import { ApiService } from '../services/api.service';
import { EchoService } from '../services/echo.service';

interface ChatMessage {
  id?: number;
  sender_type: 'bot' | 'user' | 'admin' | 'system';
  message_content: string;
  timestamp?: string;
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

  getCurrentTime(): string {
    const now = new Date();
    const hh = String(now.getHours()).padStart(2, '0');
    const mm = String(now.getMinutes()).padStart(2, '0');
    return `${hh}:${mm}`;
  }

  startBot() {
    this.messages = [];
    this.selectedCategory = null;
    this.selectedProblem = null;
    this.sessionId = null;
    
    this.appendBotMsg('Halo! Terima kasih telah menghubungi D-Voyager dan selamat datang. Saya Kiperina, yang akan membantu menjawab pertanyaanmu hari ini! 👋');
    
    this.isTyping = true;
    this.scrollToBottom();
    
    setTimeout(() => {
      this.appendBotMsg('Sebagai informasi, obrolan ini ditujukan untuk pertanyaan umum, ya. Jika nanti kamu butuh bantuan lebih lanjut, jangan khawatir, saya akan langsung menghubungkanmu dengan Live Customer Service kami. 👤');
      this.scrollToBottom();
      
      setTimeout(() => {
        this.isTyping = false;
        this.appendBotMsg('Silakan pilih pertanyaan kamu di bawah ini. Setelah pertanyaanmu terkirim, mohon tunggu sebentar, ya! 👇');
        this.loadCategories();
      }, 1000);
    }, 1000);
  }

  appendBotMsg(text: string) {
    this.messages.push({ sender_type: 'bot', message_content: text, timestamp: this.getCurrentTime() });
    this.scrollToBottom();
  }

  appendUserMsg(text: string) {
    this.messages.push({ sender_type: 'user', message_content: text, timestamp: this.getCurrentTime() });
    this.scrollToBottom();
  }

  appendSystemMsg(text: string) {
    this.messages.push({ sender_type: 'system', message_content: text, timestamp: this.getCurrentTime() });
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
        this.appendBotMsg('Anda dapat menghubungi tim Customer Service kami secara langsung via WhatsApp:');
        this.problems = [
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
        let res: any[] = await this.apiService.get(`chatbot/problems?category_id=${cat.id}`).toPromise();
        
        // Add Ganti Password dynamically if Akun & Keamanan
        if (catNameLower.includes('akun') || catNameLower.includes('keamanan')) {
          const hasGanti = res.some(p => p.title.toLowerCase().includes('ganti'));
          if (!hasGanti) {
            res.push({
              id: -99,
              title: 'Ganti password',
              category_id: cat.id
            });
          }
        }
        
        this.problems = res;
        
        if (this.problems.length === 0) {
          this.openWhatsApp();
        } else {
          this.appendBotMsg('Silakan pilih pertanyaan kamu di bawah ini. Setelah pertanyaanmu terkirim, mohon tunggu sebentar, ya! 👇');
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

    if (prob.isReset) {
      this.resetFlow();
      return;
    }

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

      const catName = this.selectedCategory ? this.selectedCategory.name.toLowerCase() : '';
      const probTitle = prob.title.toLowerCase();
      
      // 1. Akun & Keamanan -> Lupa Password
      if (probTitle.includes('lupa') && (probTitle.includes('password') || probTitle.includes('sandi'))) {
        this.appendBotMsg('Baik, kami akan langsung menghubungkanmu dengan Customer Service untuk membantu kendala lupa password-mu. Silakan klik tombol di bawah ini, ya:');
        this.isTyping = true;
        this.scrollToBottom();
        setTimeout(() => {
          this.isTyping = false;
          this.problems = [
            { title: 'Hubungi via WhatsApp', isWhatsApp: true }
          ];
          this.scrollToBottom();
        }, 1000);
        return;
      }

      // 2. Akun & Keamanan -> Ganti Password
      if (probTitle.includes('ganti') && probTitle.includes('password')) {
        const solution = `Berikut adalah panduan mudah untuk mengganti password akunmu:\n\n1️⃣ Masuk ke menu Profil\n2️⃣ Pilih Edit Profil -> Ganti Password\n3️⃣ Masukkan Password Lama\n4️⃣ Masukkan Password Baru & Konfirmasi Password Baru\n5️⃣ Klik Simpan Password\n\nSelesai! Gampang, kan? 😉`;
        this.appendBotMsg(solution.replace(/\n/g, '<br>'));
        
        this.isTyping = true;
        this.scrollToBottom();
        setTimeout(() => {
          this.isTyping = false;
          this.appendBotMsg('Apakah anda sudah puas dengan layanan kami?');
          this.problems = [
            { title: 'Saya puas', isFeedback: true, value: 'yes' },
            { title: 'Saya tidak puas', isFeedback: true, value: 'no' }
          ];
          this.scrollToBottom();
        }, 1000);
        return;
      }

      // 3. Pembatalan & Refund
      if (catName.includes('pembatalan') || catName.includes('refund') || probTitle.includes('batal') || probTitle.includes('refund')) {
        const solution = "Kamu dapat membatalkan pesanan secara mandiri melalui halaman 'Pesanan Saya' maksimal 2 jam sebelum keberangkatan, ya. Dana akan dikembalikan ke saldo dompet digitalmu sesuai dengan syarat dan ketentuan yang berlaku. 💳";
        this.appendBotMsg(solution);
        
        this.isTyping = true;
        this.scrollToBottom();
        setTimeout(() => {
          this.isTyping = false;
          this.appendBotMsg('Apakah anda sudah puas dengan layanan kami?');
          this.problems = [
            { title: 'Saya puas', isFeedback: true, value: 'yes' },
            { title: 'Saya tidak puas', isFeedback: true, value: 'no' }
          ];
          this.scrollToBottom();
        }, 1000);
        return;
      }

      // Semua kategori lainnya (Kendala Pembayaran, Barang Tertinggal, dll.):
      let csMessage = 'Berikut adalah solusi untuk kendala Anda:<br><br>Kami akan menyambungkan Anda ke Customer Service untuk memproses kendala Anda.';
      if (catName.includes('pembayaran') || catName.includes('biaya')) {
        csMessage = 'Berikut adalah solusi untuk kendala Anda:<br><br>Kami akan menyambungkan Anda ke Customer Service untuk memproses Kendala Pembayaran Anda.';
      } else if (catName.includes('barang') || catName.includes('tertinggal') || catName.includes('hilang')) {
        csMessage = 'Berikut adalah solusi untuk kendala Anda:<br><br>Kami akan menyambungkan Anda ke Customer Service untuk memproses laporan Barang Tertinggal Anda.';
      }

      this.appendBotMsg(csMessage);
      
      this.isTyping = true;
      this.scrollToBottom();
      setTimeout(() => {
        this.isTyping = false;
        this.problems = [
          { title: 'Hubungi via WhatsApp', isWhatsApp: true }
        ];
        this.scrollToBottom();
      }, 1000);
    }, 600);
  }

  handleFeedback(value: string) {
    if (value === 'yes') {
      this.isTyping = true;
      this.scrollToBottom();
      setTimeout(() => {
        this.isTyping = false;
        this.appendBotMsg('Terima kasih banyak atas penilaianmu! Senang bisa membantu. Semoga perjalananmu menyenangkan bersama D-Voyager! ✨');
        
        this.isTyping = true;
        this.scrollToBottom();
        setTimeout(() => {
          this.isTyping = false;
          this.problems = [
            { title: 'Akhiri Pembicaraan', isReset: true }
          ];
          this.scrollToBottom();
        }, 1000);
      }, 800);
    } else {
      this.isTyping = true;
      this.scrollToBottom();
      setTimeout(() => {
        this.isTyping = false;
        this.appendBotMsg('Mohon maaf atas ketidaknyamanannya, ya. 🙏 Agar kendalamu bisa segera ditangani secara langsung, yuk hubungi staf kami melalui tautan berikut:');
        
        this.isTyping = true;
        this.scrollToBottom();
        setTimeout(() => {
          this.isTyping = false;
          this.problems = [
            { title: 'Hubungi via WhatsApp', isWhatsApp: true }
          ];
          this.scrollToBottom();
        }, 1000);
      }, 800);
    }
  }

  openWhatsApp() {
    const waNumber = '62895324354052';
    const waMessage = 'Halo Admin, saya butuh bantuan terkait D-Voyager.';
    const waUrl = `https://wa.me/${waNumber}?text=${encodeURIComponent(waMessage)}`;
    window.open(waUrl, '_blank');
    
    this.isTyping = true;
    this.scrollToBottom();
    setTimeout(() => {
      this.isTyping = false;
      this.appendBotMsg('Terima kasih banyak atas penilaianmu! Senang bisa membantu. Semoga perjalananmu menyenangkan bersama D-Voyager! ✨');
      
      this.isTyping = true;
      this.scrollToBottom();
      setTimeout(() => {
        this.isTyping = false;
        this.problems = [
          { title: 'Akhiri Pembicaraan', isReset: true }
        ];
        this.scrollToBottom();
      }, 1000);
    }, 1500);
  }

  resetFlow() {
    this.selectedCategory = null;
    this.selectedProblem = null;
    this.problems = [];
    this.appendUserMsg('Kembali ke Menu Utama');
    this.startBot();
  }

  shouldShowOutlineReset(): boolean {
    if (!this.problems || this.problems.length === 0) return false;
    return !this.problems.some(p => p.isReset || p.isWhatsApp || p.isFeedback);
  }

  isInputDisabled(): boolean {
    if (this.sessionId) return false;
    if (this.selectedCategory) return true;
    return false;
  }

  getInputPlaceholder(): string {
    if (this.sessionId) {
      return 'Ketik pesan ke Admin...';
    }
    if (this.isInputDisabled()) {
      return 'Silakan selesaikan kendala Anda di atas...';
    }
    return 'Tanya ke Kiperina...';
  }

  handleBotInput(text: string) {
    const input = text.toLowerCase();
    this.isTyping = true;
    this.scrollToBottom();

    setTimeout(() => {
      this.isTyping = false;

      // 1. Small talk: Halo / Salam
      if (input.match(/\b(halo|hai|hey|p|assalamualaikum|selamat|pagi|siang|sore|malam)\b/)) {
        this.appendBotMsg('Halo! Ada yang bisa Kiperina bantu hari ini? Silakan ketik langsung kendala Anda (misal: "lupa password", "batal tiket") atau pilih kategori di bawah. 😊');
        this.scrollToBottom();
        return;
      }

      // 2. Small talk: Terima kasih
      if (input.includes('terima kasih') || input.includes('makasih') || input.includes('thanks') || input.includes('thank you')) {
        this.appendBotMsg('Sama-sama! Kiperina senang bisa membantu Anda. Semoga perjalanan Anda menyenangkan bersama D-Voyager! ✨');
        this.scrollToBottom();
        return;
      }

      // 3. Keyword: Batal / Refund / Cancel
      if (input.includes('batal') || input.includes('refund') || input.includes('cancel')) {
        this.problems = []; // Hide options
        const cat = this.categories && this.categories.find(c => c.name.toLowerCase().includes('batal') || c.name.toLowerCase().includes('refund'));
        if (cat) {
          this.selectedCategory = cat;
        }
        
        const solution = "Kamu dapat membatalkan pesanan secara mandiri melalui halaman 'Pesanan Saya' maksimal 2 jam sebelum keberangkatan, ya. Dana akan dikembalikan ke saldo dompet digitalmu sesuai dengan syarat dan ketentuan yang berlaku. 💳";
        this.appendBotMsg(`Berikut adalah solusi untuk kendala Anda:<br><br><div style="padding:10px;background:rgba(255,255,255,0.2);border-radius:8px;border-left:3px solid #60a5fa;margin:8px 0;">${solution}</div>`);
        
        this.isTyping = true;
        this.scrollToBottom();
        setTimeout(() => {
          this.isTyping = false;
          this.appendBotMsg('Apakah anda sudah puas dengan layanan kami?');
          this.problems = [
            { title: 'Saya puas', isFeedback: true, value: 'yes' },
            { title: 'Saya tidak puas', isFeedback: true, value: 'no' }
          ];
          this.scrollToBottom();
        }, 1000);
        return;
      }

      // 4. Keyword: Lupa password
      if (input.includes('lupa') && (input.includes('password') || input.includes('sandi') || input.includes('akun'))) {
        this.problems = [];
        const cat = this.categories && this.categories.find(c => c.name.toLowerCase().includes('akun') || c.name.toLowerCase().includes('keamanan'));
        if (cat) this.selectedCategory = cat;

        this.appendBotMsg('Baik, kami akan langsung menghubungkanmu dengan Customer Service untuk membantu kendala lupa password-mu. Silakan klik tombol di bawah ini, ya:');
        this.problems = [
          { title: 'Hubungi via WhatsApp', isWhatsApp: true }
        ];
        this.scrollToBottom();
        return;
      }

      // 5. Keyword: Ganti password / Ubah password
      if (input.includes('ganti') || input.includes('ubah')) {
        if (input.includes('password') || input.includes('sandi') || input.includes('akun')) {
          this.problems = [];
          const cat = this.categories && this.categories.find(c => c.name.toLowerCase().includes('akun') || c.name.toLowerCase().includes('keamanan'));
          if (cat) this.selectedCategory = cat;

          const solution = `Berikut adalah panduan mudah untuk mengganti password akunmu:\n\n1️⃣ Masuk ke menu Profil\n2️⃣ Pilih Edit Profil -> Ganti Password\n3️⃣ Masukkan Password Lama\n4️⃣ Masukkan Password Baru & Konfirmasi Password Baru\n5️⃣ Klik Simpan Password\n\nSelesai! Gampang, kan? 😉`;
          this.appendBotMsg(`Berikut adalah solusi untuk kendala Anda:<br><br><div style="padding:10px;background:rgba(255,255,255,0.2);border-radius:8px;border-left:3px solid #60a5fa;margin:8px 0;">${solution.replace(/\n/g, '<br>')}</div>`);
          
          this.isTyping = true;
          this.scrollToBottom();
          setTimeout(() => {
            this.isTyping = false;
            this.appendBotMsg('Apakah anda sudah puas dengan layanan kami?');
            this.problems = [
              { title: 'Saya puas', isFeedback: true, value: 'yes' },
              { title: 'Saya tidak puas', isFeedback: true, value: 'no' }
            ];
            this.scrollToBottom();
          }, 1000);
          return;
        }
      }

      // 6. Keyword: Akun / Keamanan
      if (input.includes('akun') || input.includes('keamanan') || input.includes('sandi') || input.includes('password')) {
        this.problems = [];
        const cat = this.categories && this.categories.find(c => c.name.toLowerCase().includes('akun') || c.name.toLowerCase().includes('keamanan'));
        if (cat) {
          this.selectCategory(cat);
        } else {
          this.appendBotMsg('Saya dapat mendeteksi masalah akun Anda. Silakan pilih sub-kategori di bawah ini:');
          this.problems = [
            { id: 1, title: 'Lupa Password', category_id: 3 },
            { id: -99, title: 'Ganti password', category_id: 3 }
          ];
        }
        this.scrollToBottom();
        return;
      }

      // 7. Keyword: Bayar / Pembayaran / Transfer
      if (input.includes('bayar') || input.includes('pembayaran') || input.includes('transfer') || input.includes('biaya')) {
        this.problems = [];
        const cat = this.categories && this.categories.find(c => c.name.toLowerCase().includes('bayar') || c.name.toLowerCase().includes('pembayaran'));
        if (cat) {
          this.selectedCategory = cat;
        }
        this.appendBotMsg('Berikut adalah solusi untuk kendala Anda:<br><br>Kami akan menyambungkan Anda ke Customer Service untuk memproses Kendala Pembayaran Anda.');
        this.problems = [
          { title: 'Hubungi via WhatsApp', isWhatsApp: true }
        ];
        this.scrollToBottom();
        return;
      }

      // 8. Keyword: Barang / Hilang / Tertinggal
      if (input.includes('barang') || input.includes('hilang') || input.includes('tertinggal') || input.includes('ketinggalan')) {
        this.problems = [];
        const cat = this.categories && this.categories.find(c => c.name.toLowerCase().includes('barang') || c.name.toLowerCase().includes('tertinggal'));
        if (cat) {
          this.selectedCategory = cat;
        }
        this.appendBotMsg('Berikut adalah solusi untuk kendala Anda:<br><br>Kami akan menyambungkan Anda ke Customer Service untuk memproses laporan Barang Tertinggal Anda.');
        this.problems = [
          { title: 'Hubungi via WhatsApp', isWhatsApp: true }
        ];
        this.scrollToBottom();
        return;
      }

      // 9. Keyword: CS / Customer Service / Hubungi / WhatsApp / Admin
      if (input.includes('cs') || input.includes('customer service') || input.includes('hubungi') || input.includes('whatsapp') || input.includes('wa') || input.includes('admin')) {
        this.problems = [];
        this.appendBotMsg('Anda dapat menghubungi tim Customer Service kami secara langsung via WhatsApp:');
        this.problems = [
          { title: 'Hubungi via WhatsApp', isWhatsApp: true }
        ];
        this.scrollToBottom();
        return;
      }

      // Fallback
      this.appendBotMsg('Maaf, Kiperina belum memahami pesan Anda. Silakan ketik kata kunci yang lebih spesifik (seperti "batal", "bayar", "password", "barang", "customer service") atau gunakan tombol menu yang tersedia. 🙏');
      this.scrollToBottom();
    }, 800);
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
      .listen('.App\\Events\\MessageSent', (e: any) => {
        if (e.sender_type !== 'user') {
          this.messages.push({
            sender_type: e.sender_type,
            message_content: e.message_content,
            timestamp: this.getCurrentTime()
          });
          this.scrollToBottom();
        }
      })
      .listen('.App\\Events\\SessionStatusChanged', (e: any) => {
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
    if (this.isInputDisabled()) return;

    const text = this.userInput.trim();
    if (!text) return;
    this.userInput = '';
    
    // Optimistic append
    this.appendUserMsg(text);
    
    if (this.sessionId) {
      try {
        await this.apiService.post(`chat/${this.sessionId}/messages`, { message: text }).toPromise();
      } catch (e) {
        this.showToast('Gagal mengirim pesan');
      }
    } else {
      this.handleBotInput(text);
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
