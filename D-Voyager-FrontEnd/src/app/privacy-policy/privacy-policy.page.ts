import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';

@Component({
  selector: 'app-privacy-policy',
  templateUrl: './privacy-policy.page.html',
  styleUrls: ['./privacy-policy.page.scss'],
  standalone: false,
})
export class PrivacyPolicyPage implements OnInit {
  isAgreed = false;
  hasScrolledToBottom = false;

  constructor(private router: Router) {}

  ngOnInit() {}

  toggleAgreement() {
    if (!this.hasScrolledToBottom) return;
    this.isAgreed = !this.isAgreed;
  }

  onScroll(event: any) {
    const element = event.target;
    // Check if scrolled to bottom with 15px threshold
    const threshold = 15;
    if (element.scrollHeight - element.scrollTop <= element.clientHeight + threshold) {
      this.hasScrolledToBottom = true;
    }
  }

  agreeAndContinue() {
    if (!this.isAgreed || !this.hasScrolledToBottom) return;

    localStorage.setItem('has_seen_welcome', 'true');
    this.router.navigate(['/home'], { replaceUrl: true });
  }
}
