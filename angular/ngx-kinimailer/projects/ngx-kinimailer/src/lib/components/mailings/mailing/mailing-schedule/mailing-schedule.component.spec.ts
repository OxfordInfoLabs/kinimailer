import { ComponentFixture, TestBed } from '@angular/core/testing';

import { MailingScheduleComponent } from './mailing-schedule.component';

describe('MailingScheduleComponent', () => {
  let component: MailingScheduleComponent;
  let fixture: ComponentFixture<MailingScheduleComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ MailingScheduleComponent ]
    })
    .compileComponents();
  });

  beforeEach(() => {
    fixture = TestBed.createComponent(MailingScheduleComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
