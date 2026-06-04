import { Component, OnInit, OnDestroy } from '@angular/core';
import { ToastController, Platform } from '@ionic/angular';
import { ApiService } from '../services/api.service';
import { Geolocation } from '@capacitor/geolocation';
import { Subscription } from 'rxjs';

declare var mapboxgl: any;

@Component({
  selector: 'app-driver-home',
  templateUrl: './driver-home.page.html',
  styleUrls: ['./driver-home.page.scss'],
  standalone: false
})
export class DriverHomePage implements OnInit, OnDestroy {
  public driverName: string = 'Driver';
  public vehiclePlate: string = '';
  public profilePhotoUrl: string | null = null;
  
  // Mapbox properties
  private map: any = null;
  private driverMarker: any = null;
  public currentStyle: string = 'streets';
  public isMapFullscreen: boolean = false;
  
  // Trip statuses: 'scheduled', 'on_the_way', 'completed'
  public tripStatus: string = 'scheduled';

  public currentTrip: any = null;
  public nextTrip: any = null;
  public isLoadingSchedule = true;
  public nextTripDate: string | null = null;
  public slideDistance = '0px';

  private lastProfileRefreshAt: number = 0;
  private isRefreshingProfile: boolean = false;

  isManifestModalOpen = false;
  manifestData: any[] = [];
  isLoadingManifest = false;

  private backButtonSubscription: Subscription | null = null;

  constructor(
    private apiService: ApiService,
    private toastController: ToastController,
    private platform: Platform
  ) { }

  ngOnInit() {
    this.loadCachedUserData();
  }

