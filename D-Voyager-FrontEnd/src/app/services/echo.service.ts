import { Injectable } from '@angular/core';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
import { environment } from 'src/environments/environment';

@Injectable({
  providedIn: 'root'
})
export class EchoService {
  private echoInstance: Echo<any> | null = null;
  private currentToken: string | null = null;

  constructor() {}

  /**
   * Get the Laravel Echo instance dynamically.
   * If the token changes, it disconnects and recreates Echo with the new token.
   */
  getEcho(): Echo<any> {
    const token = localStorage.getItem('auth_token');

    // If the token changed, recreate the instance to update auth headers
    if (this.echoInstance && this.currentToken !== token) {
      this.disconnect();
    }

    if (!this.echoInstance) {
      this.currentToken = token;
      this.initEcho(token);
    }
    
    return this.echoInstance!;
  }

  private initEcho(token: string | null) {
    (window as any).Pusher = Pusher;

    // Konfigurasi menggunakan Reverb lokal
    this.echoInstance = new Echo({
      broadcaster: 'reverb',
      key: 'k2n8z9p5q3r7s1t6u5v4', // Sesuai REVERB_APP_KEY di .env backend
      wsHost: '127.0.0.1',
      wsPort: 8080,
      wssPort: 8080,
      forceTLS: false,
      enabledTransports: ['ws', 'wss'],
      authEndpoint: `${environment.apiUrl}/broadcasting/auth`,
      auth: {
        headers: {
          Authorization: token ? `Bearer ${token}` : ''
        }
      }
    });

    /* Konfigurasi Pusher Cloud sebelumnya:
    this.echoInstance = new Echo({
      broadcaster: 'pusher',
      key: '3d489e6e4b5731da6c86', 
      cluster: 'ap1',
      forceTLS: true,
      authEndpoint: `${environment.apiUrl}/broadcasting/auth`,
      auth: {
        headers: {
          Authorization: token ? `Bearer ${token}` : ''
        }
      }
    });
    */
  }

  /**
   * Disconnect and clear the current Echo instance.
   */
  disconnect() {
    if (this.echoInstance) {
      this.echoInstance.disconnect();
      this.echoInstance = null;
      this.currentToken = null;
    }
  }
}
