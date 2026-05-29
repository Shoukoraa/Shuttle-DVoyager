import { Component, OnInit } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { Router } from '@angular/router';
import { ApiService } from '../services/api.service';

@Component({
  selector: 'app-schedule',
  templateUrl: './schedule.page.html',
  styleUrls: ['./schedule.page.scss'],
  standalone: false
})
export class SchedulePage implements OnInit {
  public routeOrigin = '';
  public routeDest = '';
  public searchDate: string = '';
  public isLoading = true;
  
  public filters = ['Urutkan & Saring', 'Operator bis', 'Waktu Berangkat', 'Format Kursi', 'Kelas'];

  public schedules: any[] = [];

  constructor(
    private route: ActivatedRoute,
    private router: Router,
    private apiService: ApiService
  ) { }

  ngOnInit() {
    this.route.queryParams.subscribe(params => {
      if (params['origin_id'] && params['destination_id'] && params['date']) {
        this.searchDate = params['date'];
        this.loadSchedules(params['origin_id'], params['destination_id'], params['date']);
      }
    });
  }

  loadSchedules(originId: number, destinationId: number, date: string) {
    this.isLoading = true;
    this.apiService.searchSchedules(originId, destinationId, date).subscribe({
      next: (data) => {
        this.schedules = data;
        
        // Coba set label kota asal dan tujuan dari data pertama jika ada
        if (this.schedules.length > 0 && this.schedules[0].route) {
          this.routeOrigin = this.schedules[0].route.origin?.name || 'Asal';
          this.routeDest = this.schedules[0].route.destination?.name || 'Tujuan';
        }
        
        this.isLoading = false;
      },
      error: (err) => {
        console.error(err);
        this.isLoading = false;
      }
    });
  }

  openSchedule(scheduleId: number): void {
    this.router.navigate(['/select-seat'], {
      queryParams: { schedule_id: scheduleId }
    });
  }

  getVehicleCategoryLabel(schedule: any): string {
    const category = String(schedule?.vehicle?.vehicle_category || '').toLowerCase();

    if (category === 'family_car') {
      return 'Mobil Keluarga';
    }

    if (category === 'mini_bus') {
      return 'Mini Bus';
    }

    if (category === 'bus') {
      return 'BUS';
    }

    const capacity = Number(schedule?.capacity || schedule?.vehicle?.capacity || 0);

    if (capacity <= 8) {
      return 'Mobil Keluarga';
    }

    if (capacity <= 16) {
      return 'Mini Bus';
    }

    return 'BUS';
  }

  getVehicleTypeLabel(schedule: any): string {
    return schedule?.vehicle?.vehicle_type || 'Tipe tidak tersedia';
  }

  getVehicleCapacity(schedule: any): number | string {
    return schedule?.capacity || schedule?.vehicle?.capacity || '-';
  }

  getArrivalTime(schedule: any): string {
    if (schedule?.arrival_time) {
      return this.formatTime(new Date(schedule.arrival_time));
    }

    return 'Belum diatur';
  }

  private formatTime(date: Date): string {
    return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
  }
}
