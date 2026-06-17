import { Component, OnInit } from '@angular/core';
import { ApiService } from '../services/api.service';

@Component({
  selector: 'app-driver-reviews',
  templateUrl: './driver-reviews.page.html',
  styleUrls: ['./driver-reviews.page.scss'],
  standalone: false
})
export class DriverReviewsPage implements OnInit {
  public reviews: any[] = [];
  public isLoadingReviews: boolean = false;
  public rating: number = 0;
  public reviewCount: number = 0;

  constructor(private apiService: ApiService) { }

  ngOnInit() {
    this.loadDriverReviews();
  }

  loadDriverReviews() {
    this.isLoadingReviews = true;

    this.apiService.getDriverReviews().subscribe({
      next: (response) => {
        this.reviews = response?.reviews || [];
        this.rating = typeof response?.average_rating === 'number' ? response.average_rating : this.rating;
        this.reviewCount = typeof response?.review_count === 'number' ? response.review_count : this.reviewCount;
        this.isLoadingReviews = false;
      },
      error: (err) => {
        console.error('Failed to load driver reviews:', err);
        this.isLoadingReviews = false;
      }
    });
  }

  getReviewRoute(review: any): string {
    const origin = review?.booking?.schedule?.route?.origin?.name || 'Asal';
    const destination = review?.booking?.schedule?.route?.destination?.name || 'Tujuan';
    return `${origin} -> ${destination}`;
  }
}
