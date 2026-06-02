import { Component, OnInit } from '@angular/core';
import { NavigationStart, Router } from '@angular/router';
import { filter } from 'rxjs/operators';
import { Platform, ToastController } from '@ionic/angular';
import { App } from '@capacitor/app';
import { SplashScreen } from '@capacitor/splash-screen';

@Component({
  selector: 'app-root',
  templateUrl: 'app.component.html',
  styleUrls: ['app.component.scss'],
  standalone: false,
})
export class AppComponent implements OnInit {
  showSplash = true;
  private lastBackPress = 0;
  private timePeriodToExit = 2000;

  constructor(
    private router: Router,
    private platform: Platform,
    private toastController: ToastController
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
    try {
      await SplashScreen.hide();
    } catch (err) {
      console.warn('Native SplashScreen plugin is not available or failed to hide:', err);
    }
    setTimeout(() => {
      this.showSplash = false;
    }, 5000);
  }

  initializeApp() {
    this.platform.ready().then(() => {
      this.setupBackButtonBehavior();
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
