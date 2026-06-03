import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';

@Component({
  selector: 'app-welcome',
  templateUrl: './welcome.page.html',
  styleUrls: ['./welcome.page.scss'],
  standalone: false,
})
export class WelcomePage implements OnInit {
  currentSlide = 0;

  slides = [
    {
      title: 'Pemesanan Shuttle Mudah & Cepat',
      description: 'Dapatkan tiket perjalanan Anda dalam hitungan detik. Pilih rute, kursi ternyaman, dan bayar dengan berbagai metode pembayaran aman.',
    },
    {
      title: 'Pelacakan Posisi Shuttle Real-Time',
      description: 'Jangan khawatir ketinggalan shuttle lagi. Pantau langsung lokasi armada dan estimasi kedatangan secara langsung dari genggaman Anda.',
    },
    {
      title: 'Layanan Terbaik & Nyaman',
      description: 'Perjalanan aman dengan armada modern pilihan yang terawat berkala, serta dikemudikan oleh pengemudi profesional bersertifikat.',
    }
  ];

  isAnimating = false;
  isAnimatingPrev = false;

  constructor(private router: Router) {}

  ngOnInit() {
    const hasSeenWelcome = localStorage.getItem('has_seen_welcome');
    if (hasSeenWelcome) {
      this.router.navigate(['/home']);
    }
  }

  nextSlide() {
    if (this.isAnimating) return;
    this.isAnimating = true;

    setTimeout(() => {
      this.isAnimating = false;
      if (this.currentSlide < this.slides.length - 1) {
        this.currentSlide++;
      } else {
        this.finish();
      }
    }, 350); // Matches the bounce animation duration slightly
  }

  prevSlide() {
    if (this.isAnimatingPrev) return;
    this.isAnimatingPrev = true;

    setTimeout(() => {
      this.isAnimatingPrev = false;
      if (this.currentSlide > 0) {
        this.currentSlide--;
      }
    }, 350);
  }

  goToSlide(index: number) {
    this.currentSlide = index;
  }

  skip() {
    this.router.navigate(['/privacy-policy']);
  }

  finish() {
    this.router.navigate(['/privacy-policy']);
  }
}
