import { Component, NgZone, OnInit } from '@angular/core';
import { NavigationStart, Router } from '@angular/router';
import { filter } from 'rxjs/operators';
import { ModalController, Platform, ToastController } from '@ionic/angular';
import { App } from '@capacitor/app';
import { StatusBar, Style } from '@capacitor/status-bar';


@Component({
  selector: 'app-root',
  templateUrl: 'app.component.html',
  styleUrls: ['app.component.scss'],
  standalone: false,
})
export class AppComponent implements OnInit {
  showSplash = true;
  splashDismissing = false;
  private lastBackPress = 0;
  private timePeriodToExit = 2000;
  private splashTimeout: any;

  constructor(
    private router: Router,
    private platform: Platform,
    private toastController: ToastController,
    private zone: NgZone,
    private modalController: ModalController
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

  ngOnInit() {
    this.initializeSplashScreen();
  }

  async initializeSplashScreen() {
    // Dismiss custom HTML splash screen after animation completes (2.8 seconds)
    this.splashTimeout = setTimeout(() => {
      this.dismissSplash();
    }, 2800);
  }

  async dismissSplash() {
    if (this.showSplash) {
      this.splashDismissing = true;
      if (this.splashTimeout) {
        clearTimeout(this.splashTimeout);
      }
      
      // Wait for the 500ms CSS fade-out transition to finish before removing from DOM
      setTimeout(() => {
        this.showSplash = false;
      }, 500);
    }
  }

  initializeApp() {
    this.platform.ready().then(() => {
      this.setupBackButtonBehavior();
      this.setupDeepLinks();
      this.setupStatusBar();
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
    this.platform.backButton.subscribeWithPriority(9999, async () => {
      const topModal = await this.modalController.getTop();
      if (topModal) {
        await topModal.dismiss();
        return;
      }

      const currentUrl = this.router.url.split('?')[0];
      
      // Halaman yang dianggap "Root" atau halaman awal di mana tombol back harus memicu konfirmasi keluar
      const rootUrls = [
        '/home',
        '/driver-home',
        '/login',
        '/'
      ];

      // Jika berada di salah satu halaman utama/root
      if (rootUrls.includes(currentUrl)) {
        const now = Date.now();
        if (now - this.lastBackPress < this.timePeriodToExit) {
          App.exitApp();
        } else {
          this.lastBackPress = now;
          await this.showExitToast();
        }
      } else {
        // Jika sedang di sub-halaman (seperti edit-profil), jangan keluar aplikasi, tapi balik ke halaman sebelumnya
        window.history.back();
      }
    });
  }

  private async showExitToast() {
    const toast = await this.toastController.create({
      message: 'Tekan sekali lagi untuk keluar aplikasi',
      duration: 2000,
      position: 'top',
      cssClass: 'premium-toast toast-warning',
      icon: 'warning'
    });
    await toast.present();
  }

  private async setupStatusBar() {
    if (this.platform.is('cordova') || this.platform.is('capacitor')) {
      try {
        // Set overlay to true for a more modern immersive look
        // This requires safe-area-inset-top padding in CSS (already added)
        await StatusBar.setOverlaysWebView({ overlay: true });
        await StatusBar.setBackgroundColor({ color: 'transparent' });
        await StatusBar.setStyle({ style: Style.Light });
      } catch (err) {
        console.warn('Failed to configure StatusBar:', err);
      }
    }
  }
}
