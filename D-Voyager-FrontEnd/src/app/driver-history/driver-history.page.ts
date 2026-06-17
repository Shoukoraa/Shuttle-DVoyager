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
  public slideDistance = '0px';

  constructor(private apiService: ApiService) { }

  ngOnInit() {
    this.loadHistory();
  }

  ionViewWillEnter() {
    this.initTabSlideAnimation(1);
    this.loadHistory();
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

  loadHistory() {
    this.isLoading = true;

    this.apiService.getDriverSchedules().subscribe({
      next: (schedules: any[]) => {
        this.history = schedules
          .filter(schedule => schedule.status === 'completed')
          .map(schedule => {
            const rawEndTime = schedule.end_time || schedule.departure_time || '';
            const finishedAt = rawEndTime ? rawEndTime.replace(' ', 'T') : '';
            const rawDepartureTime = schedule.departure_time || '';
            const departureTime = rawDepartureTime ? rawDepartureTime.replace(' ', 'T') : '';
            const routeOrigin = schedule.route?.origin?.name || 'Unknown';
            const routeDestination = schedule.route?.destination?.name || 'Unknown';

            return {
              finishedAt: finishedAt ? new Date(finishedAt).getTime() : 0,
              id: `TRP-${schedule.id}`,
              date: departureTime ? new Date(departureTime).toLocaleDateString('id-ID', {
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
