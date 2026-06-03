import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { environment } from 'src/environments/environment';
import { Observable } from 'rxjs';
import { tap } from 'rxjs/operators';
import { EchoService } from './echo.service';

@Injectable({
  providedIn: 'root'
})
export class ApiService {
  private apiUrl = environment.apiUrl;
  private roleIdFallbacks: Record<number, string> = {
    1: 'admin',
    2: 'driver',
    3: 'customer',
  };

  constructor(
    private http: HttpClient,
    private echoService: EchoService
  ) { }

  // Auth Methods
  getUserRole(user: any, fallback: string = 'customer'): string {
    const role = user?.role;

    if (typeof role === 'string' && role.trim()) {
      return role.trim().toLowerCase();
    }

    if (typeof role?.name === 'string' && role.name.trim()) {
      return role.name.trim().toLowerCase();
    }

    const roleId = Number(user?.role_id ?? role?.id);
    return this.roleIdFallbacks[roleId] || fallback;
  }

  private storeAuthSession(response: any): void {
    if (response?.token && response?.user) {
      const role = this.getUserRole(response.user);

      localStorage.setItem('auth_token', response.token);
      localStorage.setItem('user_role', role);
      localStorage.setItem('user_data', JSON.stringify({
        ...response.user,
        role,
      }));
    }
  }

  clearAuthSession(): void {
    localStorage.removeItem('auth_token');
    localStorage.removeItem('user_role');
    localStorage.removeItem('user_data');
  }

  login(data: any): Observable<any> {
    return this.http.post(`${this.apiUrl}/auth/login`, data).pipe(
      tap((response: any) => {
        this.storeAuthSession(response);
      })
    );
  }

  register(data: any): Observable<any> {
    return this.http.post(`${this.apiUrl}/auth/register`, data).pipe(
      // register now does not return token; frontend should navigate to OTP page
      tap((response: any) => {
        // no-op: backend will send OTP; do not store token here
      })
    );
  }

  requestOtp(data: any): Observable<any> {
    return this.http.post(`${this.apiUrl}/auth/otp/request`, data);
  }

  verifyOtp(data: any): Observable<any> {
    return this.http.post(`${this.apiUrl}/auth/otp/verify`, data).pipe(
      tap((response: any) => {
        this.storeAuthSession(response);
      })
    );
  }

  requestPasswordReset(data: { email: string }): Observable<any> {
    return this.http.post(`${this.apiUrl}/auth/password/forgot`, data);
  }

  resetPassword(data: {
    email: string;
    otp: string;
    password: string;
    password_confirmation: string;
  }): Observable<any> {
    return this.http.post(`${this.apiUrl}/auth/password/reset`, data);
  }

  setGoogleSession(token: string, role: string): void {
    const userDataRaw = localStorage.getItem('user_data');
    const userData = userDataRaw ? JSON.parse(userDataRaw) : {};
    userData.role = role;

    this.storeAuthSession({
      token,
      user: userData,
    });
  }

  getGoogleOAuthRedirectUrl(frontendRedirectUrl: string): string {
    const redirectPath = `${this.apiUrl}/auth/google/redirect`;
    return `${redirectPath}?redirect=${encodeURIComponent(frontendRedirectUrl)}`;
  }

  getUserProfile(): Observable<any> {
    return this.http.get(`${this.apiUrl}/me`, { headers: this.getHeaders() });
  }

  getAppConfig(): Observable<any> {
    return this.http.get(`${this.apiUrl}/app-config`);
  }

  updateUserProfile(data: { name: string; phone: string }): Observable<any> {
    return this.http.put(`${this.apiUrl}/me`, data, { headers: this.getHeaders() }).pipe(
      tap((response: any) => {
        this.mergeUserIntoSession(response?.user || response);
      })
    );
  }

  updateUserProfilePhoto(file: File): Observable<any> {
    const formData = new FormData();
    formData.append('profile_photo', file);

    return this.http.post(`${this.apiUrl}/me/profile-photo`, formData, {
      headers: this.getAuthHeaders(),
    }).pipe(
      tap((response: any) => {
        this.mergeUserIntoSession(response?.user || response);
      })
    );
  }

  updateUserPassword(data: {
    current_password?: string;
    password: string;
    password_confirmation: string;
  }): Observable<any> {
    return this.http.put(`${this.apiUrl}/me/password`, data, { headers: this.getHeaders() });
  }

  logout(): Observable<any> {
    return this.http.post(`${this.apiUrl}/auth/logout`, {}, { headers: this.getHeaders() }).pipe(
      tap(() => {
        this.clearAuthSession();
      })
    );
  }

  deleteAccount(): Observable<any> {
    return this.http.delete(`${this.apiUrl}/me`, { headers: this.getHeaders() }).pipe(
      tap(() => {
        this.clearAuthSession();
      })
    );
  }

  // Helper method to get headers with Bearer token
  private getHeaders(): HttpHeaders {
    const token = localStorage.getItem('auth_token');
    const headers: Record<string, string> = {
      'Content-Type': 'application/json',
    };

    if (token) {
      headers['Authorization'] = `Bearer ${token}`;
      try {
        const echo = this.echoService.getEcho();
        if (echo) {
          const socketId = echo.socketId();
          if (socketId) {
            headers['X-Socket-ID'] = socketId;
          }
        }
      } catch (e) {
        // Echo might not be initialized yet
      }
    }

    return new HttpHeaders(headers);
  }

