import { Injectable } from '@angular/core';
import { CanActivate, ActivatedRouteSnapshot, RouterStateSnapshot, Router } from '@angular/router';

@Injectable({
  providedIn: 'root'
})
export class RoleGuard implements CanActivate {
  constructor(private router: Router) {}

  canActivate(
    route: ActivatedRouteSnapshot,
    state: RouterStateSnapshot
  ): boolean {
    const userDataRaw = localStorage.getItem('user_data');
    const userRole = localStorage.getItem('user_role');

    if (!userDataRaw || !userRole) {
      this.router.navigate(['/login']);
      return false;
    }

    try {
      const userData = JSON.parse(userDataRaw);
      const role = userData.role || userRole;

      // Cek apakah route memerlukan role spesifik
      const requiredRole = route.data['role'] as string | undefined;
      
      if (requiredRole && role !== requiredRole) {
        // Role tidak cocok — redirect ke dashboard yang sesuai
        if (role === 'driver') {
          this.router.navigate(['/driver-home']);
        } else if (role === 'customer') {
          this.router.navigate(['/home']);
        } else {
          this.router.navigate(['/login']);
        }
        return false;
      }

      return true;
    } catch (e) {
      console.error('Error parsing user_data:', e);
      this.router.navigate(['/login']);
      return false;
    }
  }
}
