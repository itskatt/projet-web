import { ComponentFixture, TestBed } from '@angular/core/testing';

import { PreviousCartComponent } from './previous-cart.component';

describe('PreviousCartComponent', () => {
  let component: PreviousCartComponent;
  let fixture: ComponentFixture<PreviousCartComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ PreviousCartComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(PreviousCartComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
