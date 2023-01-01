import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ArticlesGridComponent } from './articles-grid.component';

describe('ArticlesGridComponent', () => {
  let component: ArticlesGridComponent;
  let fixture: ComponentFixture<ArticlesGridComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ ArticlesGridComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(ArticlesGridComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
