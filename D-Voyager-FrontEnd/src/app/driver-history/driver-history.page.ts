import { Component, OnInit } from '@angular/core';
import { ApiService } from '../services/api.service';

@Component({
  selector: 'app-driver-history',
  templateUrl: './driver-history.page.html',
  styleUrls: ['./driver-history.page.scss'],
  standalone: false
})
export class DriverHistoryPage implements OnInit {
  public history: any[] = [];
  public isLoading = false;

  constructor(private apiService: ApiService) { }

  ngOnInit() {
    this.loadHistory();
  }

  ionViewWillEnter() {
    this.loadHistory();
  }

  loadHistory() {
    this.isLoading = true;

    this.apiService.getDriverSchedules().subscribe({
      next: (schedules: any[]) => {
        this.history = schedules
          .filter(schedule => schedule.status === 'completed')
          .map(schedule => {
            const finishedAt = schedule.end_time || schedule.departure_time;
            const routeOrigin = schedule.route?.origin?.name || 'Unknown';
            const routeDestination = schedule.route?.destination?.name || 'Unknown';

            return {
              finishedAt: finishedAt ? new Date(finishedAt).getTime() : 0,
              id: `TRP-${schedule.id}`,
              date: finishedAt ? new Date(finishedAt).toLocaleDateString('id-ID', {
                day: '2-digit',
                month: 'long',
                year: 'numeric'
              }) : '-',
              route: `${routeOrigin} - ${routeDestination}`,
              passengers: schedule.total_passengers || 0,
              status: 'Selesai',
            };
          })
          .sort((a, b) => b.finishedAt - a.finishedAt);

        this.isLoading = false;
      },
      error: (err) => {
        console.error('Failed to load driver history', err);
        this.history = [];
        this.isLoading = false;
      }
    });
  }
}
