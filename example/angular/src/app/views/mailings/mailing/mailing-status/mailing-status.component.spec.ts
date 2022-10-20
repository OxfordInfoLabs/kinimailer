import { ComponentFixture, TestBed } from '@angular/core/testing';

import { MailingStatusComponent } from './mailing-status.component';

describe('MailingStatusComponent', () => {
  let component: MailingStatusComponent;
  let fixture: ComponentFixture<MailingStatusComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ MailingStatusComponent ]
    })
    .compileComponents();

    fixture = TestBed.createComponent(MailingStatusComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
