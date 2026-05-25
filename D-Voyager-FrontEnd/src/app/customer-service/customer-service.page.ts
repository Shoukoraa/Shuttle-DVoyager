import { Component, OnInit } from '@angular/core';
import { ApiService } from '../services/api.service';

@Component({
  selector: 'app-customer-service',
  templateUrl: './customer-service.page.html',
  styleUrls: ['./customer-service.page.scss'],
  standalone: false
})
export class CustomerServicePage implements OnInit {
  userName = 'User';

  constructor(private apiService: ApiService) { }

  ngOnInit() {
    this.loadCachedUserName();
    this.refreshUserName();
  }

  private loadCachedUserName() {
    const userDataRaw = localStorage.getItem('user_data');

    if (!userDataRaw) {
      return;
    }

    try {
      const userData = JSON.parse(userDataRaw);
      this.userName = userData?.name || userData?.full_name || this.userName;
    } catch (error) {
      console.error('Error parsing localStorage user_data:', error);
    }
  }

  private refreshUserName() {
    const token = localStorage.getItem('auth_token');

    if (!token) {
      return;
    }

    this.apiService.getUserProfile().subscribe({
      next: (user) => {
        const currentUserRaw = localStorage.getItem('user_data');
        const currentUser = currentUserRaw ? JSON.parse(currentUserRaw) : {};
        const mergedUser = {
          ...currentUser,
          ...user,
          role: currentUser.role || localStorage.getItem('user_role') || 'customer',
        };

        this.userName = mergedUser?.name || mergedUser?.full_name || this.userName;
        localStorage.setItem('user_data', JSON.stringify(mergedUser));
      },
      error: (error) => {
        console.error('Failed to refresh customer service user name:', error);
      }
    });
  }

}