  ionViewWillEnter() {
    this.initTabSlideAnimation(0);
    this.refreshUserData();
    this.loadSchedule();
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

  ionViewWillLeave() {
    if (this.backButtonSubscription) {
      this.backButtonSubscription.unsubscribe();
      this.backButtonSubscription = null;
    }
  }

  ngOnDestroy() {
    if (this.backButtonSubscription) {
      this.backButtonSubscription.unsubscribe();
      this.backButtonSubscription = null;
    }
    this.stopTracking();
  }

  loadSchedule() {
    this.isLoadingSchedule = true;
    this.apiService.getDriverSchedules().subscribe({
      next: (schedules: any[]) => {
        const todayKey = this.getDateKey(new Date());
        const activeSchedules = schedules.filter(s => {
          return s.status !== 'completed' && this.getDateKey(new Date(s.departure_time)) === todayKey;
        });
        const futureSchedules = schedules.filter(s => {
          return s.status !== 'completed' && this.getDateKey(new Date(s.departure_time)) > todayKey;
        });
        
        if (activeSchedules && activeSchedules.length > 0) {
          const sched = activeSchedules[0];
          
          this.currentTrip = {
            id: sched.id,
            displayId: `TRP-${sched.id}`,
            route: `${sched.route?.origin?.name || 'Unknown'} - ${sched.route?.destination?.name || 'Unknown'}`,
            date: sched.departure_time,
            departureTime: new Date(sched.departure_time).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}),
            arrivalTime: sched.arrival_time ? new Date(sched.arrival_time).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) : '-',
            passengers: sched.total_passengers || 0,
            origin_lat: sched.route?.origin?.latitude ? Number(sched.route.origin.latitude) : null,
            origin_lng: sched.route?.origin?.longitude ? Number(sched.route.origin.longitude) : null,
            destination_lat: sched.route?.destination?.latitude ? Number(sched.route.destination.latitude) : null,
            destination_lng: sched.route?.destination?.longitude ? Number(sched.route.destination.longitude) : null
          };
          this.tripStatus = sched.status || 'scheduled';
          this.nextTrip = null;
          this.nextTripDate = null;
          
          if (this.tripStatus === 'on_the_way' || this.tripStatus === 'scheduled') {
            this.startTracking();
          }
          if (this.tripStatus === 'on_the_way') {
            setTimeout(() => this.initDriverMap(), 500);
          }
        } else {
          this.currentTrip = null;
          this.tripStatus = 'scheduled';
          this.nextTrip = futureSchedules.length > 0 ? this.mapScheduleToTrip(futureSchedules[0]) : null;
          this.nextTripDate = this.nextTrip?.fullDate || null;
        }
        this.isLoadingSchedule = false;
      },
      error: (err) => {
        console.error('Failed to load schedule', err);
        this.isLoadingSchedule = false;
        this.currentTrip = null;
        this.nextTrip = null;
      }
    });
  }

  private mapScheduleToTrip(sched: any) {
    const departureDate = new Date(sched.departure_time);
    const arrivalDate = sched.arrival_time ? new Date(sched.arrival_time) : null;

    return {
      id: sched.id,
      displayId: `TRP-${sched.id}`,
      route: `${sched.route?.origin?.name || 'Unknown'} - ${sched.route?.destination?.name || 'Unknown'}`,
      fullDate: departureDate.toLocaleDateString('id-ID', {
        weekday: 'long',
        day: '2-digit',
        month: 'long',
        year: 'numeric'
      }),
      departureTime: departureDate.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}),
      arrivalTime: arrivalDate ? arrivalDate.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) : '-',
      passengers: sched.total_passengers || 0,
      vehicleType: sched.vehicle?.vehicle_type || 'Shuttle',
      statusLabel: 'Siap untuk besok'
    };
  }

  loadCachedUserData() {
    const userDataRaw = localStorage.getItem('user_data');
    if (!userDataRaw) return;

    try {
      const user = JSON.parse(userDataRaw);
      this.applyUserToView(user);
    } catch (err) {
      console.error('Error parsing localStorage user_data:', err);
    }
  }

  private getDateKey(date: Date): string {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');

    return `${year}-${month}-${day}`;
  }

  refreshUserData(force: boolean = false) {
    const token = localStorage.getItem('auth_token');
    const shouldSkip = !force && Date.now() - this.lastProfileRefreshAt < 30000;
    if (!token || this.isRefreshingProfile || shouldSkip) return;

    this.isRefreshingProfile = true;
    this.apiService.getUserProfile().subscribe({
      next: (response) => {
        const user = response || {};
        const currentUserRaw = localStorage.getItem('user_data');
        const currentUser = currentUserRaw ? JSON.parse(currentUserRaw) : {};

        const merged = {
          ...currentUser,
          ...user,
          role: currentUser.role || localStorage.getItem('user_role') || 'driver'
        };

        localStorage.setItem('user_data', JSON.stringify(merged));
        this.applyUserToView(merged);
        this.lastProfileRefreshAt = Date.now();
        this.isRefreshingProfile = false;
      },
      error: (err) => {
        console.error('Failed to refresh driver profile:', err);
        this.isRefreshingProfile = false;
      }
    });
  }

  private applyUserToView(user: any) {
    let name = user?.name || user?.full_name || this.driverName;
    if (name && name.length > 50) {
      name = name.substring(0, 50) + '...';
    }
    this.driverName = name;
    this.vehiclePlate = user?.vehicle?.plate || user?.vehicle_plate || this.vehiclePlate;
    this.profilePhotoUrl = user?.profile_photo_url || null;
  }

  startTrip() {
    if (!this.currentTrip || !this.currentTrip.id) return;
    
    this.apiService.startSchedule(this.currentTrip.id).subscribe({
      next: () => {
        this.tripStatus = 'on_the_way';
        console.log('Trip started!');
        this.startTracking();
        setTimeout(() => this.initDriverMap(), 500);
      },
      error: async (err) => {
        console.error('Gagal memulai perjalanan', err);
        const toast = await this.toastController.create({
          message: 'Gagal memulai perjalanan: ' + (err.error?.message || 'Kesalahan Server'),
          duration: 1100,
          cssClass: 'premium-toast toast-danger',
          position: 'top',
          icon: 'close-circle'
        });
        await toast.present();
      }
    });
  }

  finishTrip() {
    if (!this.currentTrip || !this.currentTrip.id) return;

    this.apiService.finishSchedule(this.currentTrip.id).subscribe({
      next: () => {
        this.tripStatus = 'completed';
        console.log('Trip completed!');
        this.stopTracking();
        if (this.map) {
          this.map.remove();
          this.map = null;
        }
        this.driverMarker = null;
        setTimeout(() => this.loadSchedule(), 2000);
      },
      error: async (err) => {
        console.error('Gagal menyelesaikan perjalanan', err);
        const toast = await this.toastController.create({
          message: 'Gagal menyelesaikan perjalanan: ' + (err.error?.message || 'Kesalahan Server'),
          duration: 1100,
          cssClass: 'premium-toast toast-danger',
          position: 'top',
          icon: 'close-circle'
        });
        await toast.present();
      }
    });
  }

  private watchId: any = null;

  async startTracking() {
    try {
      // 1. Minta izin secara eksplisit (Sangat Penting untuk Android 6+)
      let permissionState = 'granted';
      try {
        const permissions = await Geolocation.requestPermissions();
        permissionState = permissions.location;
      } catch (err) {
        console.warn('Geolocation.requestPermissions not supported or failed, checking permissions:', err);
        try {
          const check = await Geolocation.checkPermissions();
          permissionState = check.location;
        } catch (checkErr) {
          console.warn('Geolocation.checkPermissions also failed:', checkErr);
        }
      }

      if (permissionState === 'denied') {
        console.error('Izin lokasi ditolak oleh pengguna');
        const toast = await this.toastController.create({
          message: 'Izin lokasi (GPS) ditolak. Live Tracking tidak akan berfungsi.',
          duration: 1100,
          cssClass: 'premium-toast toast-warning',
          icon: 'warning'
        });
        await toast.present();
        return;
      }

      // 2. Dapatkan posisi saat ini secara instan dan kirim ke backend
      try {
        const position = await Geolocation.getCurrentPosition({ enableHighAccuracy: true });
        const lat = position.coords.latitude;
        const lng = position.coords.longitude;
        
        this.setDriverMarker(lat, lng);

        if (this.currentTrip && this.currentTrip.id) {
          this.apiService.updateLocation({
            schedule_id: this.currentTrip.id,
            latitude: lat,
            longitude: lng
          }).subscribe({
            next: (loc) => console.log('Lokasi GPS awal berhasil dikirim:', loc),
            error: (err) => console.error('Gagal mengirim lokasi GPS awal:', err)
          });
        }
      } catch (posErr) {
        console.warn('Gagal mendapatkan lokasi GPS awal:', posErr);
      }

      // 3. Bersihkan watch lama jika ada agar tidak duplikat
      if (this.watchId !== null) {
        await Geolocation.clearWatch({ id: this.watchId });
      }

      // 4. Mulai watch posisi secara background/terus-menerus
      this.watchId = await Geolocation.watchPosition({ enableHighAccuracy: true }, (pos: any, err: any) => {
        if (err || !pos) {
          console.error('GPS tracking error', err);
          return;
        }

        const currentLat = pos.coords.latitude;
        const currentLng = pos.coords.longitude;
        
        this.setDriverMarker(currentLat, currentLng);

        if (this.currentTrip && this.currentTrip.id) {
          this.apiService.updateLocation({
            schedule_id: this.currentTrip.id,
            latitude: currentLat,
            longitude: currentLng
          }).subscribe();
        }
      });
    } catch (e) {
      console.error('Gagal mengambil atau melacak lokasi GPS:', e);
    }
  }

  async stopTracking() {
    if (this.watchId !== null) {
      await Geolocation.clearWatch({ id: this.watchId });
      this.watchId = null;
    }
  }

  initDriverMap() {
    mapboxgl.accessToken = 'pk.eyJ1IjoibW9yZW4tNjciLCJhIjoiY21vam1pbWxuMDA0bDJxb2xkZTBnM2s3cSJ9.wUfxEG062R3T-AZr_m9Fvw';
    if (this.map) {
      try {
        this.map.remove();
      } catch (e) {}
      this.map = null;
    }

    const defaultCenter = [110.3785, -7.7970]; // Default Yogyakarta
    let center = defaultCenter;

    if (this.currentTrip && this.currentTrip.origin_lng && this.currentTrip.origin_lat) {
      center = [this.currentTrip.origin_lng, this.currentTrip.origin_lat];
    }

    const styleUrls: Record<string, string> = {
      'streets': 'mapbox://styles/mapbox/streets-v12',
      'dark': 'mapbox://styles/mapbox/dark-v11',
      'satellite': 'mapbox://styles/mapbox/satellite-streets-v12'
    };

    try {
      this.map = new mapboxgl.Map({
        container: 'driver-map',
        style: styleUrls[this.currentStyle] || styleUrls['streets'],
        center: center,
        zoom: 12,
        pitch: 0 // Top-down flat view
      });

      this.map.on('load', () => {
        this.drawRouteOnDriverMap();
        this.updateDriverMarkerPosition();
      });
    } catch (e) {
      console.error('Gagal memuat peta Mapbox supir:', e);
    }
  }

  setMapStyle(style: string) {
    this.currentStyle = style;
    const styleUrls: Record<string, string> = {
      'streets': 'mapbox://styles/mapbox/streets-v12',
      'dark': 'mapbox://styles/mapbox/dark-v11',
      'satellite': 'mapbox://styles/mapbox/satellite-streets-v12'
    };
    if (this.map) {
      this.map.setStyle(styleUrls[style]);
      
      this.map.once('style.load', () => {
        this.drawRouteOnDriverMap();
        this.updateDriverMarkerPosition();
      });
    }
  }

  toggleMapFullscreen() {
    this.isMapFullscreen = !this.isMapFullscreen;

    if (this.isMapFullscreen) {
      this.backButtonSubscription = this.platform.backButton.subscribeWithPriority(9999, () => {
        this.toggleMapFullscreen();
      });
    } else {
      if (this.backButtonSubscription) {
        this.backButtonSubscription.unsubscribe();
        this.backButtonSubscription = null;
      }
    }

    setTimeout(() => {
      if (this.map) {
        this.map.resize();
        if (this.driverMarker) {
          const coords = this.driverMarker.getLngLat();
          this.map.flyTo({ center: coords, zoom: this.isMapFullscreen ? 15 : 14 });
        }
      }
    }, 200);
  }

  drawRouteOnDriverMap() {
    if (!this.map || !this.currentTrip) return;
    const start = [this.currentTrip.origin_lng, this.currentTrip.origin_lat];
    const end = [this.currentTrip.destination_lng, this.currentTrip.destination_lat];

    if (!start[0] || !start[1] || !end[0] || !end[1]) return;

    // Tambah marker terminal asal dan tujuan
    new mapboxgl.Marker({ color: '#3880ff' }) // Biru untuk asal
      .setLngLat(start)
      .addTo(this.map);

    new mapboxgl.Marker({ color: '#10dc60' }) // Hijau untuk tujuan
      .setLngLat(end)
      .addTo(this.map);

    const url = `https://api.mapbox.com/directions/v5/mapbox/driving/${start[0]},${start[1]};${end[0]},${end[1]}?geometries=geojson&overview=full&access_token=${mapboxgl.accessToken}`;

    fetch(url)
      .then(res => res.json())
      .then(data => {
        if (!data.routes[0]) return;
        const routeCoords = data.routes[0].geometry;

        if (this.map.getSource('route')) {
          this.map.getSource('route').setData({
            type: 'Feature',
            geometry: routeCoords
          });
        } else {
          this.map.addSource('route', {
            type: 'geojson',
            data: {
              type: 'Feature',
              properties: {},
              geometry: routeCoords
            }
          });

          this.map.addLayer({
            id: 'route',
            type: 'line',
            source: 'route',
            layout: {
              'line-join': 'round',
              'line-cap': 'round'
            },
            paint: {
              'line-color': '#0066ff',
              'line-width': 5,
              'line-opacity': 0.75
            }
          });
        }
      })
      .catch(err => console.error('Gagal menggambar rute Mapbox:', err));
  }

  async updateDriverMarkerPosition() {
    if (!this.map) return;

    try {
      const position = await Geolocation.getCurrentPosition({ enableHighAccuracy: true });
      const lat = position.coords.latitude;
      const lng = position.coords.longitude;
      this.setDriverMarker(lat, lng);
    } catch (err) {
      if (this.currentTrip && this.currentTrip.origin_lat) {
        this.setDriverMarker(this.currentTrip.origin_lat, this.currentTrip.origin_lng);
      }
    }
  }

  private setDriverMarker(lat: number, lng: number) {
    if (!this.map) return;

    if (this.driverMarker) {
      this.driverMarker.setLngLat([lng, lat]);
    } else {
      const el = document.createElement('div');
      el.style.width = '32px';
      el.style.height = '32px';
      el.style.backgroundImage = 'url("https://cdn-icons-png.flaticon.com/512/3448/3448339.png")';
      el.style.backgroundSize = 'cover';
      el.style.filter = 'drop-shadow(0 2px 4px rgba(0,0,0,0.3))';

      this.driverMarker = new mapboxgl.Marker(el)
        .setLngLat([lng, lat])
        .addTo(this.map);
    }

    this.map.flyTo({ center: [lng, lat], zoom: 14, speed: 0.8 });
  }

  openManifest() {
    this.isManifestModalOpen = true;
    this.loadManifest();
  }

  closeManifest() {
    this.isManifestModalOpen = false;
  }

  loadManifest() {
    if (!this.currentTrip || !this.currentTrip.id) return;
    
    this.isLoadingManifest = true;
    const scheduleId = this.currentTrip.id;
    
    this.apiService.getDriverManifest(scheduleId).subscribe({
      next: (data) => {
        this.manifestData = data;
        this.isLoadingManifest = false;
      },
      error: (err) => {
        console.error('Failed to load manifest', err);
        this.isLoadingManifest = false;
      }
    });
  }
}
