import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { ApiService } from '../services/api.service';

interface Seat {
  id: string;
  real_id?: number;
  status: 'available' | 'unavailable' | 'selected' | 'empty' | 'driver';
}

@Component({
  selector: 'app-select-seat',
  templateUrl: './select-seat.page.html',
  styleUrls: ['./select-seat.page.scss'],
  standalone: false
})
export class SelectSeatPage implements OnInit {
  
  routeOrigin = '';
  routeDest = '';
  
  shuttleInfo = {
    operator: 'D-Voyager Shuttle',
    time: '',
    arrivalTime: '',
    date: '',
    type: '',
    rating: 5.0,
    price: 0,
    photo: '',
    photos: [] as string[]
  };

  seatMap: Seat[][] = [];
  selectedSeats: Seat[] = [];
  scheduleId: number | null = null;
  isLoading = true;
  vehicleCategory: 'family_car' | 'mini_bus' | 'bus' = 'mini_bus';

  constructor(private route: ActivatedRoute, private router: Router, private apiService: ApiService) { }

  ngOnInit() {
    this.route.queryParams.subscribe(params => {
      if (params['schedule_id']) {
        const incomingId = Number(params['schedule_id']);
        if (this.scheduleId !== incomingId) {
          this.selectedSeats = [];
          this.scheduleId = incomingId;
        }
        this.loadSeats(this.scheduleId!);
      }
    });
  }

  loadSeats(scheduleId: number) {
    this.isLoading = true;
    this.apiService.getSeats(scheduleId).subscribe({
      next: (res) => {
        const schedule = res.schedule;
        const dbSeats = res.seats_layout;

        this.routeOrigin = schedule.route?.origin?.name || 'Asal';
        this.routeDest = schedule.route?.destination?.name || 'Tujuan';

        const deptTime = new Date(schedule.departure_time);
        this.shuttleInfo.time = this.formatTime(deptTime);
        this.shuttleInfo.arrivalTime = schedule?.arrival_time
          ? this.formatTime(new Date(schedule.arrival_time))
          : 'Belum diatur';
        this.shuttleInfo.date = deptTime.toLocaleDateString();
        this.shuttleInfo.type = schedule.vehicle?.vehicle_type || 'Shuttle';
        
        const rawPhoto = schedule.vehicle?.photo;
        if (Array.isArray(rawPhoto)) {
          this.shuttleInfo.photos = rawPhoto;
          this.shuttleInfo.photo = rawPhoto[0] || '';
        } else if (typeof rawPhoto === 'string' && rawPhoto.trim() !== '') {
          if (rawPhoto.trim().startsWith('[')) {
            try {
              const parsed = JSON.parse(rawPhoto);
              if (Array.isArray(parsed)) {
                this.shuttleInfo.photos = parsed;
                this.shuttleInfo.photo = parsed[0] || '';
              } else {
                this.shuttleInfo.photos = [rawPhoto];
                this.shuttleInfo.photo = rawPhoto;
              }
            } catch (e) {
              this.shuttleInfo.photos = [rawPhoto];
              this.shuttleInfo.photo = rawPhoto;
            }
          } else {
            this.shuttleInfo.photos = [rawPhoto];
            this.shuttleInfo.photo = rawPhoto;
          }
        } else {
          this.shuttleInfo.photos = [];
          this.shuttleInfo.photo = '';
        }

        this.shuttleInfo.price = schedule.price || schedule.route?.price || 0;
        this.vehicleCategory = schedule.vehicle?.vehicle_category || this.inferCategoryByCapacity(schedule.capacity);

        this.buildSeatMap(dbSeats, schedule.capacity, this.vehicleCategory);
        this.isLoading = false;
      },
      error: (err) => {
        console.error(err);
        this.isLoading = false;
      }
    });
  }

  private formatTime(date: Date): string {
    return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
  }

  private inferCategoryByCapacity(capacity: number): 'family_car' | 'mini_bus' | 'bus' {
    if (capacity <= 8) {
      return 'family_car';
    }

    if (capacity <= 16) {
      return 'mini_bus';
    }

    return 'bus';
  }

