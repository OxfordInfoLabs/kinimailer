import { ComponentFixture, TestBed } from '@angular/core/testing';

import { MailingProfilesComponent } from './mailing-profiles.component';

describe('MailingProfilesComponent', () => {
  let component: MailingProfilesComponent;
  let fixture: ComponentFixture<MailingProfilesComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ MailingProfilesComponent ]
    })
    .compileComponents();
  });

  beforeEach(() => {
    fixture = TestBed.createComponent(MailingProfilesComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
