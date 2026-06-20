import { Component, OnInit, OnDestroy, HostListener } from '@angular/core';
import { Router } from '@angular/router';
import { ToastController, AlertController, Platform } from '@ionic/angular';
import { ApiService } from '../services/api.service';
import { EchoService } from '../services/echo.service';
import { Subscription } from 'rxjs';

declare var mapboxgl: any;

@Component({
  selector: 'app-tickets',
  templateUrl: './tickets.page.html',
  styleUrls: ['./tickets.page.scss'],
  standalone: false
})
export class TicketsPage implements OnInit, OnDestroy {

  // Tracking properties
  isTrackingOpen = false;
  trackingTicket: any = null;
  private trackingMap: any = null;
  private driverTrackingMarker: any = null;
  private backButtonSubscription: Subscription | null = null;
  private resumeSubscription: Subscription | null = null;

  hasTickets: boolean = false;
  tickets: any[] = [];
  isLoading: boolean = false;
  showPaymentSuccessMascot = false;
  isPaymentSuccessLeaving = false;
  private paymentSuccessHideTimer: any = null;
  private paymentSuccessRemoveTimer: any = null;
  selectedTab: 'aktif' | 'riwayat' = 'aktif';
  isRatingOpen = false;
  ratingTicket: any = null;
  selectedRating = 0;
  ratingComment = '';
  isSubmittingRating = false;
  readonly ratingOptions = [1, 2, 3, 4, 5];

  @HostListener('window:focus')
  onWindowFocus() {
    if (sessionStorage.getItem('pendingPaymentBookingId')) {
      this.fetchMyBookings();
    }
  }

  constructor(
    private apiService: ApiService,
    private router: Router,
    private toastController: ToastController,
    private alertController: AlertController,
    private echoService: EchoService,
    private platform: Platform
  ) { }

  ngOnInit() {
    const role = localStorage.getItem('user_role');
    if (role === 'driver') {
      this.router.navigate(['/driver-home'], { replaceUrl: true });
    }
  }

  public slideDistance = '0px';

  ionViewWillEnter() {
    this.initTabSlideAnimation(1);

    const role = localStorage.getItem('user_role');
    if (role === 'driver') {
      this.router.navigate(['/driver-home'], { replaceUrl: true });
      return;
    }

    this.resumeSubscription = this.platform.resume.subscribe(() => {
      if (sessionStorage.getItem('pendingPaymentBookingId')) {
        this.fetchMyBookings();
      }
    });

    const shouldShowPaymentSuccess = sessionStorage.getItem('showPaymentSuccessMascot') === '1';
    if (shouldShowPaymentSuccess) {
      this.selectedTab = 'aktif';
      sessionStorage.removeItem('showPaymentSuccessMascot');
      this.presentPaymentSuccessMascot();
    }
    this.fetchMyBookings();
  }

  ionViewWillLeave() {
    this.clearBackButtonHandler();
    this.clearPaymentSuccessTimers();
    if (this.resumeSubscription) {
      this.resumeSubscription.unsubscribe();
      this.resumeSubscription = null;
    }
  }

  private initTabSlideAnimation(currentTabIndex: number) {
    const prev = localStorage.getItem('active_tab_index');
    if (prev !== null) {
      const prevIndex = parseInt(prev, 10);
      if (prevIndex !== currentTabIndex) {
        const diff = prevIndex - currentTabIndex;
        this.slideDistance = `${diff * 25}vw`;
      }
    }
    localStorage.setItem('active_tab_index', currentTabIndex.toString());
  }

