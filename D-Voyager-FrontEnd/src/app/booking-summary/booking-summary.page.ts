import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { ToastController } from '@ionic/angular';
import { ApiService } from '../services/api.service';
import { environment } from 'src/environments/environment';

@Component({
  selector: 'app-booking-summary',
  templateUrl: './booking-summary.page.html',
  styleUrls: ['./booking-summary.page.scss'],
  standalone: false
})
export class BookingSummaryPage implements OnInit {
  
  tripDetails = {
    scheduleId: null as number | null,
    operator: '',
    origin: '',
    destination: '',
    date: '',
    time: '',
    arrivalTime: '',
    seats: [] as string[]
  };

  contactDetails = {
    name: '',
    phone: '',
    email: ''
  };

  passengers: { seatNumber: string, title: string, name: string }[] = [];

  fareDetails = {
    ticketPrice: 0,
    fee: environment.serviceFee
  };

  get totalFare() {
    return (this.fareDetails.ticketPrice * this.tripDetails.seats.length) + this.fareDetails.fee;
  }

  constructor(
    private router: Router, 
    private apiService: ApiService,
    private toastCtrl: ToastController
  ) { 
    // Get data from navigation state
    const navigation = this.router.getCurrentNavigation();
    if (navigation?.extras?.state) {
      const state = navigation.extras.state;
      this.tripDetails.scheduleId = state['scheduleId'] || null;
      this.tripDetails.seats = state['selectedSeats'] || [];
      this.fareDetails.ticketPrice = state['pricePerSeat'] || 0;
      this.tripDetails.operator = state['shuttleInfo']?.operator || '';
      this.tripDetails.time = state['shuttleInfo']?.time || '';
      this.tripDetails.arrivalTime = this.normalizeArrivalTime(state['shuttleInfo']?.arrivalTime);
      this.tripDetails.date = state['shuttleInfo']?.date || '';
      this.tripDetails.origin = state['routeOrigin'] || '';
      this.tripDetails.destination = state['routeDest'] || '';
    }
  }

  ngOnInit() {
    // If state is lost on page refresh, try to retrieve from sessionStorage
    const savedState = sessionStorage.getItem('bookingSummaryState');
    if (savedState && this.tripDetails.seats.length === 0) {
      const state: any = JSON.parse(savedState);
      this.tripDetails.scheduleId = state.scheduleId || null;
      this.tripDetails.seats = state.selectedSeats || [];
      this.fareDetails.ticketPrice = state.pricePerSeat || 0;
      this.tripDetails.operator = state.shuttleInfo?.operator || '';
      this.tripDetails.time = state.shuttleInfo?.time || '';
      this.tripDetails.arrivalTime = this.normalizeArrivalTime(state.shuttleInfo?.arrivalTime);
      this.tripDetails.date = state.shuttleInfo?.date || '';
      this.tripDetails.origin = state.routeOrigin || '';
      this.tripDetails.destination = state.routeDest || '';
      
      if (state.contactDetails) {
        this.contactDetails = state.contactDetails;
      }
    }

    this.passengers = this.tripDetails.seats.map(seat => ({
      seatNumber: seat,
      title: 'Tn.',
      name: ''
    }));

    this.saveBookingSummaryState();
    this.loadServiceFee();
  }

  private normalizeArrivalTime(arrivalTime: string | undefined): string {
    if (!arrivalTime || arrivalTime === 'Estimasi') {
      return 'Belum diatur';
    }

    return arrivalTime;
  }

  private loadServiceFee() {
    this.apiService.getAppConfig().subscribe({
      next: (config) => {
        const serviceFee = Number(config?.service_fee);

        if (!Number.isNaN(serviceFee) && serviceFee >= 0) {
          this.fareDetails.fee = serviceFee;
          this.saveBookingSummaryState();
        }
      },
      error: (error) => {
        console.error('Failed to load app config:', error);
      }
    });
  }

  private saveBookingSummaryState() {
    sessionStorage.setItem('bookingSummaryState', JSON.stringify({
      scheduleId: this.tripDetails.scheduleId,
      selectedSeats: this.tripDetails.seats,
      pricePerSeat: this.fareDetails.ticketPrice,
      serviceFee: this.fareDetails.fee,
      shuttleInfo: {
        operator: this.tripDetails.operator,
        time: this.tripDetails.time,
        arrivalTime: this.tripDetails.arrivalTime,
        date: this.tripDetails.date
      },
      routeOrigin: this.tripDetails.origin,
      routeDest: this.tripDetails.destination,
      contactDetails: this.contactDetails
    }));
  }

  async proceedToPayment() {
    if (!this.contactDetails.name || !this.contactDetails.phone || !this.contactDetails.email) {
      const toast = await this.toastCtrl.create({
        message: 'Mohon lengkapi detail penumpang terlebih dahulu.',
        duration: 2000,
        color: 'warning',
        position: 'top'
      });
      toast.present();
      return;
    }

    this.saveBookingSummaryState();
    this.router.navigate(['/payment']);
  }

}
