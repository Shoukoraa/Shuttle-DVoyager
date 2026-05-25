import { ComponentFixture, TestBed } from '@angular/core/testing';
import { SelectSeatPage } from './select-seat.page';

describe('SelectSeatPage', () => {
  let component: SelectSeatPage;
  let fixture: ComponentFixture<SelectSeatPage>;

  beforeEach(() => {
    fixture = TestBed.createComponent(SelectSeatPage);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