  private getAuthHeaders(): HttpHeaders {
    const token = localStorage.getItem('auth_token');
    const headers: Record<string, string> = {};

    if (token) {
      headers['Authorization'] = `Bearer ${token}`;
      try {
        const echo = this.echoService.getEcho();
        if (echo) {
          const socketId = echo.socketId();
          if (socketId) {
            headers['X-Socket-ID'] = socketId;
          }
        }
      } catch (e) {
        // Echo might not be initialized yet
      }
    }

    return new HttpHeaders(headers);
  }

  private mergeUserIntoSession(updatedUser: any): void {
    const currentUserRaw = localStorage.getItem('user_data');
    const currentUser = currentUserRaw ? JSON.parse(currentUserRaw) : {};

    localStorage.setItem('user_data', JSON.stringify({
      ...currentUser,
      ...updatedUser,
      role: updatedUser?.role || currentUser.role || localStorage.getItem('user_role') || 'customer',
    }));
  }

  // --- Customer Methods ---

  getLocations(): Observable<any> {
    return this.http.get(`${this.apiUrl}/locations`);
  }

  getVouchers(): Observable<any> {
    return this.http.get(`${this.apiUrl}/vouchers`);
  }

  searchSchedules(originId: number, destinationId: number, date: string): Observable<any> {
    // using generic GET but no auth needed actually, just query params
    return this.http.get(`${this.apiUrl}/schedules?origin_id=${originId}&destination_id=${destinationId}&date=${date}`);
  }

  getSeats(scheduleId: number): Observable<any> {
    return this.http.get(`${this.apiUrl}/schedules/${scheduleId}/seats`);
  }

  bookTicket(data: any): Observable<any> {
    return this.http.post(`${this.apiUrl}/customer/booking`, data, { headers: this.getHeaders() });
  }

  payTicket(bookingId: number, data: any): Observable<any> {
    return this.http.post(`${this.apiUrl}/customer/booking/${bookingId}/pay`, data, { headers: this.getHeaders() });
  }

  cancelBooking(bookingId: number): Observable<any> {
    return this.http.post(`${this.apiUrl}/customer/booking/${bookingId}/cancel`, {}, { headers: this.getHeaders() });
  }

  submitBookingReview(bookingId: number, data: { rating: number; comment?: string }): Observable<any> {
    return this.http.post(`${this.apiUrl}/customer/booking/${bookingId}/review`, data, { headers: this.getHeaders() });
  }

  getMyTickets(): Observable<any> {
    return this.http.get(`${this.apiUrl}/customer/my-bookings`, { headers: this.getHeaders() });
  }

  // Example generic GET method
  get(endpoint: string): Observable<any> {
    return this.http.get(`${this.apiUrl}/${endpoint}`, { headers: this.getHeaders() });
  }

  // Example generic POST method
  post(endpoint: string, data: any): Observable<any> {
    return this.http.post(`${this.apiUrl}/${endpoint}`, data, { headers: this.getHeaders() });
  }

  // --- Driver Methods ---
  getDriverManifest(scheduleId: number): Observable<any> {
    return this.http.get(`${this.apiUrl}/driver/schedule/${scheduleId}/manifest`, { headers: this.getHeaders() });
  }

  getDriverSchedules(): Observable<any> {
    return this.http.get(`${this.apiUrl}/driver/my-schedules`, { headers: this.getHeaders() });
  }

  getDriverReviews(): Observable<any> {
    return this.http.get(`${this.apiUrl}/driver/reviews`, { headers: this.getHeaders() });
  }

  // --- Tracking & Control ---
  startSchedule(scheduleId: number): Observable<any> {
    return this.http.post(`${this.apiUrl}/driver/schedule/${scheduleId}/start`, {}, { headers: this.getHeaders() });
  }

  finishSchedule(scheduleId: number): Observable<any> {
    return this.http.post(`${this.apiUrl}/driver/schedule/${scheduleId}/finish`, {}, { headers: this.getHeaders() });
  }

  updateLocation(data: { schedule_id: number; latitude: number; longitude: number }): Observable<any> {
    return this.http.post(`${this.apiUrl}/driver/location`, data, { headers: this.getHeaders() });
  }

  // --- Chat Methods ---
  getChatMessages(scheduleId: number, customerId?: number): Observable<any> {
    const role = localStorage.getItem('user_role') === 'driver' ? 'driver' : 'customer';
    const url = role === 'driver' && customerId
      ? `${this.apiUrl}/driver/chat/${scheduleId}/${customerId}`
      : `${this.apiUrl}/customer/chat/${scheduleId}`;
    return this.http.get(url, { headers: this.getHeaders() });
  }

  sendMessage(data: { schedule_id: number; customer_id: number; message: string; sender_type: string }): Observable<any> {
    const role = localStorage.getItem('user_role') === 'driver' ? 'driver' : 'customer';
    return this.http.post(`${this.apiUrl}/${role}/chat`, data, { headers: this.getHeaders() });
  }

  // --- Customer Methods ---
  getMyBookings(): Observable<any> {
    return this.http.get(`${this.apiUrl}/customer/my-bookings`, { headers: this.getHeaders() });
  }
}
