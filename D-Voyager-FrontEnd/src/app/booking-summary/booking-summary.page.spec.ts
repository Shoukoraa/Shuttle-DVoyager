import { ComponentFixture, TestBed } from '@angular/core/testing';
import { BookingSummaryPage } from './booking-summary.page';

describe('BookingSummaryPage', () => {
  let component: BookingSummaryPage;
  let fixture: ComponentFixture<BookingSummaryPage>;

  beforeEach(() => {
    fixture = TestBed.createComponent(BookingSummaryPage);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
