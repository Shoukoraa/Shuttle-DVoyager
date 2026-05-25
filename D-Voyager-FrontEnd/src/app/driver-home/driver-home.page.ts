import { Component, OnInit } from '@angular/core';
import { ApiService } from '../services/api.service';

@Component({
  selector: 'app-driver-home',
  templateUrl: './driver-home.page.html',
  styleUrls: ['./driver-home.page.scss'],
  standalone: false
})
export class DriverHomePage implements OnInit {
  public driverName: string = 'Driver';
  public vehiclePlate: string = '';
  public profilePhotoUrl: string | null = null;
  
  // Trip statuses: 'scheduled', 'on_the_way', 'completed'
  public tripStatus: string = 'scheduled';

  public currentTrip: any = null;
  public nextTrip: any = null;
  public isLoadingSchedule = true;
  public nextTripDate: string | null = null;

  private lastProfileRefreshAt: number = 0;
  private isRefreshingProfile: boolean = false;

  isManifestModalOpen = false;
  manifestData: any[] = [];
  isLoadingManifest = false;

  constructor(private apiService: ApiService) { }

  ngOnInit() {
    this.loadCachedUserData();
  }

  ionViewWillEnter() {
    this.refreshUserData();
    this.loadSchedule();
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
            passengers: sched.total_passengers || 0
          };
          this.tripStatus = sched.status || 'scheduled';
          this.nextTrip = null;
          this.nextTripDate = null;
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
    this.driverName = user?.name || user?.full_name || this.driverName;
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
      },
      error: (err) => console.error('Gagal memulai perjalanan', err)
    });
  }

  finishTrip() {
    if (!this.currentTrip || !this.currentTrip.id) return;

    this.apiService.finishSchedule(this.currentTrip.id).subscribe({
      next: () => {
        this.tripStatus = 'completed';
        console.log('Trip completed!');
        this.stopTracking();
        // Load next schedule if any
        setTimeout(() => this.loadSchedule(), 2000);
      },
      error: (err) => console.error('Gagal menyelesaikan perjalanan', err)
    });
  }

  private watchId: number | null = null;

  startTracking() {
    if (navigator.geolocation) {
      this.watchId = navigator.geolocation.watchPosition((position) => {
        const lat = position.coords.latitude;
        const lng = position.coords.longitude;
        if (this.currentTrip && this.currentTrip.id) {
          this.apiService.updateLocation({
            schedule_id: this.currentTrip.id,
            latitude: lat,
            longitude: lng
          }).subscribe();
        }
      }, (err) => {
        console.error('GPS tracking error', err);
      }, { enableHighAccuracy: true });
    }
  }

  stopTracking() {
    if (this.watchId !== null && navigator.geolocation) {
      navigator.geolocation.clearWatch(this.watchId);
      this.watchId = null;
    }
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
