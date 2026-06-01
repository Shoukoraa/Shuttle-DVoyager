import { Component, NgZone } from '@angular/core';
import { NavigationStart, Router } from '@angular/router';
import { filter } from 'rxjs/operators';
import { Platform, ToastController } from '@ionic/angular';
import { App } from '@capacitor/app';

@Component({
  selector: 'app-root',
  templateUrl: 'app.component.html',
  styleUrls: ['app.component.scss'],
  standalone: false,
})
export class AppComponent {
  private lastBackPress = 0;
  private timePeriodToExit = 2000;

  constructor(
    private router: Router,
    private platform: Platform,
    private toastController: ToastController,
    private zone: NgZone
  ) {
    this.router.events
      .pipe(filter((event) => event instanceof NavigationStart))
      .subscribe(() => {
        const activeEl = document.activeElement as HTMLElement | null;
        if (activeEl && typeof activeEl.blur === 'function') {
          activeEl.blur();
        }
      });

    this.initializeApp();
  }

  initializeApp() {
    this.platform.ready().then(() => {
      this.setupBackButtonBehavior();
      this.setupDeepLinks();
    });
  }

  setupDeepLinks() {
    App.addListener('appUrlOpen', (event: any) => {
      const url = event.url;
      // Menangkap callback dari Google Auth (Custom Scheme)
      if (url && url.includes('shuttle://auth/callback')) {
        
        // Tutup in-app browser
        import('@capacitor/browser').then(({ Browser }) => {
          Browser.close();
        }).catch(err => console.log(err));

        // Ekstrak token dan data dari URL query parameters
        const queryParamsStr = url.split('?')[1];
        if (queryParamsStr) {
          const searchParams = new URLSearchParams(queryParamsStr);
          const oauth = searchParams.get('oauth');
          const token = searchParams.get('token');
          const role = searchParams.get('role');
          const oauthError = searchParams.get('oauth_error');

          // Arahkan ke halaman login (Angular Router butuh NgZone untuk update view)
          this.zone.run(() => {
            this.router.navigate(['/login'], {
              queryParams: { oauth, token, role, oauth_error: oauthError }
            });
          });
        }
      }
    });
  }

  setupBackButtonBehavior() {
    this.platform.backButton.subscribeWithPriority(10, async () => {
      const currentUrl = this.router.url.split('?')[0];
      const rootUrls = [
        '/home',
        '/tickets',
        '/promos',
        '/profile',
        '/driver-home',
        '/driver-history',
        '/driver-chat',
        '/driver-profile',
        '/login'
      ];

      if (rootUrls.includes(currentUrl)) {
        const now = Date.now();
        if (now - this.lastBackPress < this.timePeriodToExit) {
          try {
            await App.exitApp();
          } catch (err) {
            console.error('Error exiting app:', err);
          }
        } else {
          this.lastBackPress = now;
          await this.showExitToast();
        }
      } else {
        window.history.back();
      }
    });
  }

  private async showExitToast() {
    const toast = await this.toastController.create({
      message: 'Tekan tombol kembali sekali lagi untuk keluar aplikasi.',
      duration: 2000,
      position: 'bottom',
      color: 'dark'
    });
    await toast.present();
  }
}
