import { Injectable } from '@angular/core';
import { CanActivate, Router, UrlTree } from '@angular/router';

@Injectable({
  providedIn: 'root'
})
export class WelcomeGuard implements CanActivate {
  constructor(private router: Router) {}

  canActivate(): boolean | UrlTree {
    const hasSeenWelcome = localStorage.getItem('has_seen_welcome');
    
    if (!hasSeenWelcome) {
      return this.router.parseUrl('/welcome');
    }
    return true;
  }
}