  fetchMyBookings() {
    // Load cached bookings first for an instant user interface response
    const cached = localStorage.getItem('cached_bookings');
    if (cached) {
      try {
        const parsed = JSON.parse(cached);
        this.tickets = Array.isArray(parsed) ? parsed : Object.values(parsed || {});
        this.hasTickets = this.tickets.length > 0;
      } catch (e) {
        console.error('Failed to parse cached bookings', e);
      }
    }

    // Only display full-page loading spinner if we don't have cached data
    if (this.tickets.length === 0) {
      this.isLoading = true;
    }

    this.apiService.getMyBookings().subscribe({
      next: (data: any) => {
        const bookingsArray = Array.isArray(data) ? data : Object.values(data || {});
        this.tickets = bookingsArray;
        this.hasTickets = this.tickets.length > 0;
        this.isLoading = false;
        
        // Save the fresh bookings to cache
        localStorage.setItem('cached_bookings', JSON.stringify(bookingsArray));

        // Check for pending payment status
        const pendingBookingId = sessionStorage.getItem('pendingPaymentBookingId');
        if (pendingBookingId) {
          const targetTicket = this.tickets.find(t => t.id === Number(pendingBookingId));
          if (targetTicket) {
            const ticketStatus = (targetTicket.status || '').toLowerCase();
            if (ticketStatus === 'paid') {
              this.selectedTab = 'aktif';
              this.presentPaymentSuccessMascot();
              sessionStorage.removeItem('pendingPaymentBookingId');
            } else if (['cancelled', 'canceled', 'completed'].includes(ticketStatus)) {
              sessionStorage.removeItem('pendingPaymentBookingId');
            }
          } else {
            sessionStorage.removeItem('pendingPaymentBookingId');
          }
        }
      },
      error: (err) => {
        console.error('Failed to fetch bookings', err);
        this.isLoading = false;
        // If there's no cache available, ensure empty state is shown properly
        if (this.tickets.length === 0) {
          this.hasTickets = false;
        }
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

  private presentPaymentSuccessMascot() {
    this.clearPaymentSuccessTimers(false);
    this.isPaymentSuccessLeaving = false;
    this.showPaymentSuccessMascot = true;

    this.paymentSuccessHideTimer = setTimeout(() => {
      this.dismissPaymentSuccessMascot();
    }, 3000);
  }

  dismissPaymentSuccessMascot() {
    if (!this.showPaymentSuccessMascot || this.isPaymentSuccessLeaving) {
      return;
    }

    if (this.paymentSuccessHideTimer) {
      clearTimeout(this.paymentSuccessHideTimer);
      this.paymentSuccessHideTimer = null;
    }

    this.isPaymentSuccessLeaving = true;
    this.paymentSuccessRemoveTimer = setTimeout(() => {
      this.showPaymentSuccessMascot = false;
      this.isPaymentSuccessLeaving = false;
      this.paymentSuccessRemoveTimer = null;
    }, 360);
  }

  private clearPaymentSuccessTimers(resetState: boolean = true) {
    if (this.paymentSuccessHideTimer) {
      clearTimeout(this.paymentSuccessHideTimer);
      this.paymentSuccessHideTimer = null;
    }

    if (this.paymentSuccessRemoveTimer) {
      clearTimeout(this.paymentSuccessRemoveTimer);
      this.paymentSuccessRemoveTimer = null;
    }

    if (!resetState) {
      return;
    }

    this.showPaymentSuccessMascot = false;
    this.isPaymentSuccessLeaving = false;
  }

  isCancelModalOpen = false;
  ticketToCancel: any = null;

  cancelBooking(ticket: any) {
    this.ticketToCancel = ticket;
    this.isCancelModalOpen = true;
  }

  closeCancelModal() {
    this.isCancelModalOpen = false;
    setTimeout(() => {
      this.ticketToCancel = null;
    }, 300);
  }

  confirmCancel() {
    if (!this.ticketToCancel) return;
    this.isLoading = true;
    this.isCancelModalOpen = false;
    this.apiService.cancelBooking(this.ticketToCancel.id).subscribe({
      next: () => {
        this.presentToast('Pesanan berhasil dibatalkan', 'success');
        this.fetchMyBookings();
        this.ticketToCancel = null;
      },
      error: (err) => {
        console.error(err);
        this.isLoading = false;
        this.presentToast(err.error?.message || 'Gagal membatalkan pesanan', 'danger');
        this.ticketToCancel = null;
      }
    });
  }

  canRate(ticket: any): boolean {
    const isCompleted = (ticket?.status || '').toLowerCase() === 'completed';
    return this.isHistoryTicket(ticket) && !ticket?.review && !!ticket?.schedule?.driver_id && isCompleted;
  }

  getStatusColor(status: string): string {
    const s = (status || '').toLowerCase();
    if (s === 'paid' || s === 'completed') return '#10dc60'; // Success green
    if (s === 'cancelled' || s === 'canceled') return '#eb445a'; // Danger red
    if (s === 'on_the_way') return '#3880ff'; // Primary blue
    return '#ffc409'; // Warning yellow for booked
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
    const icon = color === 'success' ? 'checkmark-circle' :
                 color === 'danger' ? 'close-circle' :
                 color === 'warning' ? 'warning' : 'information-circle';

    const toast = await this.toastController.create({
      message,
      duration: 1100,
      cssClass: `premium-toast toast-${color}`,
      position: 'top',
      icon
    });

    await toast.present();
  }

  // --- Chat Features ---
  isChatOpen = false;
  activeChatTicket: any = null;
  chatMessages: any[] = [];
  newMessage: string = '';
  isTyping = false;
  typingTimeout: any = null;
  lastTypingSent = 0;

  ngOnDestroy() {
    this.unsubscribeFromChat();
    this.clearBackButtonHandler();
    this.clearPaymentSuccessTimers();
  }

  private registerBackButtonHandler(handler: () => void) {
    this.clearBackButtonHandler();
    this.backButtonSubscription = this.platform.backButton.subscribeWithPriority(10001, () => {
      handler();
    });
  }

  private clearBackButtonHandler() {
    if (this.backButtonSubscription) {
      this.backButtonSubscription.unsubscribe();
      this.backButtonSubscription = null;
    }
  }

  openChat(ticket: any) {
    this.activeChatTicket = ticket;
    this.isChatOpen = true;
    this.loadChatHistory();
    this.subscribeToChat();
    this.registerBackButtonHandler(() => this.closeChat());
  }

  closeChat() {
    this.isChatOpen = false;
    this.unsubscribeFromChat();
    this.activeChatTicket = null;
    this.isTyping = false;
    if (this.typingTimeout) {
      clearTimeout(this.typingTimeout);
    }
    this.clearBackButtonHandler();
  }

  loadChatHistory() {
    if (!this.activeChatTicket?.schedule_id) return;
    this.apiService.getChatMessages(this.activeChatTicket.schedule_id).subscribe({
      next: (msgs) => this.chatMessages = msgs,
      error: (err) => console.error(err)
    });
  }

  subscribeToChat() {
    if (!this.activeChatTicket?.schedule_id || !this.activeChatTicket?.customer_id) return;
    
    const scheduleId = this.activeChatTicket.schedule_id;
    const customerId = this.activeChatTicket.customer_id;

    this.echoService.getEcho()
      .private(`chat.${scheduleId}.${customerId}`)
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
    if (!this.activeChatTicket?.schedule_id || !this.activeChatTicket?.customer_id) return;
    const scheduleId = this.activeChatTicket.schedule_id;
    const customerId = this.activeChatTicket.customer_id;
    
    this.echoService.getEcho().leave(`chat.${scheduleId}.${customerId}`);
  }

  sendTypingEvent() {
    const now = Date.now();
    if (now - this.lastTypingSent < 1500) return;
    this.lastTypingSent = now;

    if (!this.activeChatTicket?.schedule_id || !this.activeChatTicket?.customer_id) return;
    const scheduleId = this.activeChatTicket.schedule_id;
    const customerId = this.activeChatTicket.customer_id;

    this.echoService.getEcho()
      .private(`chat.${scheduleId}.${customerId}`)
      .whisper('typing', { typing: true });
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

  openTracking(ticket: any) {
    this.trackingTicket = ticket;
    this.isTrackingOpen = true;
    this.registerBackButtonHandler(() => this.closeTracking());

    // Mulai inisialisasi peta
    setTimeout(() => this.initTrackingMap(), 500);

    // Subscribe ke channel Reverb privat untuk trip/schedule ini
    const scheduleId = ticket.schedule_id;
    this.echoService.getEcho()
      .private(`schedules.${scheduleId}`)
      .listen('.App\\Events\\DriverLocationUpdated', (e: any) => {
        console.log('Update lokasi supir real-time diterima:', e);
        this.updateDriverTrackingPosition(e.latitude, e.longitude);
      });
  }

  closeTracking() {
    this.clearBackButtonHandler();

    if (this.trackingTicket) {
      const scheduleId = this.trackingTicket.schedule_id;
      this.echoService.getEcho().leave(`schedules.${scheduleId}`);
    }

    if (this.trackingMap) {
      try {
        this.trackingMap.remove();
      } catch (e) {}
      this.trackingMap = null;
    }
    this.driverTrackingMarker = null;
    this.isTrackingOpen = false;
    this.trackingTicket = null;
    this.isMapFullscreen = false;
  }

  public currentStyle: string = 'dark';
  public isMapFullscreen: boolean = false;

  initTrackingMap() {
    mapboxgl.accessToken = 'pk.eyJ1IjoibW9yZW4tNjciLCJhIjoiY21vam1pbWxuMDA0bDJxb2xkZTBnM2s3cSJ9.wUfxEG062R3T-AZr_m9Fvw';
    if (!this.trackingTicket) return;

    if (this.trackingMap) {
      try {
        this.trackingMap.remove();
      } catch (e) {}
      this.trackingMap = null;
    }

    const route = this.trackingTicket.schedule?.route;
    const origin = route?.origin;
    const destination = route?.destination;

    let center = [110.3785, -7.7970]; // Default Yogyakarta
    if (origin && origin.longitude && origin.latitude) {
      center = [Number(origin.longitude), Number(origin.latitude)];
    }

    const styleUrls: Record<string, string> = {
      'streets': 'mapbox://styles/mapbox/streets-v12',
      'dark': 'mapbox://styles/mapbox/dark-v11',
      'satellite': 'mapbox://styles/mapbox/satellite-streets-v12'
    };

    try {
      this.trackingMap = new mapboxgl.Map({
        container: 'customer-map',
        style: styleUrls[this.currentStyle] || styleUrls['dark'], // Tema default
        center: center,
        zoom: 10,
        pitch: 35
      });

      this.trackingMap.on('load', () => {
        this.drawRouteOnCustomerMap();

        // Tampilkan marker supir jika sudah ada data lokasi awal dari eager loading
        const locations = this.trackingTicket.schedule?.locations;
        if (locations && locations.length > 0) {
          const latestLoc = locations[0];
          this.updateDriverTrackingPosition(Number(latestLoc.latitude), Number(latestLoc.longitude));
        } else {
          // Jika belum ada lokasi GPS driver, tampilkan di koordinat asal dulu
          if (origin && origin.longitude && origin.latitude) {
            this.updateDriverTrackingPosition(Number(origin.latitude), Number(origin.longitude));
          }
        }
      });
    } catch (e) {
      console.error('Gagal memuat peta Mapbox customer:', e);
    }
  }

  setMapStyle(style: string) {
    this.currentStyle = style;
    const styleUrls: Record<string, string> = {
      'streets': 'mapbox://styles/mapbox/streets-v12',
      'dark': 'mapbox://styles/mapbox/dark-v11',
      'satellite': 'mapbox://styles/mapbox/satellite-streets-v12'
    };
    if (this.trackingMap) {
      this.trackingMap.setStyle(styleUrls[style]);
      
      this.trackingMap.once('style.load', () => {
        this.drawRouteOnCustomerMap();
        if (this.driverTrackingMarker) {
          const coords = this.driverTrackingMarker.getLngLat();
          // Force redraw driver marker without moving camera
          this.updateDriverTrackingPosition(coords.lat, coords.lng, false);
        }
      });
    }
  }

  toggleMapFullscreen() {
    this.isMapFullscreen = !this.isMapFullscreen;

    if (this.isMapFullscreen) {
      this.registerBackButtonHandler(() => this.toggleMapFullscreen());
    } else {
      if (this.isTrackingOpen) {
        this.registerBackButtonHandler(() => this.closeTracking());
      } else {
        this.clearBackButtonHandler();
      }
    }

    setTimeout(() => {
      if (this.trackingMap) {
        this.trackingMap.resize();
        if (this.driverTrackingMarker) {
          const coords = this.driverTrackingMarker.getLngLat();
          this.trackingMap.flyTo({ center: coords, zoom: this.isMapFullscreen ? 14 : 10 });
        }
      }
    }, 200);
  }

  drawRouteOnCustomerMap() {
    if (!this.trackingMap || !this.trackingTicket) return;

    const route = this.trackingTicket.schedule?.route;
    const origin = route?.origin;
    const destination = route?.destination;

    if (!origin || !destination) return;

    const start = [Number(origin.longitude), Number(origin.latitude)];
    const end = [Number(destination.longitude), Number(destination.latitude)];

    // Tambahkan marker Halte Asal dan Halte Tujuan
    new mapboxgl.Marker({ color: '#3880ff' }) // Biru untuk asal
      .setLngLat(start)
      .addTo(this.trackingMap);

    new mapboxgl.Marker({ color: '#10dc60' }) // Hijau untuk tujuan
      .setLngLat(end)
      .addTo(this.trackingMap);

    // Ambil Directions dari Mapbox API
    const url = `https://api.mapbox.com/directions/v5/mapbox/driving/${start[0]},${start[1]};${end[0]},${end[1]}?geometries=geojson&overview=full&access_token=${mapboxgl.accessToken}`;

    fetch(url)
      .then(res => res.json())
      .then(data => {
        if (!data.routes[0]) return;
        const routeCoords = data.routes[0].geometry;

        if (this.trackingMap.getSource('route')) {
          this.trackingMap.getSource('route').setData({
            type: 'Feature',
            geometry: routeCoords
          });
        } else {
          this.trackingMap.addSource('route', {
            type: 'geojson',
            data: {
              type: 'Feature',
              properties: {},
              geometry: routeCoords
            }
          });

          this.trackingMap.addLayer({
            id: 'route',
            type: 'line',
            source: 'route',
            layout: {
              'line-join': 'round',
              'line-cap': 'round'
            },
            paint: {
              'line-color': '#FBC02D', // Garis kuning amber premium
              'line-width': 4,
              'line-opacity': 0.75
            }
          });
        }

        // Atur zoom peta agar memuat rute dan posisi driver
        const bounds = new mapboxgl.LngLatBounds();
        bounds.extend(start);
        bounds.extend(end);
        
        const locations = this.trackingTicket.schedule?.locations;
        if (locations && locations.length > 0) {
          bounds.extend([Number(locations[0].longitude), Number(locations[0].latitude)]);
        }

        this.trackingMap.fitBounds(bounds, { padding: 50 });
      })
      .catch(err => console.error('Gagal mengambil rute:', err));
  }

  updateDriverTrackingPosition(lat: number, lng: number, moveCamera: boolean = true) {
    if (!this.trackingMap) return;

    if (this.driverTrackingMarker) {
      this.driverTrackingMarker.setLngLat([lng, lat]);
    } else {
      // Buat marker custom mobil supir
      const el = document.createElement('div');
      el.style.width = '32px';
      el.style.height = '32px';
      el.style.backgroundImage = 'url("https://cdn-icons-png.flaticon.com/512/3448/3448339.png")';
      el.style.backgroundSize = 'cover';
      el.style.filter = 'drop-shadow(0 2px 4px rgba(0,0,0,0.3))';

      this.driverTrackingMarker = new mapboxgl.Marker(el)
        .setLngLat([lng, lat])
        .addTo(this.trackingMap);
    }

    // Gerakkan kamera mengikuti supir dengan zoom yang pas
    if (moveCamera) {
      this.trackingMap.easeTo({ center: [lng, lat], zoom: this.isMapFullscreen ? 14 : 13, duration: 1000 });
    }
  }
}
