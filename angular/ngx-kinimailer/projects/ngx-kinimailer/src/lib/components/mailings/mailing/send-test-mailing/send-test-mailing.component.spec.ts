import { ComponentFixture, TestBed } from '@angular/core/testing';

import { SendTestMailingComponent } from './send-test-mailing.component';

describe('SendTestMailingComponent', () => {
  let component: SendTestMailingComponent;
  let fixture: ComponentFixture<SendTestMailingComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ SendTestMailingComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(SendTestMailingComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