  buildSeatMap(dbSeats: any[], capacity: number, category: 'family_car' | 'mini_bus' | 'bus') {
    const getDbSeat = (seatNo: string) =>
      dbSeats.find((s) => String(s.seat_number) === String(seatNo));

    const isAlreadySelected = (seatNo: string) =>
      this.selectedSeats.some((s) => String(s.id) === String(seatNo));

    const newSelectedSeats: Seat[] = [];

    const mapSeat = (seatId: string): Seat => {
      if (seatId === 'driver') return { id: seatId, status: 'driver' as const };
      if (seatId.startsWith('e')) return { id: seatId, status: 'empty' as const };

      const dbSeat = getDbSeat(seatId);
      const rawStatus = String(dbSeat?.status || '').toLowerCase();
      const isAvailable = rawStatus === 'available';

      let status: 'available' | 'unavailable' | 'selected' = 'unavailable';
      if (dbSeat && isAvailable) {
        status = isAlreadySelected(seatId) ? 'selected' : 'available';
      }

      const seatObj: Seat = {
        id: seatId,
        real_id: dbSeat ? dbSeat.id : undefined,
        status: status
      };

      if (status === 'selected') {
        newSelectedSeats.push(seatObj);
      }

      return seatObj;
    };

    let seatsPerRow = 3;
    let rowPattern: Array<'S' | 'E'> = ['S', 'S', 'E', 'S'];
    let driverRow: string[] = ['e_front_1', 'e_front_2', 'e_front_3', 'driver'];

    if (category === 'family_car') {
      // Family car layout: no center aisle, front passenger on left, driver on right.
      const layoutTemplate: string[][] = [];
      let nextSeat = 1;

      // Front row.
      if (capacity >= 1) {
        layoutTemplate.push([String(nextSeat++), 'driver']);
      } else {
        layoutTemplate.push(['e_family_front_left', 'driver']);
      }

      // Rear rows: compact 2-column blocks, no aisle.
      while (nextSeat <= capacity) {
        const leftSeat = String(nextSeat++);
        const rightSeat = nextSeat <= capacity
          ? String(nextSeat++)
          : `e_family_tail_${nextSeat}`;
        layoutTemplate.push([leftSeat, rightSeat]);
      }

      this.seatMap = layoutTemplate.map(row => row.map(mapSeat));
      this.selectedSeats = newSelectedSeats;
      return;
    } else if (category === 'mini_bus') {
      // Mini bus layout: front row has 1 passenger on the left and driver on the right,
      // then the remaining rows use 3 aligned columns.
      const layoutTemplate: string[][] = [];
      let nextSeat = 1;

      if (capacity >= 1) {
        layoutTemplate.push([String(nextSeat++), 'e_mini_front_gap', 'driver']);
      } else {
        layoutTemplate.push(['e_mini_front_left', 'e_mini_front_gap', 'driver']);
      }

      while (nextSeat <= capacity) {
        const row: string[] = [];
        for (let i = 0; i < 3; i++) {
          if (nextSeat <= capacity) {
            row.push(String(nextSeat++));
          } else {
            row.push(`e_mini_tail_${nextSeat}_${i}`);
          }
        }
        layoutTemplate.push(row);
      }

      this.seatMap = layoutTemplate.map(row => row.map(mapSeat));
      this.selectedSeats = newSelectedSeats;
      return;
    } else {
      seatsPerRow = 4;
      rowPattern = ['S', 'S', 'E', 'S', 'S'];
      driverRow = ['e_front_1', 'e_front_2', 'e_front_3', 'e_front_4', 'driver'];
    }

    const layoutTemplate: string[][] = [driverRow];
    let seatNumber = 1;
    let rowIndex = 0;

    while (seatNumber <= capacity) {
      let seatsPlacedInRow = 0;
      const row = rowPattern.map((cell, cellIndex) => {
        if (cell === 'E') {
          return `e_${category}_${rowIndex}_${cellIndex}`;
        }

        if (seatsPlacedInRow < seatsPerRow && seatNumber <= capacity) {
          seatsPlacedInRow += 1;
          return String(seatNumber++);
        }

        return `e_${category}_${rowIndex}_${cellIndex}_tail`;
      });

      layoutTemplate.push(row);
      rowIndex += 1;
    }

    this.seatMap = layoutTemplate.map(row => row.map(mapSeat));
    this.selectedSeats = newSelectedSeats;
  }

  toggleSeat(seat: Seat) {
    if (seat.status === 'unavailable' || seat.status === 'empty' || seat.status === 'driver') {
      return;
    }

    if (seat.status === 'available') {
      seat.status = 'selected';
      this.selectedSeats.push(seat);
    } else if (seat.status === 'selected') {
      seat.status = 'available';
      this.selectedSeats = this.selectedSeats.filter(s => s.id !== seat.id);
    }
  }

  proceedToBooking() {
    const token = localStorage.getItem('auth_token');
    if (!token) {
      alert('Anda harus login atau mendaftar terlebih dahulu untuk melanjutkan pemesanan.');
      this.router.navigate(['/login']);
      return;
    }

    const seatNumbers = this.selectedSeats.map(s => s.id);
    this.router.navigate(['/booking-summary'], {
      state: {
        scheduleId: this.scheduleId,
        selectedSeats: seatNumbers,
        pricePerSeat: this.shuttleInfo.price,
        shuttleInfo: this.shuttleInfo,
        routeOrigin: this.routeOrigin,
        routeDest: this.routeDest
      }
    });
  }

}
