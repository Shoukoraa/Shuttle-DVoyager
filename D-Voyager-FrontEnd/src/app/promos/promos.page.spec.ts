import { ComponentFixture, TestBed } from '@angular/core/testing';
import { PromosPage } from './promos.page';

describe('PromosPage', () => {
  let component: PromosPage;
  let fixture: ComponentFixture<PromosPage>;

  beforeEach(() => {
    fixture = TestBed.createComponent(PromosPage);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
