import { ComponentFixture, TestBed } from '@angular/core/testing';

import { NewMailingComponent } from './new-mailing.component';

describe('NewMailingComponent', () => {
  let component: NewMailingComponent;
  let fixture: ComponentFixture<NewMailingComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ NewMailingComponent ]
    })
    .compileComponents();
  });

  beforeEach(() => {
    fixture = TestBed.createComponent(NewMailingComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
